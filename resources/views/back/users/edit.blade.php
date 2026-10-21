@extends('back.layout')

@section('title', 'Edit User')

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
                        <i class="bi bi-pencil-square me-2"></i> Edit User
                    </h1>
                    <p class="text-muted mb-0">Update the user information</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i> {{ $user->name }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('back.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium">
                                <i class="bi bi-person me-2 text-success"></i>Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
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
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle me-1"></i>
                            Leave the password fields empty to keep the current password.
                        </div>

                        <div class="row">
                            <!-- Password -->
                            <div class="col-md-6 mb-4">
                                <label for="password" class="form-label fw-medium">
                                    <i class="bi bi-lock me-2 text-success"></i>New Password
                                </label>
                                <input type="password"
                                       name="password"
                                       id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="At least 8 characters">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password confirmation -->
                            <div class="col-md-6 mb-4">
                                <label for="password_confirmation" class="form-label fw-medium">
                                    <i class="bi bi-lock-fill me-2 text-success"></i>Confirm Password
                                </label>
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       class="form-control"
                                       placeholder="Repeat the password">
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
                                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
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
                                       value="{{ old('phone', $user->phone) }}"
                                       maxlength="15">
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
                                      maxlength="255">{{ old('address', $user->address) }}</textarea>
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
                                   value="{{ old('city', $user->city) }}"
                                   maxlength="100">
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

                            <div id="imagePreview" class="mt-3 d-flex align-items-center gap-3">
                                <img id="previewImg"
                                     src="{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}"
                                     alt="Preview"
                                     class="img-thumbnail rounded-circle"
                                     style="width: 120px; height: 120px; object-fit: cover; {{ $user->avatar ? '' : 'display: none;' }}">
                                @if ($user->avatar)
                                    <div class="form-check">
                                        <input type="checkbox" name="remove_avatar" id="remove_avatar" value="1" class="form-check-input">
                                        <label for="remove_avatar" class="form-check-label text-danger">Remove current picture</label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('back.users.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-circle me-2"></i> Update User
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
            const img = document.getElementById('previewImg');
            img.src = ev.target.result;
            img.style.display = 'block';
            const remove = document.getElementById('remove_avatar');
            if (remove) remove.checked = false;
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
