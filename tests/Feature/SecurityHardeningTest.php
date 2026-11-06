<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    /**
     * The app's migrations are MySQL-only (ALTER TABLE ... AUTO_INCREMENT),
     * so build just the admin table in the in-memory sqlite database.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('admin_kepegawaian', function (Blueprint $table) {
            $table->increments('id_admin');
            $table->string('nama', 150);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    private function admin(string $email, string $password = 'rahasia-asli-123'): User
    {
        return User::create([
            'nama' => $email,
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function test_rotating_x_forwarded_for_does_not_reset_the_login_limit(): void
    {
        $this->admin('owner@example.test');
        $this->admin('admin@example.test');

        for ($i = 1; $i <= 5; $i++) {
            $this->withHeader('X-Forwarded-For', "10.0.0.{$i}")
                ->post('/login', ['email' => 'admin@example.test', 'password' => 'salah-'.$i]);
        }

        $this->withHeader('X-Forwarded-For', '10.9.9.9')
            ->post('/login', ['email' => 'admin@example.test', 'password' => 'rahasia-asli-123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_one_admin_account_locks_after_twenty_failures_from_any_ip(): void
    {
        $this->admin('owner@example.test');
        $this->admin('admin@example.test');

        for ($i = 1; $i <= 20; $i++) {
            $this->withHeader('CF-Connecting-IP', "203.0.113.{$i}")
                ->post('/login', ['email' => 'admin@example.test', 'password' => 'salah-'.$i]);
        }

        $this->withHeader('CF-Connecting-IP', '198.51.100.20')
            ->post('/login', ['email' => 'admin@example.test', 'password' => 'rahasia-asli-123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_different_cloudflare_visitors_are_limited_separately(): void
    {
        $this->admin('owner@example.test');
        $this->admin('admin@example.test');

        for ($i = 1; $i <= 5; $i++) {
            $this->withHeader('CF-Connecting-IP', '203.0.113.7')
                ->post('/login', ['email' => 'admin@example.test', 'password' => 'salah-'.$i]);
        }

        $this->withHeader('CF-Connecting-IP', '198.51.100.20')
            ->post('/login', ['email' => 'admin@example.test', 'password' => 'rahasia-asli-123'])
            ->assertSessionHasNoErrors();

        $this->assertAuthenticated();
    }

    public function test_other_admins_cannot_change_the_owner_accounts_password_or_email(): void
    {
        $owner = $this->admin('owner@example.test');
        $admin = $this->admin('admin@example.test');
        $this->assertTrue($owner->isProtected());

        $this->actingAs($admin)
            ->patch("/admin/users/{$owner->id_admin}/password", [
                'password' => 'diambil-alih-123',
                'password_confirmation' => 'diambil-alih-123',
            ])
            ->assertNotFound();

        $this->actingAs($admin)
            ->patch("/admin/users/{$owner->id_admin}", [
                'nama' => 'x',
                'email' => 'attacker@example.test',
            ])
            ->assertNotFound();

        $owner->refresh();
        $this->assertSame('owner@example.test', $owner->email);
        $this->assertTrue(Hash::check('rahasia-asli-123', $owner->password));
    }

    public function test_owner_can_still_change_own_password(): void
    {
        $owner = $this->admin('owner@example.test');

        $this->actingAs($owner)
            ->patch("/admin/users/{$owner->id_admin}/password", [
                'password' => 'baru-sekali-123',
                'password_confirmation' => 'baru-sekali-123',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('baru-sekali-123', $owner->refresh()->password));
    }

    public function test_security_headers_are_sent(): void
    {
        $this->get('/login')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
