<?php

namespace Tests\Feature;

use App\Models\Loker;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LinkPreviewMetaTest extends TestCase
{
    /**
     * The app's migrations are MySQL-only, so build just the tables the
     * public pages read in the in-memory sqlite database.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('jenjang', function (Blueprint $table) {
            $table->increments('id_jenjang');
            $table->integer('urutan')->default(0);
            $table->string('nama_jenjang');
            $table->unsignedInteger('id_pendidikan_minimum')->nullable();
        });

        Schema::create('pendidikan_terakhir', function (Blueprint $table) {
            $table->increments('id_pendidikan_terakhir');
            $table->string('pendidikan_terakhir');
        });

        Schema::create('tahap_rekrutmen', function (Blueprint $table) {
            $table->increments('id_tahap_rekrutmen');
            $table->string('tahap_rekrutmen');
        });

        Schema::create('unit_kerja', function (Blueprint $table) {
            $table->increments('id_unit_kerja');
            $table->string('nama_unit');
        });

        Schema::create('kriteria', function (Blueprint $table) {
            $table->increments('id_kriteria');
            $table->string('teks_kriteria');
        });

        Schema::create('loker', function (Blueprint $table) {
            $table->increments('id_loker');
            $table->string('judul_loker');
            $table->text('deskripsi_loker')->nullable();
            $table->string('wilayah')->nullable();
            $table->unsignedInteger('id_pendidikan_terakhir')->nullable();
            $table->unsignedInteger('id_jenjang')->nullable();
            $table->string('status_loker')->default('dibuka');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->timestamps();
        });

        Schema::create('kriteria_loker', function (Blueprint $table) {
            $table->increments('id_kriteria_loker');
            $table->unsignedInteger('id_loker');
            $table->unsignedInteger('id_posisi')->nullable();
            $table->unsignedInteger('id_unit_kerja')->nullable();
            $table->unsignedInteger('id_kriteria')->nullable();
            $table->integer('bobot')->default(0);
            $table->string('keterangan')->nullable();
        });

        DB::table('jenjang')->insert(['id_jenjang' => 1, 'urutan' => 1, 'nama_jenjang' => 'Guru SD']);
    }

    private function openLoker(): Loker
    {
        return Loker::create([
            'judul_loker' => 'Guru Matematika (SD)',
            'deskripsi_loker' => 'Mengajar matematika untuk siswa SD.',
            'wilayah' => 'DKI Jakarta',
            'id_jenjang' => 1,
            'status_loker' => 'dibuka',
            'start_time' => now()->subDay(),
            'end_time' => now()->addDays(14)->startOfDay(),
        ]);
    }

    public function test_landing_page_has_link_preview_tags(): void
    {
        $this->openLoker();

        $this->get('/')
            ->assertOk()
            ->assertSee('<meta property="og:title" content="Rekrutmen YPI Al Azhar">', false)
            ->assertSee('<meta property="og:description" content="Bergabunglah bersama Yayasan Pesantren Islam Al-Azhar. 1 Lowongan kerja tersedia, lamar sekarang!">', false)
            ->assertSee('<meta property="og:image" content="'.asset('images/og/og-default.jpg').'?v=3">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
    }

    public function test_lowongan_list_has_link_preview_tags(): void
    {
        $this->openLoker();

        $this->get('/lowongan')
            ->assertOk()
            ->assertSee('<meta property="og:title" content="Lowongan Kerja - Rekrutmen YPI Al Azhar">', false)
            ->assertSee('<meta property="og:description" content="1 lowongan tersedia, lamar sekarang!">', false)
            ->assertSee('<meta property="og:image" content="'.asset('images/og/og-lowongan.jpg').'?v=3">', false)
            ->assertSee('<meta property="og:url" content="'.url('/lowongan').'">', false);
    }

    public function test_lowongan_detail_preview_describes_the_job(): void
    {
        $loker = $this->openLoker();
        $deadline = $loker->end_time->translatedFormat('d F Y');

        $this->get(route('loker.show', $loker))
            ->assertOk()
            ->assertSee('<meta property="og:title" content="Guru Matematika (SD) - Rekrutmen YPI Al Azhar">', false)
            ->assertSee('<meta property="og:description" content="Formulir pendaftaran Guru Matematika (SD) YPI Al-Azhar wilayah DKI Jakarta, berlaku sampai '.$deadline.', lamar sekarang!">', false)
            ->assertSee('<meta property="og:url" content="'.route('loker.show', $loker).'">', false);
    }
}
