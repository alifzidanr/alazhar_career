<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $this->sk_tugas_sementara_upload
            ? route('admin.pelamar.berkas', ['pelamar' => $this->id_pelamar, 'column' => 'sk_tugas_sementara_upload'])
            : null;
    }

    public function hasilTesKesehatanUrl(): ?string
    {
        return $this->hasil_tes_kesehatan_upload
            ? route('admin.pelamar.berkas', ['pelamar' => $this->id_pelamar, 'column' => 'hasil_tes_kesehatan_upload'])
            : null;
    }
}
