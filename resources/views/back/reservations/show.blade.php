{{-- resources/views/back/reservations/show.blade.php --}}
@extends('back.layout')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 fw-bold mb-4 text-success text-center">Reservation #{{ $reservation->id }}</h1>
                    @if (session('success'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">User</label>
                            <p class="form-control">{{ $reservation->user->name ?? 'Unknown' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Product</label>
                            <p class="form-control">{{ \App\Http\Controllers\ReservationController::getProductName($reservation->product_id) }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Quantity</label>
                            <p class="form-control">{{ $reservation->quantity }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Reserved Until</label>
                            <p class="form-control">{{ $reservation->reserved_until->format('Y-m-d H:i') }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control">
                                <span class="badge bg-info">{{ ucfirst($reservation->status->value) }}</span>
                            </p>
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-4">
                        <div class="d-flex gap-2 w-100 w-md-auto">
                            <a href="{{ route('back.reservations.index') }}" class="btn btn-secondary w-100 w-md-auto">Back</a>
                            <a href="{{ route('back.reservations.edit', $reservation) }}" class="btn btn-warning w-100 w-md-auto">Edit</a>
                        </div>
                        <form action="{{ route('back.reservations.destroy', $reservation) }}" method="POST" class="d-inline w-100 w-md-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this reservation?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection