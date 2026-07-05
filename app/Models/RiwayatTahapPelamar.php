<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatTahapPelamar extends Model
{
    protected $table = 'riwayat_tahap_pelamar';

    protected $primaryKey = 'id_riwayat';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_pelamar',
        'id_tahap_rekrutmen',
        'id_status_pelamar',
        'catatan',
        'created_by',
    ];

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'id_pelamar');
    }

    public function tahapRekrutmen()
    {
        return $this->belongsTo(TahapRekrutmen::class, 'id_tahap_rekrutmen');
    }

    public function statusPelamar()
    {
        return $this->belongsTo(StatusPelamar::class, 'id_status_pelamar');
    }
}
