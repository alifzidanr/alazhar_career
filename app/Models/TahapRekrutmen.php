<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahapRekrutmen extends Model
{
    // id values seeded in DB, in pipeline order
    public const SELEKSI_BERKAS = 1;

    public const TES_TULIS = 2;

    public const WAWANCARA = 3;

    public const ORIENTASI = 4;

    public const TUGAS_SEMENTARA = 5;

    public const TES_KESEHATAN = 6;

    public const TERIMA_SK = 7;

    public const MIGRASI_DATA = 8;

    protected $table = 'tahap_rekrutmen';

    protected $primaryKey = 'id_tahap_rekrutmen';

    public $timestamps = false;

    protected $fillable = ['tahap_rekrutmen'];

    public function pelamar()
    {
        return $this->hasMany(Pelamar::class, 'id_tahap_rekrutmen');
    }

    /**
     * The status a pelamar is given by default upon entering a tahap.
     */
    public static function statusAwalUntuk(int $idTahap): int
    {
        return match ($idTahap) {
            self::SELEKSI_BERKAS => StatusPelamar::SCREENING,
            default => StatusPelamar::ONGOING,
        };
    }
}
