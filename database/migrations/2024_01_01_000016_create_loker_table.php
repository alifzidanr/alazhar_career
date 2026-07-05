<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loker', function (Blueprint $table) {
            $table->increments('id_loker');
            $table->string('judul_loker', 150);
            $table->text('deskripsi_loker')->nullable();
            $table->string('lokasi', 150)->nullable();
            $table->enum('status_loker', ['dibuka', 'ditutup'])->default('dibuka');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loker');
    }
};
