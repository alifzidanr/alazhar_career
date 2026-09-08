<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrisStatusNikah extends Model
{
    protected $table = 'hris_status_nikah';

    protected $primaryKey = 'id_status_nikah';

    public $timestamps = false;

    protected $fillable = ['status_nikah'];
}
