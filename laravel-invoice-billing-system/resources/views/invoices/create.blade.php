@extends('layouts.app')

@section('title', 'Create New Invoice')

@section('content')
    <div class="py-section">
        <div class="mb-5">
            <h1 class="display-3 fw-bold text-dark mb-3">Create New Invoice</h1>
            <p class="lead text-muted">Fill in the details below to create a new invoice</p>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-5">
            <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm" enctype="multipart/form-data">
                @csrf

                <div class="row g-4 mb-4">
                    <!-- Invoice Number -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Invoice Number
                        </label>
                        <input type="text" value="{{ $invoiceNumber }}" disabled
                            class="form-control bg-light">
                    </div>

                    <!-- Invoice Date -->
                    <div class="col-md-6">
                        <label for="invoice_date" class="form-label fw-semibold">
                            Invoice Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="invoice_date" id="invoice_date" required
                            value="{{ old('invoice_date', date('Y-m-d')) }}"
                            class="form-control @error('invoice_date') is-invalid @enderror">
                        @error('invoice_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Client -->
                    <div class="col-md-6">
                        <label for="client_id" class="form-label fw-semibold">
                            Select Client <span class="text-danger">*</span>
                        </label>
                        <select name="client_id" id="client_id" required
                            class="form-select @error('client_id') is-invalid @enderror">
                            <option value="">-- Select Client --</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} @if ($client->company)
                                        - {{ $client->company }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div class="col-md-6">
                        <label for="due_date" class="form-label fw-semibold">
                            Due Date
                        </label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                            class="form-control @error('due_date') is-invalid @enderror">
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" id="status" required
                            class="form-select @error('status') is-invalid @enderror">
                            <option value="unpaid" {{ old('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Currency -->
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
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Invoice Logo (optional) -->
                    <div class="col-md-6">
                        <label for="logo" class="form-label fw-semibold">
                            Invoice Logo (optional)
                        </label>
                        <input type="file" name="logo" id="logo"
                            class="form-control @error('logo') is-invalid @enderror" accept="image/png,image/jpeg">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            <img id="logoPreview" src="#" alt="Logo Preview" style="max-height:80px;display:none;" />
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold text-dark mb-0">Invoice Items</h5>
                        <button type="button" onclick="addItem()" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-uppercase small fw-semibold">Description</th>
                                    <th class="text-uppercase small fw-semibold" style="width: 120px;">Quantity</th>
                                    <th class="text-uppercase small fw-semibold" style="width: 140px;">Price</th>
                                    <th class="text-uppercase small fw-semibold" style="width: 140px;">Total</th>
                                    <th class="text-uppercase small fw-semibold" style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr class="item-row">
                                    <td>
                                        <input type="text" name="items[0][description]" required
                                            placeholder="Service description"
                                            class="form-control @error('items.0.description') is-invalid @enderror">
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" required min="1" value="1"
                                            class="form-control item-quantity @error('items.0.quantity') is-invalid @enderror"
                                            onchange="calculateRow(this)">
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][price]" required min="0" step="0.01"
                                            placeholder="0.00"
                                            class="form-control item-price @error('items.0.price') is-invalid @enderror"
                                            onchange="calculateRow(this)">
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control item-total bg-light"
                                            value="0.00" readonly>
                                    </td>
                                    <td>
                                        <button type="button" onclick="removeItem(this)" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totals -->
                <div class="d-flex justify-content-end mb-4">
                    <div class="w-100" style="max-width: 300px;">
                        <div class="d-flex justify-content-between text-muted mb-2">
                            <span>Subtotal:</span>
                            <span class="fw-semibold">Rs. <span id="subtotalDisplay">0.00</span></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-muted mb-2">
                            <span>Tax:</span>
                            <div class="d-flex align-items-center">
                                <span class="me-2">Rs.</span>
                                <input type="number" name="tax" id="tax" min="0" step="0.01"
                                    value="0"
                                    class="form-control form-control-sm @error('tax') is-invalid @enderror"
                                    style="width: 100px;"
                                    onchange="calculateTotal()">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between text-dark fs-5 fw-bold border-top pt-2">
                            <span>Total:</span>
                            <span>Rs. <span id="totalDisplay">0.00</span></span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label for="notes" class="form-label fw-semibold">
                        Notes / Terms
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="form-control @error('notes') is-invalid @enderror"
                        placeholder="Any additional notes or payment terms...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i> Create Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemIndex = 1;

        // Add new item row
        function addItem() {
            const tbody = document.getElementById('itemsBody');
            const newRow = `
        <tr class="item-row">
            <td>
                <input type="text" name="items[${itemIndex}][description]" required placeholder="Service description"
                    class="form-control">
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" required min="1" value="1"
                    class="form-control item-quantity"
                    onchange="calculateRow(this)">
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][price]" required min="0" step="0.01" placeholder="0.00"
                    class="form-control item-price"
                    onchange="calculateRow(this)">
            </td>
            <td>
                <input type="text" class="form-control item-total bg-light" 
                    value="0.00" readonly>
            </td>
            <td>
                <button type="button" onclick="removeItem(this)" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
            tbody.insertAdjacentHTML('beforeend', newRow);
            itemIndex++;
        }

        // Remove item row
        function removeItem(button) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                Swal.fire({
                    title: 'Remove Item?',
                    text: 'Are you sure you want to remove this item?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('tr').remove();
                        calculateTotal();
                        Toast.fire({
                            icon: 'success',
                            title: 'Item removed successfully'
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: 'Cannot Remove',
                    text: 'At least one item is required!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Calculate single row total
        function calculateRow(input) {
            const row = input.closest('tr');
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const total = quantity * price;
            row.querySelector('.item-total').value = total.toFixed(2);
            calculateTotal();
        }

        // Calculate invoice total
        function calculateTotal() {
            let subtotal = 0;
            document.querySelectorAll('.item-total').forEach(input => {
                subtotal += parseFloat(input.value) || 0;
            });

            const tax = parseFloat(document.getElementById('tax').value) || 0;
            const total = subtotal + tax;

            document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2);
            document.getElementById('totalDisplay').textContent = total.toFixed(2);
        }

        // Logo preview
        const logoInput = document.getElementById('logo');
        if (logoInput) {
            logoInput.addEventListener('change', function () {
                const file = this.files && this.files[0];
                const preview = document.getElementById('logoPreview');
                if (file && preview) {
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'block';
                }
            });
        }
    </script>
    </div>
    </div>
@endsection
