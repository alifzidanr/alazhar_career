<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TugasSementara extends Model
{
    protected $table = 'tugas_sementara';

    protected $primaryKey = 'id_tugas_sementara';

    protected $fillable = [
        'id_pelamar',
        'sk_tugas_sementara_upload',
        'hasil_tes_kesehatan_upload',
    ];

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'id_pelamar');
    }

    public function skTugasSementaraUrl(): ?string
    {
        return $this->sk_tugas_sementara_upload ? Storage::disk('public')->url($this->sk_tugas_sementara_upload) : null;
    }

    public function hasilTesKesehatanUrl(): ?string
    {
        return $this->hasil_tes_kesehatan_upload ? Storage::disk('public')->url($this->hasil_tes_kesehatan_upload) : null;
    }
}
