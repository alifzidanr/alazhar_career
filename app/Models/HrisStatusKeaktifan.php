<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrisStatusKeaktifan extends Model
{
    protected $table = 'hris_status_keaktifan';

    protected $primaryKey = 'id_status_keaktifan';

    public $timestamps = false;

    protected $fillable = ['status_keaktifan'];
}
