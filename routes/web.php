<?php

use App\Http\Controllers\Admin\JenjangController as AdminJenjangController;
use App\Http\Controllers\Admin\KriteriaController as AdminKriteriaController;
use App\Http\Controllers\Admin\KriteriaLokerController;
use App\Http\Controllers\Admin\LokasiController as AdminLokasiController;
use App\Http\Controllers\Admin\LokerController as AdminLokerController;
use App\Http\Controllers\Admin\NotifikasiController;
use App\Http\Controllers\Admin\PelamarController as AdminPelamarController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\LamaranController;
use App\Http\Controllers\Public\LokerController;
use App\Http\Controllers\Public\StatusLamaranController;
use Illuminate\Support\Facades\Route;

// Public: landing, job listing + apply
Route::get('/', [LokerController::class, 'index'])->name('loker.index');
Route::get('/lowongan', [LokerController::class, 'list'])->name('loker.list');
Route::get('/loker/{loker}', [LokerController::class, 'show'])->name('loker.show');
Route::post('/loker/{loker}/lamar', [LamaranController::class, 'store'])->name('loker.lamar');

Route::get('/status-lamaran', [StatusLamaranController::class, 'index'])->name('status.index');
Route::post('/status-lamaran', [StatusLamaranController::class, 'search'])->name('status.search');

Route::view('/tentang-kami', 'public.tentang')->name('tentang.index');

// Admin
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/pelamar');

    Route::get('/pelamar', [AdminPelamarController::class, 'index'])->name('pelamar.index');
    Route::get('/pelamar/{pelamar}', [AdminPelamarController::class, 'show'])->name('pelamar.show');
    Route::patch('/pelamar/{pelamar}/status', [AdminPelamarController::class, 'updateStatus'])->name('pelamar.status');
    Route::patch('/pelamar/{pelamar}/lanjut', [AdminPelamarController::class, 'advanceTahap'])->name('pelamar.lanjut');
    Route::patch('/pelamar/{pelamar}/mundur', [AdminPelamarController::class, 'regressTahap'])->name('pelamar.mundur');
    Route::patch('/pelamar/{pelamar}/catatan', [AdminPelamarController::class, 'updateCatatan'])->name('pelamar.catatan');
    Route::patch('/pelamar/{pelamar}/tes-tulis', [AdminPelamarController::class, 'updateTesTulis'])->name('pelamar.tes-tulis');
    Route::patch('/pelamar/{pelamar}/wawancara', [AdminPelamarController::class, 'updateWawancara'])->name('pelamar.wawancara');
    Route::patch('/pelamar/{pelamar}/orientasi', [AdminPelamarController::class, 'updateOrientasi'])->name('pelamar.orientasi');
    Route::patch('/pelamar/{pelamar}/tugas-sementara', [AdminPelamarController::class, 'updateTugasSementara'])->name('pelamar.tugas-sementara');

    Route::post('/pelamar/{pelamar}/notify', [NotifikasiController::class, 'send'])->name('pelamar.notify');

    Route::resource('/loker', AdminLokerController::class)->except(['show']);
    Route::post('/loker/{loker}/kriteria', [KriteriaLokerController::class, 'store'])->name('loker.kriteria.store');
    Route::delete('/loker/{loker}/kriteria/{kriteria}', [KriteriaLokerController::class, 'destroy'])->name('loker.kriteria.destroy');

    Route::get('/kriteria', [AdminKriteriaController::class, 'index'])->name('kriteria.index');
    Route::post('/kriteria', [AdminKriteriaController::class, 'store'])->name('kriteria.store');
    Route::patch('/kriteria/{kriteria}', [AdminKriteriaController::class, 'update'])->name('kriteria.update');
    Route::delete('/kriteria/{kriteria}', [AdminKriteriaController::class, 'destroy'])->name('kriteria.destroy');

    Route::get('/jenjang', [AdminJenjangController::class, 'index'])->name('jenjang.index');
    Route::post('/jenjang', [AdminJenjangController::class, 'store'])->name('jenjang.store');
    Route::patch('/jenjang/{jenjang}', [AdminJenjangController::class, 'update'])->name('jenjang.update');
    Route::delete('/jenjang/{jenjang}', [AdminJenjangController::class, 'destroy'])->name('jenjang.destroy');

    Route::get('/lokasi', [AdminLokasiController::class, 'index'])->name('lokasi.index');
    Route::post('/lokasi', [AdminLokasiController::class, 'store'])->name('lokasi.store');
    Route::patch('/lokasi/{lokasi}', [AdminLokasiController::class, 'update'])->name('lokasi.update');
    Route::delete('/lokasi/{lokasi}', [AdminLokasiController::class, 'destroy'])->name('lokasi.destroy');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/password', [AdminUserController::class, 'updatePassword'])->name('users.password');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
