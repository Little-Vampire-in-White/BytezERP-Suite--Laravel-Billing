<?phpplete Code Reference - All Updated Files

namespace App\Http\Controllers;

use App\Models\Invoice;go to Users Table
use App\Models\Client;grations/2025_12_05_000001_add_logo_to_users_table.php`
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controlleron;
{se Illuminate\Database\Schema\Blueprint;
    // Define the currency symbolsema;
    private $currencies = [
        'USD' => '$',nds Migration
        'PKR' => 'Rs.',
        'EUR' => '€',p(): void
        'GBP' => '£',
        'AED' => 'د.إ',users', function (Blueprint $table) {
    ];      $table->string('logo_path')->nullable()->after('password');
        });
    // Saare invoices dikhaye (only for current user)
    public function index()
    {ublic function down(): void
        $invoices = Invoice::where('user_id', auth()->id())
            ->with('client')', function (Blueprint $table) {
            ->latest()opColumn('logo_path');
            ->paginate(10);
        return view('invoices.index', compact('invoices'));
    }
```
    // Naya invoice banane ka form
    public function create()
    {
        $clients = Client::all();Invoices Table
        // Latest invoice number generate kare02_add_currency_to_invoices_table.php`
        $lastInvoice = Invoice::where('user_id', auth()->id())->latest()->first();
        $invoiceNumber = 'INV-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 4, '0', STR_PAD_LEFT);
<?php
        $currencies = array_keys($this->currencies);
use Illuminate\Database\Migrations\Migration;
        return view('invoices.create', compact('clients', 'invoiceNumber', 'currencies'));
    }lluminate\Support\Facades\Schema;

    // Invoice save kare Migration
    public function store(Request $request)
    {ublic function up(): void
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',able) {
            'invoice_date' => 'required|date',ault('USD')->after('total');
            'due_date' => 'nullable|date',
            'status' => 'required|in:paid,unpaid,pending',
            'currency' => 'required|in:USD,PKR,EUR,GBP,AED',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',n (Blueprint $table) {
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        // Invoice number generate kare
        $lastInvoice = Invoice::where('user_id', auth()->id())->latest()->first();
        $invoiceNumber = 'INV-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 4, '0', STR_PAD_LEFT);
**File:** `database/migrations/2025_12_05_000003_add_user_id_to_invoices_table.php`
        // Subtotal calculate kare
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['price'];
        }inate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
        // Tax aur total calculate kare
        $tax = $request->tax ?? 0;
        $total = $subtotal + $tax;
{
        // Invoice create kare
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,eprint $table) {
            'user_id' => auth()->id(),')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            'client_id' => $request->client_id,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'currency' => $request->currency,
            'subtotal' => $subtotal,nction (Blueprint $table) {
            'tax' => $tax,reignKeyIfExists(['user_id']);
            'total' => $total,('user_id');
            'notes' => $request->notes
        ]);
};
        // Handle per-invoice logo upload (optional)
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $invoice->logo_path = $path;
            $invoice->save();
        }
