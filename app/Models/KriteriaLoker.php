<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KriteriaLoker extends Model
{
    public const WAJIB = 'wajib';

    public const DIUTAMAKAN = 'diutamakan';

    public const NILAI_TAMBAH = 'nilai_tambah';

    protected $table = 'kriteria_loker';

    protected $primaryKey = 'id_kriteria_loker';

    public $timestamps = false;

    protected $fillable = [
        'id_loker',
        'id_posisi',
        'id_unit_kerja',
        'id_kriteria',
        'bobot',
        'keterangan',
    ];

    public function loker()
    {
        return $this->belongsTo(Loker::class, 'id_loker');
    }

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'id_posisi');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'id_unit_kerja');
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria');
    }

    /** Display text: prefer the managed kriteria list, fall back to legacy free-text. */
    public function teksKriteria(): ?string
    {
        return $this->kriteria?->teks_kriteria ?? $this->keterangan;
    }
}
