<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TesTulis extends Model
{
    protected $table = 'tes_tulis';

    protected $primaryKey = 'id_tes_tulis';

    protected $fillable = [
        'id_pelamar',
        'nilai_tes_agama_umum',
        'nilai_tes_bidang_studi',
        'nilai_tes_inggris_umum',
        'tanggal_pelaksanaan',
    ];

    protected function casts(): array
    {
        return [
            'nilai_tes_agama_umum' => 'decimal:2',
            'nilai_tes_bidang_studi' => 'decimal:2',
            'nilai_tes_inggris_umum' => 'decimal:2',
            'tanggal_pelaksanaan' => 'datetime',
        ];
    }

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'id_pelamar');
    }

    /** Weighted average: agama umum 20%, inggris umum 30%, bidang studi 50%. */
    public function nilaiRataRata(): ?float
    {
        $bobot = [
            'nilai_tes_agama_umum' => 0.2,
            'nilai_tes_inggris_umum' => 0.3,
            'nilai_tes_bidang_studi' => 0.5,
        ];

        $totalBobot = 0;
        $totalNilai = 0;

        foreach ($bobot as $field => $persentase) {
            if ($this->$field !== null) {
                $totalNilai += ((float) $this->$field) * $persentase;
                $totalBobot += $persentase;
            }
        }

        return $totalBobot > 0 ? round($totalNilai / $totalBobot, 2) : null;
    }
}
