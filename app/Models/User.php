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

    protected $table = 'admin_kepegawaian';

    protected $primaryKey = 'id_admin';

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
