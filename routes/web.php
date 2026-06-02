<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\PaymentNotificationController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\QrCodeController as AdminQrCodeController;
use App\Http\Controllers\OrderController as CustomerOrderController;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboardController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Cashier\TransactionController; // Tambahkan ini
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| QR SCAN ROUTES (PUBLIC / CUSTOMER)
|--------------------------------------------------------------------------
*/
Route::get('/scan', [QrCodeController::class, 'showScan'])->name('scan.qr');
Route::get('/scan/validate/{qr_code}', [QrCodeController::class, 'validateQr'])->name('scan.qr.validate.get')->middleware('throttle:15,1');
Route::post('/scan/validate', [QrCodeController::class, 'validateQr'])->name('scan.qr.validate')->middleware('throttle:15,1');
Route::get('/scan/validate', function () {
    return redirect()->route('scan.qr')->with('error', 'Silakan masukkan kode meja Anda melalui form yang tersedia.');
});
Route::get('/scan/check', [QrCodeController::class, 'check'])->name('scan.qr.check');
Route::get('/scan/reset', [QrCodeController::class, 'resetSession'])->name('scan.qr.reset');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Login (no middleware)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Protected Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ============================================
        // QR CODE MANAGEMENT (ADMIN)
        // ============================================
        Route::get('/qrcodes/export', [AdminQrCodeController::class, 'exportPage'])->name('qrcodes.export');
        Route::get('/qrcodes/export/csv', [AdminQrCodeController::class, 'exportCsv'])->name('qrcodes.export.csv');
        Route::get('/qrcodes/export/excel', [AdminQrCodeController::class, 'exportExcel'])->name('qrcodes.export.excel');
        Route::get('/qrcodes/export/pdf', [AdminQrCodeController::class, 'exportPdf'])->name('qrcodes.export.pdf');
        
        Route::resource('qrcodes', AdminQrCodeController::class);
        Route::post('/qrcodes/{qrcode}/toggle-status', [AdminQrCodeController::class, 'toggleStatus'])
            ->name('qrcodes.toggle-status');
        Route::get('/qrcodes/{qrcode}/download', [AdminQrCodeController::class, 'download'])
            ->name('qrcodes.download');
        Route::get('/qrcodes/{qrcode}/print', [AdminQrCodeController::class, 'print'])
            ->name('qrcodes.print');
        Route::post('/qrcodes/bulk-delete', [AdminQrCodeController::class, 'bulkDelete'])
            ->name('qrcodes.bulk-delete');
        Route::post('/qrcodes/{qrcode}/duplicate', [AdminQrCodeController::class, 'duplicate'])
            ->name('qrcodes.duplicate');
        
        // ============================================
        // PRODUCTS MANAGEMENT
        // ============================================
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/toggle-availability', [ProductController::class, 'toggleAvailability'])
            ->name('products.toggle-availability');

        // ============================================
        // CATEGORIES MANAGEMENT
        // ============================================
        Route::resource('categories', CategoryController::class);

        // ============================================
        // ORDERS MANAGEMENT
        // ============================================
        Route::controller(AdminOrderController::class)->group(function () {
            Route::get('/orders', 'index')->name('orders.index');
            Route::get('/orders/create', 'create')->name('orders.create');
            Route::post('/orders', 'store')->name('orders.store');
            Route::get('/orders/{order}', 'show')->name('orders.show');
            Route::get('/orders/{order}/edit', 'edit')->name('orders.edit');
            Route::put('/orders/{order}', 'update')->name('orders.update');
            Route::delete('/orders/{order}', 'destroy')->name('orders.destroy');
            Route::put('/orders/{order}/status', 'updateStatus')->name('orders.update-status');
            Route::post('/orders/{order}/confirm-payment', 'confirmPayment')->name('orders.confirm-payment');
            Route::get('/orders/{order}/invoice', 'printInvoice')->name('orders.invoice');
            Route::get('/orders/{order}/export/pdf', 'exportPdf')->name('orders.export.pdf');
        });

        // ============================================
        // USERS MANAGEMENT
        // ============================================
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // ============================================
        // REPORTS MANAGEMENT
        // ============================================
        Route::prefix('reports')->name('reports.')->controller(ReportController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/sales', 'sales')->name('sales');
            Route::get('/export-pdf', 'exportPdf')->name('export-pdf');
            Route::get('/export-excel', 'exportExcel')->name('export-excel');
        });

        // ============================================
        // SETTINGS MANAGEMENT
        // ============================================
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general');
        Route::post('/settings/payment', [SettingController::class, 'updatePayment'])->name('settings.payment');
        Route::post('/settings/tax', [SettingController::class, 'updateTax'])->name('settings.tax');
        Route::post('/settings/printer', [SettingController::class, 'updatePrinter'])->name('settings.printer');
        Route::post('/settings/notification', [SettingController::class, 'updateNotification'])->name('settings.notification');
        Route::post('/settings/test-printer', [SettingController::class, 'testPrinter'])->name('settings.test-printer');
        Route::post('/settings/reset', [SettingController::class, 'reset'])->name('settings.reset');
        Route::get('/settings/backup', [SettingController::class, 'backup'])->name('settings.backup');
        Route::post('/settings/restore', [SettingController::class, 'restore'])->name('settings.restore');

        // Notifications
        Route::get('/notifications', [PaymentNotificationController::class, 'adminIndex'])->name('notifications.index');
    });
});

