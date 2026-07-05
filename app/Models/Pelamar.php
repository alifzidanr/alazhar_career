<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pelamar extends Model
{
    protected $table = 'pelamar';

    protected $primaryKey = 'id_pelamar';

    protected $fillable = [
        'id_loker',
        'nama',
        'tanggal_lahir',
        'jenis_kelamin',
        'gelar',
        'no_hp',
        'email',
        'alamat',
        'id_pendidikan_terakhir',
        'institusi',
        'program_studi',
        'akreditasi',
        'tahun_lulus',
        'ipk',
        'cv_upload',
        'id_status_pelamar',
        'id_tahap_rekrutmen',
        'tanggal_apply',
        'ijazah_upload',
        'ktp_upload',
        'transkrip_nilai_upload',
        'id_pelamar_cadangan_dari',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_apply' => 'date',
            'tanggal_lahir' => 'date',
            'ipk' => 'decimal:2',
        ];
    }

    public function loker()
    {
        return $this->belongsTo(Loker::class, 'id_loker');
    }

    public function pendidikanTerakhir()
    {
        return $this->belongsTo(PendidikanTerakhir::class, 'id_pendidikan_terakhir');
    }

    public function statusPelamar()
    {
        return $this->belongsTo(StatusPelamar::class, 'id_status_pelamar');
    }

    public function tahapRekrutmen()
    {
        return $this->belongsTo(TahapRekrutmen::class, 'id_tahap_rekrutmen');
    }

    public function riwayat()
    {
        return $this->hasMany(RiwayatTahapPelamar::class, 'id_pelamar')->latest('created_at');
    }

    public function logNotifikasi()
    {
        return $this->hasMany(LogNotifikasi::class, 'id_pelamar')->latest('created_at');
    }

    /** The primary candidate this pelamar is a backup for, if any. */
    public function cadanganDari()
    {
        return $this->belongsTo(Pelamar::class, 'id_pelamar_cadangan_dari');
    }

    /** Backup candidates designated for this pelamar (set during Wawancara stage). */
    public function kandidatCadangan()
    {
        return $this->hasMany(Pelamar::class, 'id_pelamar_cadangan_dari');
    }

    public function namaLengkap(): string
    {
        return trim($this->nama.($this->gelar ? ', '.$this->gelar : ''));
    }

    public function ijazahUrl(): ?string
    {
        return $this->ijazah_upload ? Storage::disk('public')->url($this->ijazah_upload) : null;
    }

    public function ktpUrl(): ?string
    {
        return $this->ktp_upload ? Storage::disk('public')->url($this->ktp_upload) : null;
    }

    public function transkripUrl(): ?string
    {
        return $this->transkrip_nilai_upload ? Storage::disk('public')->url($this->transkrip_nilai_upload) : null;
    }

    public function cvUrl(): ?string
    {
        return $this->cv_upload ? Storage::disk('public')->url($this->cv_upload) : null;
    }

    /** No. HP normalized to a wa.me-compatible international format (62xxx, no symbols). */
    public function whatsappNumber(): ?string
    {
        if (! $this->no_hp) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $this->no_hp);

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '62')) {
            $digits = '62'.$digits;
        }

        return $digits;
    }
}
