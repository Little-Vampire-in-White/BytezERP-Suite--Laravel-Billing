@extends('layouts.app')

@section('title', 'Add New Client')

@section('content')
    <div class="py-section">
        <div class="mb-5">
            <h1 class="display-3 fw-bold text-dark mb-3">Add New Client</h1>
            <p class="lead text-muted">Fill in the details below to add a new client</p>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-5">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf

                <!-- Name Field -->
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">
                        Client Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Enter client name" value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">
                        Email Address
                    </label>
                    <input type="email" name="email" id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="client@example.com" value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div class="mb-4">
                    <label for="phone" class="form-label fw-semibold">
                        Phone Number
                    </label>
                    <input type="text" name="phone" id="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        placeholder="0300-1234567" value="{{ old('phone') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Company Field -->
                <div class="mb-4">
                    <label for="company" class="form-label fw-semibold">
                        Company Name
                    </label>
                    <input type="text" name="company" id="company"
                        class="form-control @error('company') is-invalid @enderror"
                        placeholder="Company name" value="{{ old('company') }}">
                    @error('company')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Address Field -->
                <div class="mb-4">
                    <label for="address" class="form-label fw-semibold">
                        Address
                    </label>
                    <textarea name="address" id="address" rows="3"
                        class="form-control @error('address') is-invalid @enderror"
                        placeholder="Enter full address">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save Client
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection
