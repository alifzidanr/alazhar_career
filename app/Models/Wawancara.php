<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wawancara extends Model
{
    protected $table = 'wawancara';

    protected $primaryKey = 'id_wawancara';

    protected $fillable = [
        'id_pelamar',
        'nilai_wawancara_agama',
        'rekomendasi_wawancara_agama',
        'nilai_praktik_micro_teaching',
        'rekomendasi_praktik_micro_teaching',
        'nilai_wawancara_umum',
        'rekomendasi_wawancara_umum',
        'tanggal_pelaksanaan',
    ];

    protected function casts(): array
    {
        return [
            'nilai_wawancara_agama' => 'decimal:2',
            'nilai_praktik_micro_teaching' => 'decimal:2',
            'nilai_wawancara_umum' => 'decimal:2',
            'tanggal_pelaksanaan' => 'datetime',
        ];
    }

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'id_pelamar');
    }

    public function nilaiRataRataWawancara(): ?float
    {
        $nilai = array_filter(
            [$this->nilai_wawancara_agama, $this->nilai_praktik_micro_teaching, $this->nilai_wawancara_umum],
            fn ($n) => $n !== null
        );

        return count($nilai) ? round(((float) array_sum($nilai)) / count($nilai), 2) : null;
    }
}
