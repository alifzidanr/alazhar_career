<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posisi extends Model
{
    protected $table = 'posisi';

    protected $primaryKey = 'id_posisi';

    public $timestamps = false;

    protected $fillable = ['nama_posisi'];

    public function kriteriaLoker()
    {
        return $this->hasMany(KriteriaLoker::class, 'id_posisi');
    }
}
