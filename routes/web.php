<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\RawMaterialController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\WasteController;
use App\Http\Controllers\Admin\StockOpnameController;
use App\Http\Controllers\Admin\InventoryMovementController;
use App\Http\Controllers\Admin\ProductController;
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


    Route::get('/admin/wastes', [WasteController::class, 'index'])->name('admin.wastes.index');
    Route::get('/admin/wastes/create', [WasteController::class, 'create'])->name('admin.wastes.create');
    Route::post('/admin/wastes', [WasteController::class, 'store'])->name('admin.wastes.store');

    Route::get('/admin/opnames', [StockOpnameController::class, 'index'])->name('admin.opnames.index');
    Route::get('/admin/opnames/create', [StockOpnameController::class, 'create'])->name('admin.opnames.create');
    Route::post('/admin/opnames', [StockOpnameController::class, 'store'])->name('admin.opnames.store');
    Route::get('/admin/opnames/{id}', [StockOpnameController::class, 'show'])->name('admin.opnames.show');
    Route::post('/admin/opnames/{id}/post', [StockOpnameController::class, 'post'])->name('admin.opnames.post');
    Route::get('/admin/inventory-movements', [InventoryMovementController::class, 'index'])
        ->name('admin.inventory-movements.index');

    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

    // Resep (BOM)
    Route::get('/products/{product}/recipes', [ProductController::class, 'recipes'])->name('admin.products.recipes');
    Route::post('/products/{product}/recipes', [ProductController::class, 'recipesStore'])->name('admin.products.recipes.store');
    Route::delete('/products/{product}/recipes/{recipeId}', [ProductController::class, 'recipesDestroy'])->name('admin.products.recipes.destroy');
});

Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir/dashboard', [DashboardController::class, 'kasir'])->name('kasir.dashboard');
});
