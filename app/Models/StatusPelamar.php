<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPelamar extends Model
{
    // id values seeded in DB: 1 lolos, 2 tidak lolos, 3 dicadangkan, 4 ditolak, 5 mundur, 6 screening,
    // 7 ongoing, 8 diterima, 9 migrated
    public const LOLOS = 1;

    public const TIDAK_LOLOS = 2;

    public const DICADANGKAN = 3;

    public const DITOLAK = 4;

    public const MUNDUR = 5;

    public const SCREENING = 6;

    public const ONGOING = 7;

    public const DITERIMA = 8;

    public const MIGRATED = 9;

    protected $table = 'status_pelamar';

    protected $primaryKey = 'id_status_pelamar';

    public $timestamps = false;

    protected $fillable = ['status_pelamar'];

    public function pelamar()
    {
        return $this->hasMany(Pelamar::class, 'id_status_pelamar');
    }
}
