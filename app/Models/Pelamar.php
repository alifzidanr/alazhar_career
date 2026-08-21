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
        'nik',
        'tanggal_lahir',
        'jenis_kelamin',
        'gelar',
        'no_hp',
        'email',
        'alamat',
        'pernah_rekrutmen_sebelumnya',
        'bulan_rekrutmen_sebelumnya',
        'tahun_rekrutmen_sebelumnya',
        'id_tahap_rekrutmen_sebelumnya',
        'pernah_bekerja_di_al_azhar',
        'lokasi_kerja_al_azhar_sebelumnya',
        'bulan_kerja_al_azhar_sebelumnya',
        'tahun_kerja_al_azhar_sebelumnya',
        'jenis_kepegawaian_al_azhar_sebelumnya',
        'jenis_kepegawaian_al_azhar_lainnya',
        'id_pendidikan_terakhir',
        'institusi',
        'institusi_s1',
        'institusi_s2',
        'institusi_s3',
        'program_studi',
        'program_studi_s1',
        'program_studi_s2',
        'program_studi_s3',
        'kategori_perguruan_tinggi_d3',
        'kategori_perguruan_tinggi_s1',
        'kategori_perguruan_tinggi_s2',
        'kategori_perguruan_tinggi_s3',
        'akreditasi',
        'tahun_lulus',
        'ipk_s1',
        'ipk_s2',
        'ipk_s3',
        'ipk_d3',
        'cv_upload',
        'pas_foto_upload',
        'surat_lamaran_upload',
        'id_status_pelamar',
        'id_tahap_rekrutmen',
        'tanggal_apply',
        'ijazah_upload',
        'ktp_upload',
        'sim_upload',
        'transkrip_nilai_upload',
        'transkrip_nilai_s1_upload',
        'transkrip_nilai_s2_upload',
        'transkrip_nilai_s3_upload',
        'sertifikat_gada_pratama_upload',
        'sertifikat_tambahan_upload',
        'bersedia_ditempatkan',
        'id_pelamar_cadangan_dari',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_apply' => 'date',
            'tanggal_lahir' => 'date',
            'ipk_s1' => 'decimal:2',
            'ipk_s2' => 'decimal:2',
            'ipk_s3' => 'decimal:2',
            'ipk_d3' => 'decimal:2',
            'bersedia_ditempatkan' => 'boolean',
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

    public function tahapRekrutmenSebelumnya()
    {
        return $this->belongsTo(TahapRekrutmen::class, 'id_tahap_rekrutmen_sebelumnya');
    }

    public function riwayat()
    {
        return $this->hasMany(RiwayatTahapPelamar::class, 'id_pelamar')->latest('created_at');
    }

    public function logNotifikasi()
    {
        return $this->hasMany(LogNotifikasi::class, 'id_pelamar')->latest('created_at');
    }

    public function tesTulis()
    {
        return $this->hasOne(TesTulis::class, 'id_pelamar');
    }

    public function wawancara()
    {
        return $this->hasOne(Wawancara::class, 'id_pelamar');
    }

    public function orientasi()
    {
        return $this->hasOne(Orientasi::class, 'id_pelamar');
    }

    public function tugasSementara()
    {
        return $this->hasOne(TugasSementara::class, 'id_pelamar');
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

    /** Final score: tes tulis average 60%, wawancara average 40%. */
    public function nilaiAkhir(): ?float
    {
        $komponen = [
            [$this->tesTulis?->nilaiRataRata(), 0.6],
            [$this->wawancara?->nilaiRataRataWawancara(), 0.4],
        ];

        $totalBobot = 0;
        $totalNilai = 0;

        foreach ($komponen as [$nilai, $bobot]) {
            if ($nilai !== null) {
                $totalNilai += $nilai * $bobot;
                $totalBobot += $bobot;
            }
        }

        return $totalBobot > 0 ? round($totalNilai / $totalBobot, 2) : null;
    }

    public function namaLengkap(): string
    {
        return trim($this->nama.($this->gelar ? ', '.$this->gelar : ''));
    }

    public function usia(): ?int
    {
        return $this->tanggal_lahir?->age;
    }

    public function bulanRekrutmenSebelumnyaLabel(): ?string
    {
        if (! $this->bulan_rekrutmen_sebelumnya) {
            return null;
        }

        return \Carbon\Carbon::create(null, (int) $this->bulan_rekrutmen_sebelumnya, 1)->translatedFormat('F');
    }

    public function bulanKerjaAlAzharSebelumnyaLabel(): ?string
    {
        if (! $this->bulan_kerja_al_azhar_sebelumnya) {
            return null;
        }

        return \Carbon\Carbon::create(null, (int) $this->bulan_kerja_al_azhar_sebelumnya, 1)->translatedFormat('F');
    }

    public function jenisKepegawaianAlAzharLabel(): ?string
    {
        if ($this->jenis_kepegawaian_al_azhar_sebelumnya === 'Lain-lain') {
            return $this->jenis_kepegawaian_al_azhar_lainnya ?: 'Lain-lain';
        }

        return $this->jenis_kepegawaian_al_azhar_sebelumnya;
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

    public function transkripS1Url(): ?string
    {
        return $this->transkrip_nilai_s1_upload ? Storage::disk('public')->url($this->transkrip_nilai_s1_upload) : null;
    }

    public function transkripS2Url(): ?string
    {
        return $this->transkrip_nilai_s2_upload ? Storage::disk('public')->url($this->transkrip_nilai_s2_upload) : null;
    }

    public function transkripS3Url(): ?string
    {
        return $this->transkrip_nilai_s3_upload ? Storage::disk('public')->url($this->transkrip_nilai_s3_upload) : null;
    }

    public function cvUrl(): ?string
    {
        return $this->cv_upload ? Storage::disk('public')->url($this->cv_upload) : null;
    }

    public function pasFotoUrl(): ?string
    {
        return $this->pas_foto_upload ? Storage::disk('public')->url($this->pas_foto_upload) : null;
    }

    public function suratLamaranUrl(): ?string
    {
        return $this->surat_lamaran_upload ? Storage::disk('public')->url($this->surat_lamaran_upload) : null;
    }

    public function simUrl(): ?string
    {
        return $this->sim_upload ? Storage::disk('public')->url($this->sim_upload) : null;
    }

    public function sertifikatGadaPratamaUrl(): ?string
    {
        return $this->sertifikat_gada_pratama_upload ? Storage::disk('public')->url($this->sertifikat_gada_pratama_upload) : null;
    }

    public function sertifikatTambahanUrl(): ?string
    {
        return $this->sertifikat_tambahan_upload ? Storage::disk('public')->url($this->sertifikat_tambahan_upload) : null;
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