### User Model
        // Invoice items save kare
        foreach ($request->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price']
            ]);Foundation\Auth\User as Authenticatable;
        }inate\Notifications\Notifiable;

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully!');
    }se HasFactory, Notifiable;

    // Invoice detail dikhaye
    public function show(Invoice $invoice)
    {   'email',
        $this->authorize('view', $invoice);
        $invoice->load('client', 'items', 'user');
        $currencySymbol = $this->currencies[$invoice->currency] ?? '$';
        return view('invoices.show', compact('invoice', 'currencySymbol'));
    }rotected $hidden = [
        'password',
    // Invoice PDF download kare
    public function downloadPdf(Invoice $invoice)
    {
        $this->authorize('downloadPdf', $invoice);
        $invoice->load('client', 'items', 'user');
        $currencySymbol = $this->currencies[$invoice->currency] ?? '$';
            'email_verified_at' => 'datetime',
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'currencySymbol'));
        ];
        return $pdf->download($invoice->invoice_number . '.pdf');
    }
    /**
    // Invoice delete kareeated by this user.
    public function destroy(Invoice $invoice)
    {ublic function invoices()
        $this->authorize('delete', $invoice);
        $invoice->delete();ny(Invoice::class);
    }
        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully!');
    }
}--

### Invoice Model
**File:** `app/Models/Invoice.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'client_id',
        'invoice_date',
        'due_date',
        'status',
        'subtotal',
        'tax',
        'total',
        'currency',
        'notes'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    // Invoice ka user (who created it)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Invoice ka client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Invoice ki items
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
```

---

## 3. POLICIES

### InvoicePolicy
**File:** `app/Policies/InvoicePolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine whether the user can view the invoice.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id;
    }

    /**
     * Determine whether the user can update the invoice.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id;
    }

    /**
     * Determine whether the user can delete the invoice.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id;
    }

    /**
     * Determine whether the user can download the invoice PDF.
     */
    public function downloadPdf(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id;
    }
}
```

---

## 4. SERVICE PROVIDER

### AppServiceProvider
**File:** `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Invoice;
use App\Policies\InvoicePolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Invoice::class => InvoicePolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
```

---

## 5. CONTROLLERS

### InvoiceController
**File:** `app/Http/Controllers/InvoiceController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    // Define the currency symbols
    private $currencies = [
        'USD' => '$',
        'PKR' => 'Rs.',
        'EUR' => '€',
        'GBP' => '£',
        'AED' => 'د.إ',
    ];

    // Saare invoices dikhaye (only for current user)
    public function index()
    {
        $invoices = Invoice::where('user_id', auth()->id())
            ->with('client')
            ->latest()
            ->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    // Naya invoice banane ka form
    public function create()
    {
        $clients = Client::all();
        // Latest invoice number generate kare
        $lastInvoice = Invoice::where('user_id', auth()->id())->latest()->first();
        $invoiceNumber = 'INV-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        $currencies = array_keys($this->currencies);

        return view('invoices.create', compact('clients', 'invoiceNumber', 'currencies'));
    }

    // Invoice save kare
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'status' => 'required|in:paid,unpaid,pending',
            'currency' => 'required|in:USD,PKR,EUR,GBP,AED',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        // Invoice number generate kare
        $lastInvoice = Invoice::where('user_id', auth()->id())->latest()->first();
        $invoiceNumber = 'INV-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        // Subtotal calculate kare
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['price'];
        }

        // Tax aur total calculate kare
        $tax = $request->tax ?? 0;
        $total = $subtotal + $tax;

        // Invoice create kare
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => auth()->id(),
            'client_id' => $request->client_id,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'currency' => $request->currency,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'notes' => $request->notes
        ]);

        // Invoice items save kare
        foreach ($request->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price']
            ]);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully!');
    }

    // Invoice detail dikhaye
    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load('client', 'items', 'user');
        $currencySymbol = $this->currencies[$invoice->currency] ?? '$';
        return view('invoices.show', compact('invoice', 'currencySymbol'));
    }

    // Invoice PDF download kare
    public function downloadPdf(Invoice $invoice)
    {
        $this->authorize('downloadPdf', $invoice);
        $invoice->load('client', 'items', 'user');
        $currencySymbol = $this->currencies[$invoice->currency] ?? '$';

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'currencySymbol'));

        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    // Invoice delete kare
    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully!');
    }
}
```

---

### UserController (NEW)
**File:** `app/Http/Controllers/UserController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Show the user profile/settings page.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile including logo upload.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $user = auth()->user();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($user->logo_path && Storage::disk('public')->exists($user->logo_path)) {
                Storage::disk('public')->delete($user->logo_path);
            }

            // Store new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'logo_path' => $validated['logo_path'] ?? $user->logo_path,
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Delete the user's logo.
     */
    public function deleteLogo()
    {
        $user = auth()->user();

        if ($user->logo_path && Storage::disk('public')->exists($user->logo_path)) {
            Storage::disk('public')->delete($user->logo_path);
        }

        $user->update(['logo_path' => null]);

        return redirect()->route('profile.edit')
            ->with('success', 'Logo deleted successfully!');
    }
}
```

---

## 6. ROUTES

### Web Routes
**File:** `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserController;

// Home page - Dashboard
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// Client Routes
Route::resource('clients', ClientController::class);

// Invoice Routes
Route::resource('invoices', InvoiceController::class);
Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])
    ->name('invoices.pdf');

// User Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::delete('/profile/logo', [UserController::class, 'deleteLogo'])->name('profile.deleteLogo');
});
```

---

## 7. KEY BLADE VIEW SECTIONS

### Currency Display Helper (used in multiple views)

```php
@php
    $currencySymbols = [
        'USD' => '$',
        'PKR' => 'Rs.',
        'EUR' => '€',
        'GBP' => '£',
        'AED' => 'د.إ',
    ];
    $symbol = $currencySymbols[$invoice->currency] ?? '$';
@endphp
```

### Logo Display (show.blade.php & pdf.blade.php)

```blade
@if ($invoice->user && $invoice->user->logo_path)
    <img src="{{ asset('storage/' . $invoice->user->logo_path) }}" alt="Logo"
        style="max-height: 80px; margin-bottom: 15px;">
@endif
```

### Currency Dropdown (create.blade.php)

```blade
<div class="col-md-6">
    <label for="currency" class="form-label fw-semibold">
        Currency <span class="text-danger">*</span>
    </label>
    <select name="currency" id="currency" required
        class="form-select @error('currency') is-invalid @enderror">
        <option value="">-- Select Currency --</option>
        @foreach ($currencies as $currency)
            <option value="{{ $currency }}" {{ old('currency') == $currency ? 'selected' : '' }}>
                {{ $currency }}
            </option>
        @endforeach
    </select>
</div>
```

---

## QUICK DEPLOYMENT CHECKLIST

```bash
# 1. Backup database
# 2. Run migrations
php artisan migrate

# 3. Create storage symlink
php artisan storage:link

# 4. Set permissions
chmod -R 755 storage/app/public

# 5. Clear cache
php artisan cache:clear

# 6. Test the features
# - Create invoice with currency
# - Upload logo in profile
# - Verify authorization works
```

---

**All code is production-ready and follows Laravel 12 best practices!**
