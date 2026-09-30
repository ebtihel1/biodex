{{-- resources/views/back/reservations/edit.blade.php --}}
@extends('back.layout')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 fw-bold mb-4 text-success text-center">Edit Reservation #{{ $reservation->id }}</h1>
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4" role="alert">
                            <ul class="list-unstyled mb-0">
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
                    <form action="{{ route('back.reservations.update', $reservation) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- User -->
                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-bold">User</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $reservation->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Product -->
                        <div class="mb-3">
                            <label for="product_id" class="form-label fw-bold">Product</label>
                            <select name="product_id" id="product_id" class="form-select" required>
                                @if(isset($products) && !empty($products))
                                    @foreach($products as $id => $product)
                                        <option value="{{ $id }}" {{ old('product_id', $reservation->product_id) == $id ? 'selected' : '' }}>{{ $product['name'] }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled selected>No products available</option>
                                @endif
                            </select>
                            @error('product_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Quantity -->
                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-bold">Quantity</label>
                            <input type="number" min="1" name="quantity" id="quantity" value="{{ old('quantity', $reservation->quantity) }}" class="form-control" required>
                            @error('quantity')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Reserved Until -->
                        <div class="mb-3">
                            <label for="reserved_until" class="form-label fw-bold">Reserved Until</label>
                            <input type="datetime-local" name="reserved_until" id="reserved_until" value="{{ old('reserved_until', $reservation->reserved_until->format('Y-m-d\TH:i')) }}" class="form-control" required>
                            @error('reserved_until')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                @foreach(\App\Enums\ReservationStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ old('status', $reservation->status) == $status->value ? 'selected' : '' }}>{{ ucfirst($status->value) }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Buttons -->
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                            <a href="{{ route('back.reservations.index') }}" class="btn btn-secondary w-100 w-md-auto">Cancel</a>
                            <button type="submit" class="btn btn-warning w-100 w-md-auto">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection