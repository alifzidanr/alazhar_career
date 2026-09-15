<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Live read of simpeg-proto's own t_ref_regional table (via the `hris` DB
 * connection — same MySQL server, see config/database.php), instead of a
 * local snapshot like the other Hris* reference models. That table has
 * 1000+ rows and keeps growing, so a snapshot would go stale quickly; a
 * live cross-database read keeps the Migrasi Data form's Regional dropdown
 * exactly in sync with HRIS's own Tambah Pegawai form.
 */
class HrisRegional extends Model
{
    protected $connection = 'hris';

    protected $table = 't_ref_regional';

    protected $primaryKey = 'id_regional';

    public $timestamps = false;
}
