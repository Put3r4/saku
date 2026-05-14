<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CriterionController;


/*
|--------------------------------------------------------------------------
| Rute Publik
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'index'])->name('login');
    Route::post('/masuk', [AuthController::class, 'authenticate'])->name('login.process');
});

Route::post('/keluar', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Rute Administrator
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('criteria', CriterionController::class);
});

/*
|--------------------------------------------------------------------------
| Rute Mahasiswa (Sistem SAW & Budgeting)
|--------------------------------------------------------------------------
*/

Route::prefix('mahasiswa')->name('student.')->group(function () {
    Route::get('/dashboard', function () {
        return "Area Mahasiswa: Form Input Anggaran & Hasil Rekomendasi";
    })->name('dashboard');

});


