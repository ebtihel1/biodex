@extends('back.layout')

@section('title', 'User Details')

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
                        <i class="bi bi-person-vcard me-2"></i> User Details
                    </h1>
                    <p class="text-muted mb-0">Overview of the user account</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('back.users.edit', $user->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 text-center">
                <div class="card-body p-4">
                    <div class="mx-auto mb-3" style="width: 120px; height: 120px;">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                 class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center"
                                 style="width: 120px; height: 120px; font-size: 3rem; font-weight: 700;">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-bold text-success mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    @php
                        $roleName = optional($user->role)->name ?? 'none';
                        $roleBadge = match ($roleName) {
                            'admin' => 'bg-danger',
                            'entreprise' => 'bg-primary',
                            'citoyen' => 'bg-success',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $roleBadge }}">{{ ucfirst($roleName) }}</span>
                    <p class="text-muted small mt-3 mb-0">
                        <i class="bi bi-calendar-check me-1"></i>
                        Member since {{ $user->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i> Account Information</h5>
                </div>
                <div class="card-body p-4">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted"><i class="bi bi-person me-1"></i> Full Name</dt>
                        <dd class="col-sm-8 fw-semibold">{{ $user->name }}</dd>

                        <dt class="col-sm-4 text-muted"><i class="bi bi-envelope me-1"></i> Email</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>

                        <dt class="col-sm-4 text-muted"><i class="bi bi-person-badge me-1"></i> Role</dt>
                        <dd class="col-sm-8">{{ ucfirst($roleName) }}</dd>

                        <dt class="col-sm-4 text-muted"><i class="bi bi-telephone me-1"></i> Phone</dt>
                        <dd class="col-sm-8">{{ $user->phone ?: '—' }}</dd>

                        <dt class="col-sm-4 text-muted"><i class="bi bi-geo-alt me-1"></i> Address</dt>
                        <dd class="col-sm-8">{{ $user->address ?: '—' }}</dd>

                        <dt class="col-sm-4 text-muted"><i class="bi bi-building me-1"></i> City</dt>
                        <dd class="col-sm-8">{{ $user->city ?: '—' }}</dd>

                        <dt class="col-sm-4 text-muted"><i class="bi bi-calendar-check me-1"></i> Joined</dt>
                        <dd class="col-sm-8">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-title { font-weight: 600; font-size: 1.75rem; }
</style>
@endsection
