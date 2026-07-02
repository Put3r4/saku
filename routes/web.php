<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CriterionController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MatrixController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\HistoryController;


/*
|--------------------------------------------------------------------------
| Rute Publik
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang-saw', [HomeController::class, 'tentangSaw'])->name('tentang-saw');
Route::get('/daftar-menu', [HomeController::class, 'daftarMenu'])->name('daftar-menu');

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'index'])->name('login');
    Route::post('/masuk', [AuthController::class, 'authenticate'])->name('login.process');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if ($user && $user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('student.dashboard');
})->middleware('auth')->name('dashboard');

Route::post('/keluar', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Rute Administrator
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('criteria', CriterionController::class)->except(['show']);
    Route::resource('menu', MenuController::class)->except(['show']);
    
    // Matrix input routes
    Route::get('matrix', [MatrixController::class, 'index'])->name('matrix.index');
    Route::post('matrix', [MatrixController::class, 'update'])->name('matrix.update');
});

/*
|--------------------------------------------------------------------------
| Rute Mahasiswa (Sistem SAW & Budgeting)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::post('/recommend', [StudentDashboardController::class, 'recommend'])->name('recommend');
    Route::post('/pilih-menu', [StudentDashboardController::class, 'selectMenu'])->name('select-menu');
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('history.index');
});

require __DIR__.'/settings.php';


