<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();
    \Illuminate\Support\Facades\Auth::login($user);
    $orders = \App\Models\Order::latest()->get(); // simulating get instead of paginate
    $stats = ["pending_payment" => 0, "waiting" => 0, "processed" => 0, "completed_today" => 0, "today_revenue" => 0];
    echo "Rendering orders index...\n";
    $html = view("cashier.orders.index", compact("orders", "stats"))->render();
    echo "SUCCESS\n";
} catch (\Exception $e) {
    echo "ERROR CAUGHT IN ORDERS INDEX:\n";
    echo $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine() . "\n";
}
