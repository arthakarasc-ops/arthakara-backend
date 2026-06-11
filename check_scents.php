<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ORDERS ===" . PHP_EOL;
$orders = DB::table('orders')->get();
echo "Total orders: " . count($orders) . PHP_EOL;
foreach ($orders as $o) {
    echo "  Order ID: " . $o->id . " | user_id: " . $o->user_id . " | status_id: " . $o->status_id . PHP_EOL;
}

echo PHP_EOL . "=== ORDER ITEMS (raw SQL) ===" . PHP_EOL;
$items = DB::table('order_items')->get();
echo "Total order_items: " . count($items) . PHP_EOL;
foreach ($items as $item) {
    echo "  Item ID: " . $item->id . " | order_id: " . $item->order_id . " | scents: " . var_export($item->scents, true) . PHP_EOL;
}

echo PHP_EOL . "=== SCENTS ===" . PHP_EOL;
$scents = DB::table('scents')->get();
echo "Total scents: " . count($scents) . PHP_EOL;
foreach ($scents as $s) {
    echo "  Scent ID: " . $s->id . " | name: " . $s->name . " | is_active: " . $s->is_active . PHP_EOL;
}

echo PHP_EOL . "=== TABLES CHECK ===" . PHP_EOL;
$tables = DB::select("SHOW TABLES");
foreach ($tables as $t) {
    $tArr = (array)$t;
    echo "  " . reset($tArr) . PHP_EOL;
}
