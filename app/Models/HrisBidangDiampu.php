<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrisBidangDiampu extends Model
{
    protected $table = 'hris_bidang_diampu';

    protected $primaryKey = 'id_bidang_diampu';

    public $timestamps = false;

    protected $fillable = ['nama_bidang'];
}
