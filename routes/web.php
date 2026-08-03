<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\SmartController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BackupController;

// Public Redirect Route
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Panel (Protected)
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'auto_logout']], function () {
    
    // Dashboard
    Route::get('/', function() { return redirect()->route('admin.dashboard'); });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/activity', [DashboardController::class, 'activity'])->name('admin.activity');

    // CRUD Kriteria
    Route::resource('kriteria', KriteriaController::class)->names([
        'index' => 'admin.kriteria.index',
        'store' => 'admin.kriteria.store',
        'update' => 'admin.kriteria.update',
        'destroy' => 'admin.kriteria.destroy'
    ])->only(['index', 'store', 'update', 'destroy']);

    // CRUD Supplier (mapped from Alternatif model)
    Route::get('/supplier/report', [AlternatifController::class, 'report'])->name('admin.supplier.report');
    Route::resource('supplier', AlternatifController::class)->names([
        'index' => 'admin.supplier.index',
        'store' => 'admin.supplier.store',
        'update' => 'admin.supplier.update',
        'destroy' => 'admin.supplier.destroy'
    ])->only(['index', 'store', 'update', 'destroy']);

    // CRUD Bobot (we will manage rating/bobot normalisation under this section)
    Route::get('/bobot', [KriteriaController::class, 'index'])->name('admin.bobot.index');

    // Penilaian Matrix
    Route::get('/penilaian/report', [PenilaianController::class, 'report'])->name('admin.penilaian.report');
    Route::get('/penilaian', [PenilaianController::class, 'index'])->name('admin.penilaian.index');
    Route::post('/penilaian', [PenilaianController::class, 'storeOrUpdate'])->name('admin.penilaian.store');

    // Proses SMART Calculations
    Route::get('/smart', [SmartController::class, 'index'])->name('admin.smart.index');
    Route::post('/smart/process', [SmartController::class, 'process'])->name('admin.smart.process');
    Route::get('/supplier/{id}/detail', [SmartController::class, 'detail'])->name('admin.supplier.detail');
    Route::get('/smart/report', [SmartController::class, 'report'])->name('admin.smart.report');
    Route::get('/smart/excel', [SmartController::class, 'exportExcel'])->name('admin.smart.excel');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('admin.history.index');
    Route::delete('/history/{id}', [HistoryController::class, 'destroy'])->name('admin.history.destroy');
    Route::post('/history/clear', [HistoryController::class, 'clear'])->name('admin.history.clear');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings/profile', [SettingController::class, 'updateProfile'])->name('admin.settings.profile');
    Route::post('/settings/password', [SettingController::class, 'updatePassword'])->name('admin.settings.password');
    Route::post('/settings/website', [SettingController::class, 'updateWebsite'])->name('admin.settings.website');

    // Backup & Restore
    Route::get('/backup', [BackupController::class, 'index'])->name('admin.backup.index');
    Route::get('/backup/download', [BackupController::class, 'backup'])->name('admin.backup.download');
    Route::post('/backup/restore', [BackupController::class, 'restore'])->name('admin.backup.restore');
    Route::post('/backup/reset', [BackupController::class, 'reset'])->name('admin.backup.reset');
});
