@extends('front.global')

@section('title', 'Edit Profile')

@section('content')
<style>
    body:has(.biodex-profile-edit) .hero-header {
        display: none;
    }

    body:has(.biodex-profile-edit) .navbar {
        background-color: #17634d !important;
        box-shadow: 0 2px 12px rgba(18, 54, 43, 0.16);
    }

    .biodex-profile-edit {
        --profile-forest: #17634d;
        --profile-ink: #17352d;
        --profile-muted: #70817b;
        --profile-line: #e5ece8;
        max-width: 760px;
        margin: 0 auto;
        padding: 38px 24px 56px;
        color: var(--profile-ink);
    }

    .profile-edit-eyebrow {
        margin: 0 0 9px;
        color: #a35d2a;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .biodex-profile-edit h1 {
        margin: 0;
        color: var(--profile-ink);
        font-size: 2rem;
        font-weight: 700;
    }

    .profile-edit-subtitle {
        margin: 7px 0 0;
        color: var(--profile-muted);
    }

    .profile-edit-card {
        margin-top: 26px;
        border: 1px solid var(--profile-line);
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 16px 42px rgba(23, 53, 45, 0.08);
        padding: 38px 42px;
    }

    .profile-edit-card .form-label {
        font-weight: 600;
        color: var(--profile-ink);
        margin-bottom: 0.45rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .profile-edit-card .form-label i {
        color: var(--profile-forest);
    }

    .profile-edit-card .form-control {
        border: 1.5px solid var(--profile-line);
        border-radius: 8px;
        padding: 11px 14px;
        background: #fbfdfc;
        transition: border-color 160ms ease, box-shadow 160ms ease;
    }

    .profile-edit-card .form-control:focus {
        border-color: var(--profile-forest);
        box-shadow: 0 0 0 0.2rem rgba(23, 99, 77, 0.12);
        background: #fff;
    }

    .profile-edit-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 28px;
        padding-top: 24px;
        border-top: 1px solid var(--profile-line);
    }

    .profile-save-button,
    .profile-cancel-link {
        display: inline-flex;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 160ms ease, border-color 160ms ease;
    }

    .profile-save-button {
        border: 1px solid var(--profile-forest);
        background: var(--profile-forest);
        color: #fff;
        cursor: pointer;
    }

    .profile-save-button:hover {
        background: #104b39;
        border-color: #104b39;
        color: #fff;
    }

    .profile-cancel-link {
        border: 1px solid var(--profile-line);
        background: #fff;
        color: var(--profile-forest);
    }

    .profile-cancel-link:hover {
        border-color: #b9d1c4;
        background: #f3f8f5;
        color: #104b39;
    }

    .profile-edit-feedback {
        display: none;
        padding: 0.9rem 1.2rem;
        border-radius: 8px;
        margin-bottom: 1.4rem;
        align-items: center;
        font-size: 0.92rem;
    }

    .profile-edit-feedback.success {
        background-color: rgba(25, 135, 84, 0.1);
        border: 1px solid #198754;
        color: #198754;
    }

    .profile-edit-feedback.error {
        background-color: rgba(220, 53, 69, 0.1);
        border: 1px solid #dc3545;
        color: #dc3545;
    }

    .profile-avatar-editor {
        display: flex;
        align-items: center;
        gap: 22px;
        margin-bottom: 28px;
        padding-bottom: 28px;
        border-bottom: 1px solid var(--profile-line);
    }

    .profile-avatar-preview {
        position: relative;
        display: grid;
        width: 96px;
        height: 96px;
        flex-shrink: 0;
        place-items: center;
        overflow: hidden;
        border: 1px solid var(--profile-line);
        border-radius: 50%;
        background: #f2d5a0;
        color: #674329;
        font-size: 2rem;
        font-weight: 700;
    }

    .profile-avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-avatar-meta {
        flex: 1;
        min-width: 0;
    }

    .profile-avatar-meta h4 {
        margin: 0 0 4px;
        color: var(--profile-ink);
        font-size: 1rem;
        font-weight: 700;
    }

    .profile-avatar-meta p {
        margin: 0 0 12px;
        color: var(--profile-muted);
        font-size: 0.82rem;
    }

    .profile-avatar-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .profile-avatar-upload,
    .profile-avatar-remove {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 160ms ease, border-color 160ms ease;
    }

    .profile-avatar-upload {
        border: 1px solid var(--profile-line);
        background: #fff;
        color: var(--profile-forest);
    }

    .profile-avatar-upload:hover {
        border-color: #b9d1c4;
        background: #f3f8f5;
        color: #104b39;
    }

    .profile-avatar-remove {
        border: 1px solid #f0d9d4;
        background: #fff;
        color: #a33c32;
    }

    .profile-avatar-remove:hover {
        border-color: #e9bdb5;
        background: #fff7f5;
        color: #862d25;
    }

    @media (max-width: 700px) {
        .biodex-profile-edit {
            padding: 30px 16px 40px;
        }

        .profile-avatar-editor {
            flex-direction: column;
            align-items: flex-start;
        }

        .biodex-profile-edit h1 {
            font-size: 1.6rem;
        }

        .profile-edit-card {
            padding: 26px 20px;
        }

        .profile-edit-actions {
            flex-direction: column;
        }

        .profile-edit-actions .profile-save-button,
        .profile-edit-actions .profile-cancel-link {
            width: 100%;
        }
    }
