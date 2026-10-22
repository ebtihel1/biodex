{{-- resources/views/back/orders/show.blade.php --}}
@extends('back.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 fw-bold mb-4 text-success text-center">Order {{ $order->id }}</h1>

                    {{-- Success --}}
                    @if (session('success'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row g-3">
                        {{-- User --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">User</label>
                            <p class="form-control">{{ $order->user->name ?? 'Unknown' }}</p>
                        </div>

                        {{-- Total Items --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Total Items</label>
                            <p class="form-control">{{ $order->total_quantity }}</p>
                        </div>

                        {{-- Total --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Total</label>
                            <p class="form-control">{{ number_format($order->total_amount ?? 0, 2) }} dt</p>
                        </div>

                        {{-- Status --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control">
                                <span class="badge bg-info">{{ ucfirst($order->status->value ?? $order->status) }}</span>
                            </p>
                        </div>

                        {{-- Order Date --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Order Date</label>
                            <p class="form-control">{{ $order->order_date?->format('Y-m-d') ?? 'N/A' }}</p>
                        </div>

                        {{-- Shipping Address --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Shipping Address</label>
                            <p class="form-control">{{ $order->shipping_address ?? 'N/A' }}</p>
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Payment Method</label>
                            <p class="form-control">{{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                        </div>

                        {{-- Tracking Number --}}
                        @if($order->tracking_number)
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold">Tracking Number</label>
                                <p class="form-control">{{ $order->tracking_number }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Order Items --}}
                    <div class="mt-4">
                        <h5 class="fw-bold mb-3">Order Items</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }} dt</td>
                                        <td>{{ number_format($item->subtotal, 2) }} dt</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-4">
                        <a href="{{ $indexRoute }}" class="btn btn-secondary w-100 w-md-auto">Back</a>
                        <a href="{{ $editRoute }}" class="btn btn-warning w-100 w-md-auto">Edit</a>
                        <form action="{{ $destroyRoute }}" method="POST" class="d-inline w-100 w-md-auto">
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