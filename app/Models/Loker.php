<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loker extends Model
{
    protected $table = 'loker';

    protected $primaryKey = 'id_loker';

    protected $fillable = [
        'judul_loker',
        'deskripsi_loker',
        'lokasi',
        'status_loker',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function kriteria()
    {
        return $this->hasMany(KriteriaLoker::class, 'id_loker');
    }

    public function pelamar()
    {
        return $this->hasMany(Pelamar::class, 'id_loker');
    }

    public function scopeDibuka($query)
    {
        return $query->where('status_loker', 'dibuka');
    }
}
