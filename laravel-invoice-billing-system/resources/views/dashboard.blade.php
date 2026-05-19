@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="py-section">
        <div class="mb-5">
            <h1 class="display-3 fw-bold text-dark mb-3">Dashboard</h1>
            <p class="lead text-muted">Welcome to Invoice & Billing System</p>
        </div>

        <div class="row g-4 mb-5">

            <!-- Total Clients Card -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-2 fw-semibold text-uppercase">Total Clients</p>
                                <h3 class="display-4 fw-bold text-dark mb-0">{{ \App\Models\Client::count() }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-4">
                                <i class="fas fa-users text-primary fs-2"></i>
                            </div>
                        </div>
                        <a href="{{ route('clients.index') }}" class="text-primary text-decoration-none small mt-3 d-inline-block fw-semibold">
                            View all clients <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Invoices Card -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-2 fw-semibold text-uppercase">Total Invoices</p>
                                <h3 class="display-4 fw-bold text-dark mb-0">{{ \App\Models\Invoice::count() }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-4">
                                <i class="fas fa-file-invoice text-success fs-2"></i>
                            </div>
                        </div>
                        <a href="{{ route('invoices.index') }}" class="text-success text-decoration-none small mt-3 d-inline-block fw-semibold">
                            View all invoices <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Revenue Card -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-2 fw-semibold text-uppercase">Total Revenue</p>
                                <h3 class="display-4 fw-bold text-dark mb-0">Rs.
                                    {{ number_format(\App\Models\Invoice::sum('total'), 2) }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 rounded-circle p-4">
                                <i class="fas fa-dollar-sign text-warning fs-2"></i>
                            </div>
                        </div>
                        <p class="text-warning small mt-3 mb-0 fw-semibold">From all invoices</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Quick Actions -->
        <div class="my-section">
            <h2 class="h2 fw-bold text-dark mb-4">Quick Actions</h2>
            <div class="row g-4">

                <div class="col-md-6">
                    <a href="{{ route('clients.create') }}" class="text-decoration-none">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-4 me-4">
                                    <i class="fas fa-user-plus text-primary fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="card-title text-dark mb-2">Add New Client</h5>
                                    <p class="card-text text-muted mb-0">Create a new client profile</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6">
                    <a href="{{ route('invoices.create') }}" class="text-decoration-none">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <div class="card-body p-4 d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded-circle p-4 me-4">
                                    <i class="fas fa-plus-circle text-success fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="card-title text-dark mb-2">Create New Invoice</h5>
                                    <p class="card-text text-muted mb-0">Generate a new invoice</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection
