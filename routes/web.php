<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ViolationLogController;
use App\Http\Controllers\PublicSiswaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\KelasController;
use Illuminate\Support\Facades\Route;

// Show landing page on root route
Route::get('/', function () {
    return view('welcome');
});

// Public Student/Parent point checking
Route::get('/cek-poin', [PublicSiswaController::class, 'showCheckForm'])->name('siswa.check');
Route::post('/cek-poin', [PublicSiswaController::class, 'check'])->name('siswa.check.post');

// Public student contact self-completion
Route::get('/lengkapi-data', [PublicSiswaController::class, 'showLengkapiDataForm'])->name('siswa.lengkapi_data');
Route::post('/lengkapi-data', [PublicSiswaController::class, 'submitLengkapiData'])->name('siswa.lengkapi_data.post');

// Guest routes (Auth)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Students management (Authorization checked in controllers)
    Route::get('/siswa', [StudentController::class, 'index'])->name('students');
    Route::post('/siswa', [StudentController::class, 'store'])->name('students.store');
    Route::get('/siswa/template', [StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::post('/siswa/import', [StudentController::class, 'importExcel'])->name('students.import');
    Route::get('/siswa/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/siswa/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/siswa/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/siswa/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/siswa/bulk', [StudentController::class, 'bulkAction'])->name('students.bulk');

    // Violation logging (Authorization checked in controllers)
    Route::get('/pelanggaran/catat', [ViolationLogController::class, 'create'])->name('violations.create');
    Route::post('/pelanggaran/catat', [ViolationLogController::class, 'store'])->name('violations.store');

    // Violation management (Super Admin Only)
    Route::get('/pelanggaran', [\App\Http\Controllers\ViolationController::class, 'index'])->name('violations.index');
    Route::post('/pelanggaran', [\App\Http\Controllers\ViolationController::class, 'store'])->name('violations.store_type');
    Route::get('/pelanggaran/{violation}/edit', [\App\Http\Controllers\ViolationController::class, 'edit'])->name('violations.edit');
    Route::put('/pelanggaran/{violation}', [\App\Http\Controllers\ViolationController::class, 'update'])->name('violations.update');
    Route::delete('/pelanggaran/{violation}', [\App\Http\Controllers\ViolationController::class, 'destroy'])->name('violations.destroy');

    // User management (Authorization checked in controllers)
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/remap', [UserController::class, 'remap'])->name('users.remap');

    // Kelas management (Super Admin Only)
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // WhatsApp Gateway control
    Route::post('/whatsapp/start', [DashboardController::class, 'startGateway'])->name('whatsapp.start');
    Route::post('/whatsapp/logout', [DashboardController::class, 'logoutGateway'])->name('whatsapp.logout');
    Route::post('/whatsapp/stop', [DashboardController::class, 'stopGateway'])->name('whatsapp.stop');

    // Reports & Printing
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export_excel');
    Route::get('/siswa/{student}/cetak-kartu', [ReportController::class, 'printStudentCard'])->name('reports.print_card');
    Route::get('/siswa/{student}/cetak-sp', [ReportController::class, 'printStudentSp'])->name('reports.print_sp');
});
