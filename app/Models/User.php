<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nama', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use Notifiable;

    /** Owner/developer account: hidden from other admins' user list and cannot be deleted. */
    public const PROTECTED_ADMIN_ID = 1;

    protected $table = 'admin_kepegawaian';

    protected $primaryKey = 'id_admin';

    public function isProtected(): bool
    {
        return $this->id_admin === self::PROTECTED_ADMIN_ID;
    }

    /**
     * Admin display name is stored in `nama`, but Breeze's default views
     * reference `name` — expose it as an accessor for compatibility.
     */
    public function getNameAttribute(): string
    {
        return $this->attributes['nama'];
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
