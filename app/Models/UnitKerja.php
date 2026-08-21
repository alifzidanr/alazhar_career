<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    protected $table = 'unit_kerja';

    protected $primaryKey = 'id_unit_kerja';

    public $timestamps = false;

    protected $fillable = ['nama_unit'];

    public function kriteriaLoker()
    {
        return $this->hasMany(KriteriaLoker::class, 'id_unit_kerja');
    }

    public function orientasi()
    {
        return $this->hasMany(Orientasi::class, 'id_unit_kerja');
    }
}
