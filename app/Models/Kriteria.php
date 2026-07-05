<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = 'kriteria';

    protected $primaryKey = 'id_kriteria';

    public $timestamps = false;

    protected $fillable = ['teks_kriteria'];

    public function kriteriaLoker()
    {
        return $this->hasMany(KriteriaLoker::class, 'id_kriteria');
    }
}
