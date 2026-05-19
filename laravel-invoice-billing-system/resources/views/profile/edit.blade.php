@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
    @php
        use Illuminate\Support\Facades\Storage;
    @endphp
    <div class="py-section">
        <div class="mb-5">
            <h1 class="display-3 fw-bold text-dark mb-3">Profile Settings</h1>
            <p class="lead text-muted">Manage your profile information and organization logo</p>
        </div>

        <div class="row g-4">
            <!-- Profile Info Card -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-user me-2 text-primary"></i> Profile Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Name -->
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" required
                                    value="{{ old('name', $user->name) }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter your full name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" id="email" required
                                    value="{{ old('email', $user->email) }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Enter your email address">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Logo Upload -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-image me-2"></i> Organization Logo
                                </label>
                                
                                <!-- Current Logo Display -->
                                @if ($user->logo_path && Storage::disk('public')->exists($user->logo_path))
                                    <div class="mb-3">
                                        <div class="bg-light p-4 rounded text-center">
                                            <img src="{{ asset('storage/' . $user->logo_path) }}" alt="Current Logo"
                                                style="max-height: 120px; max-width: 100%;">
                                            <div class="mt-3">
                                                <form action="{{ route('profile.deleteLogo') }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Delete this logo?')">
                                                        <i class="fas fa-trash me-1"></i> Delete Logo
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-3 text-muted small">
                                        No logo uploaded yet. Upload one to display on your invoices.
                                    </div>
                                @endif

                                <!-- Upload Input -->
                                <div class="mb-3">
                                    <input type="file" name="logo" id="logo"
                                        accept="image/png,image/jpeg,image/jpg"
                                        class="form-control @error('logo') is-invalid @enderror"
                                        onchange="previewLogo(event)">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text small mt-2">
                                        <i class="fas fa-info-circle"></i>
                                        Accepted formats: PNG, JPG, JPEG. Max size: 2MB
                                    </div>
                                </div>

                                <!-- Logo Preview -->
                                <div id="logoPreview" class="mb-3" style="display: none;">
                                    <div class="bg-light p-4 rounded text-center">
                                        <img id="previewImage" alt="Logo Preview"
                                            style="max-height: 120px; max-width: 100%;">
                                        <p class="text-muted small mt-2">Preview of new logo</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-lightbulb me-2 text-warning"></i> Logo Guidelines
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <strong>Format:</strong> PNG or JPG
                            </li>
                            <li class="mb-3">
                                <strong>Size Limit:</strong> 2MB
                            </li>
                            <li class="mb-3">
                                <strong>Recommended:</strong> 200x100px or higher
                            </li>
                            <li class="mb-3">
                                <strong>Where It Appears:</strong>
                                <ul>
                                    <li>Invoice HTML view</li>
                                    <li>Invoice PDF downloads</li>
                                </ul>
                            </li>
                        </ul>

                        <div class="alert alert-info" role="alert">
                            <small>
                                <i class="fas fa-check me-1"></i>
                                Your logo will be displayed at the top-left of every invoice you create.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewLogo(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('logoPreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('logoPreview').style.display = 'none';
            }
        }
    </script>
@endsection
