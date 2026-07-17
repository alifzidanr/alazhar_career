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

    public function nilaiRataRata(): ?float
    {
        $nilai = array_filter(
            [$this->nilai_tes_agama_umum, $this->nilai_tes_bidang_studi, $this->nilai_tes_inggris_umum],
            fn ($n) => $n !== null
        );

        return count($nilai) ? round(((float) array_sum($nilai)) / count($nilai), 2) : null;
    }
}
