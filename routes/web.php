<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\RawMaterialController;
use App\Http\Controllers\Admin\PurchaseController;
Route::get('/', function () {
    return view('welcome');
});

// LOGIN
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// DASHBOARDS (wajib login + role)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/admin/users', [CashierController::class, 'index'])->name('admin.cashiers.index');
    Route::get('/admin/users/create', [CashierController::class, 'create'])->name('admin.cashiers.create');
    Route::post('/admin/users', [CashierController::class, 'store'])->name('admin.cashiers.store');

    Route::get('/admin/raw-materials', [RawMaterialController::class, 'index'])->name('admin.raw_materials.index');
    Route::get('/admin/raw-materials/create', [RawMaterialController::class, 'create'])->name('admin.raw_materials.create');
    Route::post('/admin/raw-materials', [RawMaterialController::class, 'store'])->name('admin.raw_materials.store');
    Route::get('/admin/purchases', [PurchaseController::class, 'index'])->name('admin.purchases.index');
    Route::get('/admin/purchases/create', [PurchaseController::class, 'create'])->name('admin.purchases.create');
    Route::post('/admin/purchases', [PurchaseController::class, 'store'])->name('admin.purchases.store');
});

Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir/dashboard', [DashboardController::class, 'kasir'])->name('kasir.dashboard');
});
