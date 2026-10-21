@extends('back.layout')

@section('title', 'New User')

@section('content')
<div class="container-fluid px-0">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('back.users.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left me-2"></i> Back
                </a>
                <div>
                    <h1 class="page-title text-success mb-1">
                        <i class="bi bi-person-plus me-2"></i> New User
                    </h1>
                    <p class="text-muted mb-0">Create a new platform user</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i> User Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('back.users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium">
                                <i class="bi bi-person me-2 text-success"></i>Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Ex: John Doe"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-medium">
                                <i class="bi bi-envelope me-2 text-success"></i>Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="Ex: john.doe@example.com"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Password -->
                            <div class="col-md-6 mb-4">
                                <label for="password" class="form-label fw-medium">
                                    <i class="bi bi-lock me-2 text-success"></i>Password <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                       name="password"
                                       id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="At least 8 characters"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password confirmation -->
                            <div class="col-md-6 mb-4">
                                <label for="password_confirmation" class="form-label fw-medium">
                                    <i class="bi bi-lock-fill me-2 text-success"></i>Confirm Password <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       class="form-control"
                                       placeholder="Repeat the password"
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Role -->
                            <div class="col-md-6 mb-4">
                                <label for="role_id" class="form-label fw-medium">
                                    <i class="bi bi-person-badge me-2 text-success"></i>Role
                                </label>
                                <select name="role_id"
                                        id="role_id"
                                        class="form-select @error('role_id') is-invalid @enderror">
                                    <option value="">-- No role --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6 mb-4">
                                <label for="phone" class="form-label fw-medium">
                                    <i class="bi bi-telephone me-2 text-success"></i>Phone
                                </label>
                                <input type="text"
                                       name="phone"
                                       id="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}"
                                       maxlength="15"
                                       placeholder="Ex: +216 20 145 782">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <label for="address" class="form-label fw-medium">
                                <i class="bi bi-geo-alt me-2 text-success"></i>Address
                            </label>
                            <textarea name="address"
                                      id="address"
                                      class="form-control @error('address') is-invalid @enderror"
                                      rows="2"
                                      maxlength="255"
                                      placeholder="Ex: 15 rue de la Liberté">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="mb-4">
                            <label for="city" class="form-label fw-medium">
                                <i class="bi bi-building me-2 text-success"></i>City
                            </label>
                            <input type="text"
                                   name="city"
                                   id="city"
                                   class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city') }}"
                                   maxlength="100"
                                   placeholder="Ex: Tunis">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Avatar -->
                        <div class="mb-4">
                            <label for="avatar" class="form-label fw-medium">
                                <i class="bi bi-image me-2 text-success"></i>Profile Picture
                            </label>
                            <input type="file"
                                   name="avatar"
                                   id="avatar"
                                   class="form-control @error('avatar') is-invalid @enderror"
                                   accept="image/jpeg,image/png,image/jpg,image/webp">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Accepted formats: JPEG, PNG, JPG, WEBP. Max size: 2 MB</small>

                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('back.users.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-circle me-2"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-title { font-weight: 600; font-size: 1.75rem; }
.form-label { color: #495057; }
.form-control:focus, .form-select:focus { border-color: #198754; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25); }
</style>

<script>
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('previewImg').src = ev.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
