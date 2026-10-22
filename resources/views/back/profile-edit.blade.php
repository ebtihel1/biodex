@extends('back.layout')

@section('content')
<style>
    .back-profile-edit {
        max-width: 820px;
        margin: 0 auto;
    }

    .back-profile-edit .card {
        border: none;
        border-radius: 22px;
        box-shadow: 0 12px 32px rgba(15, 76, 66, 0.08);
        overflow: hidden;
    }

    .back-profile-edit .card-header {
        background: linear-gradient(135deg, #0f4f42, #0b2f2a);
        color: #fff;
        padding: 26px 30px;
        border: none;
    }

    .back-profile-edit .card-body {
        padding: 30px;
    }

    .back-profile-edit .form-label {
        font-weight: 600;
        color: #183b36;
    }

    .back-profile-edit .form-control {
        border: 1px solid #dbe5e0;
        border-radius: 10px;
        padding: 11px 14px;
    }

    .back-profile-edit .form-control:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.12);
    }

    .back-profile-avatar {
        display: grid;
        width: 96px;
        height: 96px;
        flex-shrink: 0;
        place-items: center;
        overflow: hidden;
        border: 1px solid #dbe5e0;
        border-radius: 50%;
        background: #f2d5a0;
        color: #674329;
        font-size: 2rem;
        font-weight: 700;
    }

    .back-profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .back-profile-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 22px;
        border-top: 1px solid #e9eeeb;
    }
</style>

<div class="back-profile-edit">
    <div class="card">
        <div class="card-header">
            <h3 class="mb-1">Edit Profile</h3>
            <p class="mb-0 text-white-50">Update your personal information without leaving the back office.</p>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('back.profile.update') }}" method="POST" id="backProfileEditForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="d-flex align-items-center gap-4 flex-wrap mb-4 pb-4 border-bottom">
                    <div class="back-profile-avatar" id="backAvatarPreview">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile picture" id="backAvatarImage">
                        @else
                            <span id="backAvatarInitial">{{ mb_substr($user->name, 0, 1) }}</span>
                        @endif
                    </div>

                    <div>
                        <h5 class="mb-1">Profile picture</h5>
                        <p class="text-muted mb-2">JPG, PNG or WEBP - up to 2 MB.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <label for="avatar" class="btn btn-sm btn-outline-success mb-0">
                                <i class="bi bi-upload me-1"></i> Choose picture
                            </label>
                            <input type="file" name="avatar" id="avatar" accept="image/*" class="d-none">
                            @if ($user->avatar)
                                <button type="button" class="btn btn-sm btn-outline-danger" id="backAvatarRemove">
                                    <i class="bi bi-trash me-1"></i> Remove
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Full name</label>
                        <input type="text" class="form-control" name="name" id="name"
                               value="{{ old('name', $user->name) }}" required maxlength="255">
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" id="email"
                               value="{{ old('email', $user->email) }}" required maxlength="255">
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone"
                               value="{{ old('phone', $user->phone) }}" maxlength="15">
                    </div>

                    <div class="col-md-6">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" name="city" id="city"
                               value="{{ old('city', $user->city) }}" maxlength="100">
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" name="address" id="address" rows="2" maxlength="255">{{ old('address', $user->address) }}</textarea>
                    </div>
                </div>

                <div class="back-profile-actions">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i> Save changes
                    </button>
                    <a href="{{ route('back.profile') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('avatar');
        const preview = document.getElementById('backAvatarPreview');
        const removeBtn = document.getElementById('backAvatarRemove');

        if (input) {
            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) {
                    return;
                }

                const url = URL.createObjectURL(file);
                preview.innerHTML = '';
                const img = document.createElement('img');
                img.src = url;
                img.alt = 'Profile picture';
                preview.appendChild(img);
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                const form = document.getElementById('backProfileEditForm');

                let hidden = document.getElementById('remove_avatar');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'remove_avatar';
                    hidden.id = 'remove_avatar';
                    form.appendChild(hidden);
                }
                hidden.value = '1';

                if (input) {
                    input.value = '';
                }

                preview.innerHTML = '<span>{{ mb_substr($user->name, 0, 1) }}</span>';
                removeBtn.remove();
            });
        }
    });
</script>
@endsection
