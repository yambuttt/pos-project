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
use App\Http\Controllers\Admin\AttendanceQrController;
use App\Http\Controllers\Admin\EmployeeDeviceController;
use App\Http\Controllers\Pegawai\AttendanceV2Controller;
use App\Http\Controllers\Admin\AttendanceHistoryController;
use App\Http\Controllers\Admin\AttendancePhotoController;
use App\Http\Controllers\Pegawai\AttendanceHistoryController as PegawaiAttendanceHistoryController;
use App\Http\Controllers\Pegawai\AttendancePhotoController as PegawaiAttendancePhotoController;


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
    Route::get('/admin/raw-materials', [RawMaterialController::class, 'index'])->name('admin.raw_materials.index');
    Route::get('/admin/raw-materials/create', [RawMaterialController::class, 'create'])->name('admin.raw_materials.create');
    Route::post('/admin/raw-materials', [RawMaterialController::class, 'store'])->name('admin.raw_materials.store');
    Route::get('/admin/raw-materials/{rawMaterial}/edit', [RawMaterialController::class, 'edit'])->name('admin.raw_materials.edit');
    Route::put('/admin/raw-materials/{rawMaterial}', [RawMaterialController::class, 'update'])->name('admin.raw_materials.update');
    Route::delete('/admin/raw-materials/{rawMaterial}', [RawMaterialController::class, 'destroy'])->name('admin.raw_materials.destroy');

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

    Route::get('/admin/attendance/qr', [AttendanceQrController::class, 'index'])->name('admin.attendance.qr');
    Route::post('/admin/attendance/qr/regenerate', [AttendanceQrController::class, 'regenerate'])->name('admin.attendance.qr.regenerate');

    Route::get('/admin/attendance/devices', [EmployeeDeviceController::class, 'index'])->name('admin.attendance.devices');
    Route::post('/admin/attendance/devices/{device}/approve', [EmployeeDeviceController::class, 'approve'])->name('admin.attendance.devices.approve');
    Route::post('/admin/attendance/devices/{device}/revoke', [EmployeeDeviceController::class, 'revoke'])->name('admin.attendance.devices.revoke');
    Route::post('/admin/attendance/users/{user}/reset-devices', [EmployeeDeviceController::class, 'resetUserDevices'])->name('admin.attendance.users.reset_devices');
    Route::get('/admin/attendance/history', [AttendanceHistoryController::class, 'index'])
    ->name('admin.attendance.history');
    Route::get('/admin/attendance/photo/{attendance}/{type}', [AttendancePhotoController::class, 'show'])
    ->name('admin.attendance.photo');
    Route::get('/admin/attendance/qr/token', [\App\Http\Controllers\Admin\AttendanceQrController::class, 'token'])
    ->name('admin.attendance.qr.token');

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


    Route::get('/absensi', [AttendanceV2Controller::class, 'index'])->name('attendance');

    // init device check (AJAX)
    Route::post('/absensi/device/init', [AttendanceV2Controller::class, 'initDevice'])->name('attendance.device.init');

    // submit check-in/out (AJAX)
    Route::post('/absensi/submit', [AttendanceV2Controller::class, 'submit'])->name('attendance.submit');
    Route::get('/absensi/history', [PegawaiAttendanceHistoryController::class, 'index'])
    ->name('attendance.history');

Route::get('/absensi/photo/{attendance}/{type}', [PegawaiAttendancePhotoController::class, 'show'])
    ->name('attendance.photo');
});