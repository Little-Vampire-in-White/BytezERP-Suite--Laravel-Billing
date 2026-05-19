@extends('layouts.app')

@section('title', 'Clients')

@section('content')
    <div class="py-section">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="display-3 fw-bold text-dark mb-3">Clients</h1>
                <p class="lead text-muted">Manage all your clients</p>
            </div>
            <a href="{{ route('clients.create') }}" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-plus me-2"></i> Add New Client
            </a>
        </div>

        @if ($clients->count() > 0)
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">#</th>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">Name</th>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">Company</th>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">Email</th>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">Phone</th>
                                    <th class="px-3 py-3 text-uppercase small fw-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $client)
                                    <tr>
                                        <td class="px-3 py-3">{{ $client->id }}</td>
                                        <td class="px-3 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                                    style="width: 40px; height: 40px;">
                                                    <span
                                                        class="text-primary fw-semibold">{{ substr($client->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <div class="fw-medium text-dark">{{ $client->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-dark">{{ $client->company ?? 'N/A' }}</td>
                                        <td class="px-3 py-3 text-muted">{{ $client->email ?? 'N/A' }}</td>
                                        <td class="px-3 py-3 text-muted">{{ $client->phone ?? 'N/A' }}</td>
                                        <td class="px-3 py-3">
                                            <a href="{{ route('clients.edit', $client) }}"
                                                class="btn btn-sm btn-outline-primary me-2">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete('This will permanently delete the client and all associated invoices. This action cannot be undone.', function() {
                                                document.getElementById('delete-form-{{ $client->id }}').submit();
                                            })">
                                                <i class="fas fa-trash me-1"></i> Delete
                                            </button>
                                            <form id="delete-form-{{ $client->id }}"
                                                action="{{ route('clients.destroy', $client) }}" method="POST"
                                                class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
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
                {{ $clients->links() }}
            </div>
        @else
            <div class="card shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
                    <h3 class="h4 fw-semibold text-dark mt-3 mb-2">No Clients Yet</h3>
                    <p class="text-muted mb-4">Get started by adding your first client</p>
                    <a href="{{ route('clients.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i> Add First Client
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
