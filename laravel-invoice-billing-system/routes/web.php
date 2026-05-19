<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;

// Home page - Dashboard
Route::get('/', function () {
    // Fetch statistics to prevent "Undefined variable" 500 errors in the dashboard view
    $totalClients = \Illuminate\Support\Facades\Schema::hasTable('clients') ? \App\Models\Client::count() : 0;
    $totalInvoices = \Illuminate\Support\Facades\Schema::hasTable('invoices') ? \App\Models\Invoice::count() : 0;
    $totalRevenue = \Illuminate\Support\Facades\Schema::hasTable('invoices') ? \App\Models\Invoice::sum('total') : 0;
    $recentInvoices = \Illuminate\Support\Facades\Schema::hasTable('invoices') ? \App\Models\Invoice::with('client')->latest()->take(5)->get() : collect();

    return view('dashboard', compact('totalClients', 'totalInvoices', 'totalRevenue', 'recentInvoices'));
})->name('dashboard');

// Client Routes - Restricted to index only
Route::get('clients', function() {
    if (!\Illuminate\Support\Facades\Schema::hasTable('clients')) {
        return view('clients.index', ['clients' => collect()]);
    }
    return app(ClientController::class)->index();
})->name('clients.index');

// Safe fallbacks: prevent 500 errors if the UI still contains links to these routes
Route::get('clients/create', fn() => redirect()->route('clients.index'))->name('clients.create');
Route::get('clients/{client}/edit', fn() => redirect()->route('clients.index'))->name('clients.edit');

// Invoice Routes - Restricted to listing, creating, and viewing
Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show']);
// Safe fallbacks: prevent 500 errors if the UI still contains links to these routes
Route::get('invoices/{invoice}/edit', fn() => redirect()->route('invoices.index'))->name('invoices.edit');
Route::match(['put', 'patch'], 'invoices/{invoice}', fn() => redirect()->route('invoices.index'))->name('invoices.update');
Route::delete('invoices/{invoice}', fn() => redirect()->route('invoices.index'))->name('invoices.destroy');

Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
    ->name('invoices.pdf');

// User Profile Routes (optional)
Route::get('/profile', [UserController::class, 'edit'])->name('profile.edit');
Route::post('/profile', [UserController::class, 'update'])->name('profile.update');
Route::delete('/profile/logo', [UserController::class, 'deleteLogo'])->name('profile.deleteLogo');