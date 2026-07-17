<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Orientasi extends Model
{
    protected $table = 'orientasi';

    protected $primaryKey = 'id_orientasi';

    protected $fillable = [
        'id_pelamar',
        'id_unit_kerja',
        'uang_makan',
        'uang_transport',
        'tanggal_mulai',
        'tanggal_selesai',
        'sk_orientasi_upload',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'id_pelamar');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'id_unit_kerja');
    }

    public function skOrientasiUrl(): ?string
    {
        return $this->sk_orientasi_upload ? Storage::disk('public')->url($this->sk_orientasi_upload) : null;
    }
}
