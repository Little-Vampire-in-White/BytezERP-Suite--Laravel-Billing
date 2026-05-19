<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;

$invoices = Invoice::with('user')->latest()->take(20)->get();

if ($invoices->isEmpty()) {
    echo "No invoices found\n";
    exit(0);
}

foreach ($invoices as $inv) {
    echo sprintf("ID: %d | Number: %s | user_id: %s | user_email: %s | created_at: %s\n",
        $inv->id,
        $inv->invoice_number,
        $inv->user_id ?? 'NULL',
        $inv->user->email ?? 'NULL',
        $inv->created_at ?? 'NULL'
    );
}

exit(0);
