{{-- resources/views/back/orders/show.blade.php --}}
@extends('back.layout')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 fw-bold mb-4 text-success text-center">Order #{{ $order->id }}</h1>
                    @if (session('success'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">User</label>
                            <p class="form-control">{{ $order->user->name ?? 'Unknown' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Product</label>
                            <p class="form-control">{{ $order->product->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Quantity</label>
                            <p class="form-control">{{ $order->quantity }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Total</label>
                            <p class="form-control">${{ number_format($order->total_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control">
                                <span class="badge bg-info">{{ ucfirst($order->status->value ?? $order->status) }}</span>
                            </p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Order Date</label>
                            <p class="form-control">{{ $order->order_date?->format('Y-m-d') ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Shipping Address</label>
                            <p class="form-control">{{ $order->shipping_address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Payment Method</label>
                            <p class="form-control">{{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                        </div>
                        @if($order->tracking_number)
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Tracking Number</label>
                                <p class="form-control">{{ $order->tracking_number }}</p>
                            </div>
                        @endif
                    </div>
                    <!-- Buttons -->
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-4">
                        <a href="{{ route('back.orders.index') }}" class="btn btn-secondary w-100 w-md-auto">Back</a>
                        <a href="{{ route('back.orders.edit', $order) }}" class="btn btn-warning w-100 w-md-auto">Edit</a>
                        <form action="{{ route('back.orders.destroy', $order) }}" method="POST" class="d-inline w-100 w-md-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this order?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
