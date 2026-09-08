<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrisStatusKepegawaian extends Model
{
    protected $table = 'hris_status_kepegawaian';

    protected $primaryKey = 'id_status_kepegawaian';

    public $timestamps = false;

    protected $fillable = ['kode', 'status'];
}