/*
|--------------------------------------------------------------------------
| CASHIER ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('cashier')->name('cashier.')->middleware(['auth', 'cashier'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [CashierDashboardController::class, 'index'])->name('dashboard');
    
    // Order Management
    Route::get('/orders', [CashierOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [CashierOrderController::class, 'show'])->name('order.show');
    Route::get('/orders/{order}/details', [CashierOrderController::class, 'getOrderDetails'])->name('order.details');
    Route::put('/orders/{order}/status', [CashierOrderController::class, 'updateStatus'])->name('order.update-status');
    Route::post('/orders/{order}/confirm-payment', [CashierOrderController::class, 'confirmPayment'])->name('order.confirm-payment');
    Route::post('/orders/{order}/process-cash-payment', [CashierOrderController::class, 'processCashPayment'])->name('order.process-cash');
    Route::post('/orders/{order}/cancel', [CashierOrderController::class, 'cancelOrder'])->name('order.cancel');
    Route::get('/orders/{order}/receipt', [CashierOrderController::class, 'printReceipt'])->name('receipt');
    Route::post('/orders/{order}/completed', [CashierOrderController::class, 'markAsCompleted'])->name('order.completed');
    
    // Transaction Routes (sudah lengkap)
    Route::get('/transactions/today', [TransactionController::class, 'today'])->name('transactions.today');
    Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');
    Route::get('/transactions/daily-summary', [TransactionController::class, 'dailySummary'])->name('transactions.daily-summary');
    Route::get('/transactions/export-excel', [TransactionController::class, 'exportExcel'])->name('transactions.export-excel');
    Route::get('/transactions/export-pdf', [TransactionController::class, 'exportPDF'])->name('transactions.export-pdf');
    
    // Additional short routes for convenience
    Route::get('/daily-summary', [TransactionController::class, 'dailySummary'])->name('daily-summary');
    Route::get('/history', [TransactionController::class, 'history'])->name('history');
    
    // Transaction today orders (if still needed)
    Route::get('/transactions/today-orders', [CashierOrderController::class, 'todayTransactions'])->name('transactions.today-orders');
    Route::get('/transactions/order-history', [CashierOrderController::class, 'transactionHistory'])->name('transactions.order-history');
    
    // Table management
    Route::post('/table/reset', [CashierOrderController::class, 'resetTable'])->name('table.reset');
});

/*
|--------------------------------------------------------------------------
| CART ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['check.qr'])->prefix('cart')->name('cart.')->group(function () {
    Route::post('/save-to-session', [CartController::class, 'saveToSession'])->name('save-to-session');
    Route::post('/clear-session', [CartController::class, 'clearSession'])->name('clear-session');
    Route::get('/get-from-session', [CartController::class, 'getFromSession'])->name('get-from-session');
});

/*
|--------------------------------------------------------------------------
| CUSTOMER ROUTES (Require QR Code)
|--------------------------------------------------------------------------
*/
Route::middleware(['check.qr'])->group(function () {
    // Home
    Route::get('/', function () {
        $categories = App\Models\Category::all();
        // Query for most popular products based on number of times ordered
        $products = App\Models\Product::where('is_available', true)
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(8)
            ->get();
        return view('home', compact('categories', 'products'));
    })->name('home');

    // Menu
    Route::get('/menu', function () {
        $categories = App\Models\Category::with('products')->get();
        return view('menu', compact('categories'));
    })->name('menu');

    // Tentang Kami
    Route::get('/about', function () {
        return view('about');
    })->name('about');

    // Order Customer
    Route::get('/order/create', [CustomerOrderController::class, 'create'])->name('order.create');
    Route::post('/order', [CustomerOrderController::class, 'store'])->name('order.store')->middleware('throttle:10,1');
    Route::get('/order/{orderNumber}/success', [CustomerOrderController::class, 'success'])->name('order.success');
    Route::get('/order/{orderNumber}/payment', [CustomerOrderController::class, 'payment'])->name('order.payment');
    Route::post('/order/{orderNumber}/payment-process', [CustomerOrderController::class, 'processPayment'])->name('order.process-payment');
});

