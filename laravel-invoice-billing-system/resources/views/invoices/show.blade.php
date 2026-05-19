@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-4 fw-bold text-dark">Invoice Details</h1>
            <p class="text-muted">Invoice #{{ $invoice->invoice_number }}</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i> Download PDF
            </a>
            @auth
                @if (is_null($invoice->user_id))
                    <form action="{{ route('invoices.claim') }}" method="POST" class="d-inline" onsubmit="return confirm('Claim this invoice and set you as owner?');">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}" />
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-check me-2"></i> Claim Invoice
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </div>

    <div class="card shadow-sm mx-auto" style="max-width: 1000px;">
        <div class="card-body p-4">

            <!-- Header -->
            <div class="border-bottom pb-4 mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        @if ($invoice->logo_path)
                            <img src="{{ asset('storage/' . $invoice->logo_path) }}" alt="Logo"
                                style="max-height: 80px; margin-bottom: 15px;">
                        @elseif ($invoice->user && $invoice->user->logo_path)
                            <img src="{{ asset('storage/' . $invoice->user->logo_path) }}" alt="Logo"
                                style="max-height: 80px; margin-bottom: 15px;">
                        @endif
                        <h2 class="h2 fw-bold text-primary mb-2">{{ $invoice->user->name ?? 'iCreativez Technologies' }}</h2>
                        <p class="text-muted small mb-1">Karachi, Pakistan</p>
                        <p class="text-muted small mb-1">+92 300 5000248</p>
                        <p class="text-muted small mb-0">hello@icreativez.info</p>
                    </div>
                    <div class="text-end">
                        <h3 class="display-4 fw-bold text-dark mb-2">INVOICE</h3>
                        <p class="text-dark fw-semibold">{{ $invoice->invoice_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Invoice Info & Client Details -->
            <div class="row g-4 mb-4">

                <!-- Invoice Information -->
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted small fw-semibold mb-3">Invoice Information</h6>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex">
                            <span class="text-muted" style="width: 120px;">Invoice Date:</span>
                            <span class="fw-semibold text-dark">{{ $invoice->invoice_date->format('d M, Y') }}</span>
                        </div>
                        @if ($invoice->due_date)
                            <div class="d-flex">
                                <span class="text-muted" style="width: 120px;">Due Date:</span>
                                <span class="fw-semibold text-dark">{{ $invoice->due_date->format('d M, Y') }}</span>
                            </div>
                        @endif
                        <div class="d-flex">
                            <span class="text-muted" style="width: 120px;">Status:</span>
                            @if ($invoice->status == 'paid')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i> Paid
                                </span>
                            @elseif($invoice->status == 'unpaid')
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i> Unpaid
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i> Pending
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Client Details -->
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted small fw-semibold mb-3">Bill To</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="fw-bold text-dark fs-5 mb-1">{{ $invoice->client->name }}</p>
                        @if ($invoice->client->company)
                            <p class="text-dark fw-semibold">{{ $invoice->client->company }}</p>
                        @endif
                        @if ($invoice->client->email)
                            <p class="text-muted small mt-2 mb-1">
                                <i class="fas fa-envelope me-1"></i> {{ $invoice->client->email }}
                            </p>
                        @endif
                        @if ($invoice->client->phone)
                            <p class="text-muted small mb-1">
                                <i class="fas fa-phone me-1"></i> {{ $invoice->client->phone }}
                            </p>
                        @endif
                        @if ($invoice->client->address)
                            <p class="text-muted small mt-2 mb-0">
                                <i class="fas fa-map-marker-alt me-1"></i> {{ $invoice->client->address }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Items Table -->
            <div class="mb-4">
                <h6 class="text-uppercase text-muted small fw-semibold mb-3">Invoice Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="text-uppercase small fw-semibold">#</th>
                                <th class="text-uppercase small fw-semibold">Description</th>
                                <th class="text-uppercase small fw-semibold text-end">Qty</th>
                                <th class="text-uppercase small fw-semibold text-end">Price</th>
                                <th class="text-uppercase small fw-semibold text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td class="text-end">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ $currencySymbol }} {{ number_format($item->price, 2) }}</td>
                                    <td class="text-end fw-semibold">{{ $currencySymbol }} {{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals -->
            <div class="d-flex justify-content-end mb-4">
                <div class="bg-light p-4 rounded" style="width: 300px;">
                    <div class="d-flex justify-content-between text-muted mb-2">
                        <span>Subtotal:</span>
                        <span class="fw-semibold">{{ $currencySymbol }} {{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted mb-2">
                        <span>Tax:</span>
                        <span class="fw-semibold">{{ $currencySymbol }} {{ number_format($invoice->tax, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-dark fs-5 fw-bold border-top pt-2">
                        <span>Total:</span>
                        <span>{{ $currencySymbol }} {{ number_format($invoice->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if ($invoice->notes)
                <div class="border-top pt-4">
                    <h6 class="text-uppercase text-muted small fw-semibold mb-3">Notes / Terms</h6>
                    <p class="text-muted small lh-lg">{{ $invoice->notes }}</p>
                </div>
            @endif

            <!-- Footer -->
            <div class="border-top mt-4 pt-4 text-center">
                <p class="text-muted small">Thank you for your business!</p>
                <p class="text-muted" style="font-size: 0.75rem;">This is a computer-generated invoice.</p>
            </div>

        </div>
    </div>
@endsection
