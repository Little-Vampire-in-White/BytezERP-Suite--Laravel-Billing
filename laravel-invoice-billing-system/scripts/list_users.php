<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\User;
$users = User::all();
if ($users->isEmpty()) {
    echo "No users found\n";
    exit(0);
}
foreach ($users as $u) {
    echo sprintf("ID: %d | name: %s | email: %s | created_at: %s\n", $u->id, $u->name, $u->email, $u->created_at);
}
exit(0);
