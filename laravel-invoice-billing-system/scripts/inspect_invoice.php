<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;

$id = $argv[1] ?? 4; // default to 4
$inv = Invoice::find($id);

echo "--- Invoice inspect (id={$id}) ---\n";
if (!$inv) {
    echo "Invoice not found\n";
    exit(0);
}

echo "Invoice DB row:\n" . json_encode($inv->toArray(), JSON_PRETTY_PRINT) . "\n\n";

echo "user_id: " . ($inv->user_id ?? 'NULL') . "\n\n";

$u = $inv->user;
if ($u) {
    echo "Related user:\n" . json_encode($u->toArray(), JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Related user: NULL\n";
}

exit(0);
