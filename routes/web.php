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
use App\Http\Controllers\Kasir\SaleController;
use App\Http\Controllers\Admin\SaleController as AdminSaleController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\Kitchen\DashboardController as KitchenDashboardController;
use App\Http\Controllers\Admin\DiningTableController;
use App\Models\Product;
use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboardController;
use App\Http\Controllers\Pegawai\FaceController;
use App\Http\Controllers\Pegawai\AttendanceController;
use App\Http\Controllers\Payment\MidtransWebhookController;


Route::get('/landingtrial', [PublicMenuController::class, 'landingTrial'])->name('public.landingtrial');
Route::get('/', [PublicMenuController::class, 'landingTrial'])->name('public.home');
Route::get('/menu', [PublicMenuController::class, 'index'])->name('public.menu');
Route::get('/t/{token}', [PublicMenuController::class, 'byTableToken'])
    ->name('public.table.token');
Route::get('/order/overview', [PublicMenuController::class, 'overview'])->name('public.order.overview');
Route::post('/order/checkout', [PublicMenuController::class, 'checkout'])
    ->name('public.order.checkout');
Route::get('/order/invoice/{invoice}', [PublicMenuController::class, 'invoice'])
    ->name('public.order.invoice');

Route::get('/order/invoice/{invoice}/status', [PublicMenuController::class, 'invoiceStatus'])
    ->name('public.order.invoice.status');
// LOGIN
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/payments/midtrans/notification', function () {
    return response()->json([
        'ok' => true,
        'message' => 'Midtrans notification endpoint is alive',
    ]);
});
Route::post('/payments/midtrans/notification', [MidtransWebhookController::class, 'handle'])
    ->name('payments.midtrans.notification')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

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
    Route::get('/admin/purchases/{purchase}', [PurchaseController::class, 'show'])->name('admin.purchases.show');
    Route::get('/admin/purchases/{purchase}/export/pdf', [\App\Http\Controllers\Admin\PurchaseController::class, 'exportPdf'])
        ->name('admin.purchases.export.pdf');
    Route::get('/admin/purchases/export/pdf', [\App\Http\Controllers\Admin\PurchaseController::class, 'exportPdfBulk'])
        ->name('admin.purchases.export.pdf.bulk');
    // Export PDF gabungan (selected / by range / by period)
    Route::post('/admin/purchases/export-pdf', [\App\Http\Controllers\Admin\PurchaseController::class, 'exportPdf'])
        ->name('admin.purchases.exportPdf');


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

    Route::get('/sales', [AdminSaleController::class, 'index'])->name('admin.sales.index');
    Route::get('/sales/{sale}', [AdminSaleController::class, 'show'])->name('admin.sales.show');

    // Meja (Dine In)
    Route::get('/admin/tables', [DiningTableController::class, 'index'])->name('admin.tables.index');
    Route::get('/admin/tables/create', [DiningTableController::class, 'create'])->name('admin.tables.create');
    Route::post('/admin/tables', [DiningTableController::class, 'store'])->name('admin.tables.store');
    Route::get('/admin/tables/{table}/edit', [DiningTableController::class, 'edit'])->name('admin.tables.edit');
    Route::put('/admin/tables/{table}', [DiningTableController::class, 'update'])->name('admin.tables.update');
    Route::delete('/admin/tables/{table}', [DiningTableController::class, 'destroy'])->name('admin.tables.destroy');
    Route::post('/admin/tables/{table}/regenerate-qr', [DiningTableController::class, 'regenerateQr'])
        ->name('admin.tables.regenerateQr');

});

Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/dashboard', [KasirDashboardController::class, 'index'])->name('dashboard');

    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
    Route::get('/sales/{sale}/print-thermal', [SaleController::class, 'printThermal'])
    ->name('sales.print-thermal');

    Route::get('/ready', [KasirDashboardController::class, 'readyIndex'])->name('ready.index');
    Route::get('/ready-orders', [KasirDashboardController::class, 'readyOrders'])->name('ready.orders');
    Route::post('/ready-orders/{sale}/deliver', [KasirDashboardController::class, 'deliver'])->name('ready.deliver');
    Route::get('/sales/{sale}/payment-status', [SaleController::class, 'paymentStatus'])
        ->name('sales.payment-status');
    Route::post('/sales/find-pending-cash', [SaleController::class, 'findPendingCashOrder'])
        ->name('sales.find-pending-cash');

    Route::post('/sales/confirm-pending-cash', [SaleController::class, 'confirmPendingCashOrder'])
        ->name('sales.confirm-pending-cash');
});



Route::middleware(['auth', 'role:kitchen'])
    ->prefix('kitchen')
    ->name('kitchen.')
    ->group(function () {
        Route::get('/', [KitchenController::class, 'index'])->name('dashboard');
        Route::get('/history', [KitchenController::class, 'history'])->name('history');

        Route::get('/orders', [KitchenController::class, 'orders'])->name('orders'); // json polling
        Route::post('/orders/{sale}/process', [KitchenController::class, 'process'])->name('orders.process');
        Route::post('/orders/{sale}/done', [KitchenController::class, 'done'])->name('orders.done');
        Route::post('/items/{saleItem}/cook', [KitchenController::class, 'cookItem'])->name('items.cook');
        Route::post('/items/{saleItem}/uncook', [KitchenController::class, 'uncookItem'])->name('items.uncook');
    });

Route::prefix('pegawai')->name('pegawai.')->middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('dashboard');

    // Face enrollment
    Route::get('/face/enroll', [FaceController::class, 'showEnroll'])->name('face.enroll');
    Route::post('/face/enroll', [FaceController::class, 'storeEnroll'])->name('face.enroll.store');

    // Attendance
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/absensi/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/absensi/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
});