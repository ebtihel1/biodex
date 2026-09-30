{{-- resources/views/front/reservations/edit.blade.php --}}
@extends('front.layout')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-gradient text-dark" style="background: linear-gradient(to bottom right, #d4edda, #c3e6cb);">
    <div class="container py-3 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-3 p-md-4">
                        <h1 class="h3 fw-bold mb-4 text-success text-center">Edit Reservation #{{ $reservation->id }} 📅</h1>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success mb-4" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ $updateRoute ?? route('front.reservations.update', $reservation) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="user_id" class="form-label fw-semibold">User</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">Select User</option>
                                    @foreach(\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $reservation->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="product_id" class="form-label fw-semibold">Product</label>
                                <select name="product_id" id="product_id" class="form-select" required>
                                    <option value="">Select Product</option>
                                    @if(isset($products) && !empty($products))
                                        @foreach($products as $id => $product)
                                            <option value="{{ $id }}" {{ old('product_id', $reservation->product_id) == $id ? 'selected' : '' }}>{{ $product['name'] }} ({{ $product['price'] }})</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No products available</option>
                                    @endif
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label fw-semibold">Quantity</label>
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $reservation->quantity) }}" min="1" class="form-control" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="reserved_until" class="form-label fw-semibold">Reserved Until</label>
                                <input type="datetime-local" name="reserved_until" id="reserved_until" value="{{ old('reserved_until', $reservation->reserved_until->format('Y-m-d\TH:i')) }}" class="form-control" required>
                                @error('reserved_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label fw-semibold">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="">Select Status</option>
                                    @foreach(\App\Enums\ReservationStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ old('status', $reservation->status) == $status->value ? 'selected' : '' }}>{{ $status->value }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                <a href="{{ $showRoute ?? route('front.reservations.show', $reservation) }}" class="btn btn-secondary w-100 w-md-auto">Cancel</a>
                                <button type="submit" class="btn btn-success w-100 w-md-auto">Update Reservation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Bootstrap validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
@endsection