@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
    <div class="py-section">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="display-3 fw-bold text-dark mb-3">Invoices</h1>
                <p class="lead text-muted">Manage all your invoices</p>
            </div>
            <a href="{{ route('invoices.create') }}" class="btn btn-success btn-lg shadow-sm">
                <i class="fas fa-plus me-2"></i> Create New Invoice
            </a>
        </div>

        @if ($invoices->count() > 0)
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">Invoice #</th>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">Client</th>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">Date</th>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">Total</th>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">Status</th>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td class="px-3 py-3">
                                            <div class="fw-semibold text-dark">{{ $invoice->invoice_number }}</div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                                    style="width: 40px; height: 40px;">
                                                    <span
                                                        class="text-warning fw-semibold">{{ substr($invoice->client->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <div class="fw-medium text-dark">{{ $invoice->client->name }}</div>
                                                    <div class="small text-muted">{{ $invoice->client->company }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-muted">
                                            {{ $invoice->invoice_date->format('d M, Y') }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="fw-semibold text-dark">
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
                                                {{ $symbol }} {{ number_format($invoice->total, 2) }}
                                                <span class="small text-muted">({{ $invoice->currency }})</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
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
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('invoices.show', $invoice) }}"
                                                    class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('invoices.pdf', $invoice) }}"
                                                    class="btn btn-sm btn-outline-success" title="Download PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                    onclick="confirmDelete('This will permanently delete the invoice. This action cannot be undone.', function() {
                                                    document.getElementById('delete-form-{{ $invoice->id }}').submit();
                                                })">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $invoice->id }}"
                                                    action="{{ route('invoices.destroy', $invoice) }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-5">
                {{ $invoices->links() }}
            </div>
        @else
            <div class="card shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="fas fa-file-invoice text-muted" style="font-size: 4rem;"></i>
                    <h3 class="h4 fw-semibold text-dark mt-3 mb-2">No Invoices Yet</h3>
                    <p class="text-muted mb-4">Get started by creating your first invoice</p>
                    <a href="{{ route('invoices.create') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-plus me-2"></i> Create First Invoice
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
