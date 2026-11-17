<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.debug' => false]);
    }

    public function test_missing_page_shows_not_found_page(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('<h1>Halaman yang Anda Cari Tidak Ditemukan</h1>', false)
            ->assertSee('<title>Halaman yang Anda Cari Tidak Ditemukan', false)
            ->assertSee('lowongan terkait sudah ditutup')
            ->assertDontSee('Terjadi Kendala Teknis')
            ->assertSee('Kode galat: 404')
            ->assertDontSee('<!--#', false);
    }

    public function test_server_error_shows_technical_error_page(): void
    {
        Route::get('/__uji-galat-server', fn () => throw new \RuntimeException('uji'));

        $this->get('/__uji-galat-server')
            ->assertStatus(500)
            ->assertSee('Terjadi Kendala Teknis')
            ->assertSee('Kode galat: 500')
            ->assertDontSee('uji');
    }

    public function test_expired_form_shows_technical_error_page(): void
    {
        // CSRF checks are skipped while running tests, so raise the 419 directly.
        Route::post('/__uji-csrf', fn () => abort(419));

        $this->post('/__uji-csrf')
            ->assertStatus(419)
            ->assertSee('Terjadi Kendala Teknis')
            ->assertSee('Kode galat: 419');
    }
}
