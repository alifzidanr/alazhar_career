<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MigrasiDataPegawai extends Model
{
    protected $table = 'migrasi_data_pegawai';

    protected $primaryKey = 'id_migrasi';

    protected $fillable = [
        'id_pelamar',
        'nip',
        'nomor_faceid',
        'nama_lengkap',
        'tgl_masuk',
        'id_status_kepegawaian',
        'id_status_keaktifan',
        'id_bidang_diampu',
        'tgl_lahir',
        'jenis_kelamin',
        'id_status_nikah',
        'id_regional',
        'gelar_depan',
        'gelar_belakang',
        'no_ktp',
        'npwp',
        'no_rekening',
        'tempat_lahir',
        'golongan_darah',
        'alamat',
        'usia_purnabakti',
        'kota',
        'propinsi',
        'kode_pos',
        'no_telepon',
        'no_hp',
        'email',
        'ayah_kandung',
        'ibu_kandung',
    ];

    protected function casts(): array
    {
        return [
            'tgl_masuk' => 'date',
            'tgl_lahir' => 'date',
        ];
    }

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'id_pelamar');
    }

    public function statusKepegawaian()
    {
        return $this->belongsTo(HrisStatusKepegawaian::class, 'id_status_kepegawaian');
    }

    public function statusKeaktifan()
    {
        return $this->belongsTo(HrisStatusKeaktifan::class, 'id_status_keaktifan');
    }

    public function bidangDiampu()
    {
        return $this->belongsTo(HrisBidangDiampu::class, 'id_bidang_diampu');
    }

    public function statusNikah()
    {
        return $this->belongsTo(HrisStatusNikah::class, 'id_status_nikah');
    }
}
