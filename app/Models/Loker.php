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
        'id_pendidikan_terakhir',
        'id_jenjang',
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

    public function pendidikanTerakhir()
    {
        return $this->belongsTo(PendidikanTerakhir::class, 'id_pendidikan_terakhir');
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'id_jenjang');
    }

    public function scopeDibuka($query)
    {
        return $query->where('status_loker', 'dibuka');
    }
}