</style>

<div class="biodex-profile-edit">
    <header>
        <p class="profile-edit-eyebrow">Biodex / Personal space</p>
        <h1>Edit Profile</h1>
        <p class="profile-edit-subtitle">Update your personal information and contact details.</p>
    </header>

    <section class="profile-edit-card" aria-label="Edit profile form">
        @if (session('success'))
            <div class="profile-edit-feedback success" style="display: flex;">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="profile-edit-feedback error" style="display: flex;">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" id="profileEditForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="profile-avatar-editor">
                <div class="profile-avatar-preview" id="avatarPreview">
                    @if ($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile picture" id="avatarImage">
                    @else
                        <span id="avatarInitial">{{ mb_substr($user->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="profile-avatar-meta">
                    <h4>Profile picture</h4>
                    <p>JPG, PNG or WEBP — up to 2 MB.</p>
                    <div class="profile-avatar-actions">
                        <label for="avatar" class="profile-avatar-upload">
                            <i class="bi bi-upload" aria-hidden="true"></i>
                            Choose picture
                        </label>
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="d-none">
                        @if ($user->avatar)
                            <button type="button" class="profile-avatar-remove" id="avatarRemove">
                                <i class="bi bi-trash" aria-hidden="true"></i>
                                Remove
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">
                    <i class="bi bi-person" aria-hidden="true"></i>
                    Full name
                </label>
                <input type="text" class="form-control" name="name" id="name"
                       value="{{ old('name', $user->name) }}" required maxlength="255"
                       placeholder="Enter your full name">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope" aria-hidden="true"></i>
                    Email address
                </label>
                <input type="email" class="form-control" name="email" id="email"
                       value="{{ old('email', $user->email) }}" required maxlength="255"
                       placeholder="Enter your email">
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">
                    <i class="bi bi-telephone" aria-hidden="true"></i>
                    Phone
                </label>
                <input type="text" class="form-control" name="phone" id="phone"
                       value="{{ old('phone', $user->phone) }}" maxlength="15"
                       placeholder="Enter your phone number">
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">
                    <i class="bi bi-geo-alt" aria-hidden="true"></i>
                    Address
                </label>
                <textarea class="form-control" name="address" id="address" rows="2"
                          maxlength="255" placeholder="Enter your address">{{ old('address', $user->address) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">
                    <i class="bi bi-building" aria-hidden="true"></i>
                    City
                </label>
                <input type="text" class="form-control" name="city" id="city"
                       value="{{ old('city', $user->city) }}" maxlength="100"
                       placeholder="Enter your city">
            </div>

            <div class="profile-edit-actions">
                <button type="submit" class="profile-save-button">
                    <i class="bi bi-check-lg" aria-hidden="true"></i>
                    Save changes
                </button>
                <a href="{{ route('profile.view') }}" class="profile-cancel-link">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                    Cancel
                </a>
            </div>
        </form>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('avatar');
        const preview = document.getElementById('avatarPreview');
        const removeBtn = document.getElementById('avatarRemove');

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
                const form = document.getElementById('profileEditForm');

                let hidden = document.getElementById('remove_avatar');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'remove_avatar';
                    hidden.id = 'remove_avatar';
                    hidden.value = '1';
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
