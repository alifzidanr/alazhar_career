<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendidikanTerakhir extends Model
{
    protected $table = 'pendidikan_terakhir';

    protected $primaryKey = 'id_pendidikan_terakhir';

    public $timestamps = false;

    protected $fillable = ['pendidikan_terakhir'];

    public function pelamar()
    {
        return $this->hasMany(Pelamar::class, 'id_pendidikan_terakhir');
    }
}
