<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogNotifikasi extends Model
{
    protected $table = 'log_notifikasi';

    protected $primaryKey = 'id_log';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_pelamar',
        'channel',
        'template',
        'pesan',
        'status_kirim',
        'created_by',
    ];

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class, 'id_pelamar');
    }
}
