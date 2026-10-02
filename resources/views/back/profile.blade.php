@extends('back.layout')

@section('content')
<style>
    .profile-shell {
        max-width: 1100px;
        margin: 0 auto;
    }

    .profile-card {
        border: none;
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 12px 32px rgba(15, 76, 66, 0.08);
        overflow: hidden;
    }

    .profile-hero {
        background: linear-gradient(135deg, #0f4f42, #0b2f2a);
        color: white;
        padding: 34px 30px;
    }

    .profile-avatar {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        margin-right: 20px;
    }

    .profile-label {
        font-size: 0.72rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.7);
        margin-bottom: 8px;
    }

    .profile-content {
        padding: 28px 30px 16px;
    }

    .info-block {
        border: 1px solid #e9eeeb;
        border-radius: 16px;
        background: #f8fbfa;
        padding: 20px;
        height: 100%;
    }

    .info-label {
        color: #6c7d78;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 8px;
    }

    .info-value {
        font-size: 1.05rem;
        font-weight: 600;
        color: #183b36;
        margin: 0;
        word-break: break-word;
    }

    .action-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 18px;
    }

    .btn-action {
        border-radius: 10px;
        padding: 10px 18px;
        font-weight: 600;
    }
</style>

<div class="profile-shell">
    <div class="profile-card">
        <div class="profile-hero d-flex align-items-center flex-wrap">
            <div class="profile-avatar">{{ strtoupper(substr(optional(Auth::user())->name ?? 'U', 0, 1)) }}</div>
            <div>
                <div class="profile-label">Account</div>
                <h2 class="mb-1">{{ Auth::user()->name ?? 'User' }}</h2>
                <div class="text-white-50">{{ Auth::user()->role?->name ?? 'Administrator' }}</div>
            </div>
        </div>

        <div class="profile-content">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-block">
                        <div class="info-label">Full name</div>
                        <p class="info-value">{{ Auth::user()->name ?? 'Not provided' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-block">
                        <div class="info-label">Email</div>
                        <p class="info-value">{{ Auth::user()->email ?? 'Not provided' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-block">
                        <div class="info-label">Phone</div>
                        <p class="info-value">{{ Auth::user()->phone ?? 'Not provided' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-block">
                        <div class="info-label">City</div>
                        <p class="info-value">{{ Auth::user()->city ?? 'Not provided' }}</p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="info-block">
                        <div class="info-label">Address</div>
                        <p class="info-value">{{ Auth::user()->address ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <div class="action-row">
                <a href="{{ url('/profile') }}" class="btn btn-success btn-action">View public profile</a>
                <a href="{{ route('settings.password') }}" class="btn btn-outline-success btn-action">Change password</a>
            </div>
        </div>
    </div>
</div>
@endsection