/*
|--------------------------------------------------------------------------
| CUSTOMER DASHBOARD (Require QR Code)
|--------------------------------------------------------------------------
*/
Route::middleware(['check.qr'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/track-order/{orderNumber}', [CustomerDashboardController::class, 'trackOrder'])->name('track-order');
        Route::get('/orders-history', [CustomerDashboardController::class, 'history'])->name('history');
        
        // ============================================
        // 🔥 ROUTE UNTUK NOTIFIKASI PESANAN SELESAI
        // ============================================
        Route::get('/check-new-completed', [CustomerOrderController::class, 'checkNewCompletedOrders'])
            ->name('check-new-completed');
        
        // Route untuk reset session (jika diperlukan)
        Route::post('/reset', [CustomerController::class, 'reset'])->name('reset');
        Route::get('/check-session', [CustomerController::class, 'checkSession'])->name('checkSession');
    });

/*
|--------------------------------------------------------------------------
| API ROUTES (Version 1)
|--------------------------------------------------------------------------
*/
Route::prefix('api/v1')->name('api.')->group(function () {
    // Notifications
    Route::get('/cashier-notifications', [PaymentNotificationController::class, 'getCashierNotifications']);
    Route::get('/customer-notifications', [PaymentNotificationController::class, 'getCustomerNotifications']);
    Route::post('/notifications/mark-all-read', [PaymentNotificationController::class, 'markAllRead']);
    Route::post('/notifications/{id}/read', [PaymentNotificationController::class, 'markAsRead']);

    // Orders API
    Route::get('/orders/{order}/details', [AdminOrderController::class, 'getOrderDetails']);
    Route::post('/orders/{order}/payment-confirmation', [AdminOrderController::class, 'apiConfirmPayment']);
    
    // 🔥 API untuk cek order aktif
    Route::get('/check-active-order/{qrCode}', function($qrCode) {
        $activeOrder = App\Models\Order::where('qr_code', $qrCode)
            ->whereIn('order_status', ['waiting', 'processed'])
            ->exists();
        return response()->json(['active' => $activeOrder]);
    });
    
    // 🔥 API untuk cek status cart
    Route::get('/cart/status', function() {
        return response()->json([
            'cart_count' => count(session('cart', [])),
            'session_id' => session()->getId()
        ]);
    });
    
    // 🔥 API untuk cek order baru selesai (untuk mobile apps)
    Route::get('/customer/check-completed/{sessionId}', function($sessionId) {
        $completedOrders = App\Models\Order::where('session_id', $sessionId)
            ->where('order_status', 'completed')
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->get(['order_number', 'order_status', 'updated_at']);
            
        return response()->json([
            'success' => true,
            'completed_orders' => $completedOrders
        ]);
    });

    // 🔥 API untuk cek status order secara detail
    Route::get('/orders/{orderNumber}/status', [CustomerOrderController::class, 'checkStatus'])->name('order.status');

    // 🔥 API untuk konfirmasi niat bayar (QRIS/Transfer)
    Route::post('/orders/{orderNumber}/confirm-payment-intent', [CustomerOrderController::class, 'confirmPaymentIntent'])
        ->name('order.confirm-payment-intent');
});

/*
|--------------------------------------------------------------------------
| TEST ROUTES (Hanya untuk development)
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::get('/test/storage', function () {
        $link = storage_path('app/public');
        $public = public_path('storage');
        return [
            'storage_link_exists' => file_exists($public),
            'storage_folder_exists' => file_exists($link),
            'products_folder_exists' => file_exists(storage_path('app/public/products')),
        ];
    });
    
    // Route debug session
    Route::get('/debug-session', function () {
        return response()->json([
            'session_id' => session()->getId(),
            'qr_code' => session('qr_code'),
            'qr_validated' => session('qr_validated'),
            'session_start' => session('session_start'),
            'cart' => session('cart', []),
            'cart_count' => count(session('cart', [])),
            'all_session_data' => collect(session()->all())->except(['_token', '_previous'])->toArray()
        ]);
    });
    
    // 🔥 Route test notifikasi Pusher
    Route::get('/test-notification/{orderNumber}', function($orderNumber) {
        $order = App\Models\Order::where('order_number', $orderNumber)->first();
        if ($order) {
            broadcast(new App\Events\OrderCompleted($order));
            return 'Notifikasi dikirim untuk order #' . $orderNumber;
        }
        return 'Order tidak ditemukan';
    });
}