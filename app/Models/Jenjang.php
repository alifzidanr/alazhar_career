<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jenjang extends Model
{
    protected $table = 'jenjang';

    protected $primaryKey = 'id_jenjang';

    public $timestamps = false;

    protected $fillable = ['nama_jenjang', 'id_pendidikan_minimum'];

    public function loker()
    {
        return $this->hasMany(Loker::class, 'id_jenjang');
    }

    public function pendidikanMinimum()
    {
        return $this->belongsTo(PendidikanTerakhir::class, 'id_pendidikan_minimum');
    }
}
