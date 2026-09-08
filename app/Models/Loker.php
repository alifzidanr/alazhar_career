<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loker extends Model
{
    protected $table = 'loker';

    protected $primaryKey = 'id_loker';

    protected $fillable = [
        'judul_loker',
        'deskripsi_loker',
        'wilayah',
        'id_pendidikan_terakhir',
        'id_jenjang',
        'status_loker',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function kriteria()
    {
        return $this->hasMany(KriteriaLoker::class, 'id_loker');
    }

    public function pelamar()
    {
        return $this->hasMany(Pelamar::class, 'id_loker');
    }

    public function pendidikanTerakhir()
    {
        return $this->belongsTo(PendidikanTerakhir::class, 'id_pendidikan_terakhir');
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'id_jenjang');
    }

    /** Open lokers whose Berlaku Sampai (end_time) date, if set, hasn't passed yet. */
    public function scopeDibuka($query)
    {
        return $query->where('status_loker', 'dibuka')
            ->where(function ($q) {
                $q->whereNull('end_time')->orWhere('end_time', '>=', now()->startOfDay());
            });
    }

    /** True once the Berlaku Sampai (end_time) date has passed. Null end_time never expires. */
    public function isExpired(): bool
    {
        return $this->end_time !== null && now()->startOfDay()->gt($this->end_time->copy()->startOfDay());
    }

    /** Effective open state: manually opened AND not past its Berlaku Sampai date. */
    public function isBuka(): bool
    {
        return $this->status_loker === 'dibuka' && ! $this->isExpired();
    }
}
