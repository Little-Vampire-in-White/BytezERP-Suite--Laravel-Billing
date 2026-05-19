<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create (or update) a test user
        $email = 'admin@bytez.com';
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Create a test client
        $client = Client::create([
            'name' => 'Acme Corp',
            'email' => 'contact@acme.com',
            'phone' => '+123456789',
            'company' => 'Acme Industries',
            'address' => '456 Innovation Way, Tech City',
        ]);

        // Create an initial invoice
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-0001',
            'client_id' => $client->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(15)->format('Y-m-d'),
            'status' => 'unpaid',
            'subtotal' => 500.00,
            'tax' => 50.00,
            'total' => 550.00,
            'currency' => 'USD',
        ]);

        // Create items for the invoice
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Software Development Consultation',
            'quantity' => 5,
            'price' => 100.00,
            'total' => 500.00,
        ]);
    }
}