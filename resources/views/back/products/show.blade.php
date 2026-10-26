{{-- resources/views/back/products/show.blade.php --}}
@extends('back.layout')

@section('title', 'Product Details')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-9">

            {{-- Header --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <div>
                    <h1 class="h3 fw-bold text-success mb-1">
                        <i class="bi bi-box-seam me-1"></i>
                        Product Details
                    </h1>
                    <p class="text-muted small mb-0">
                        Created on {{ optional($product->created_at)->format('M d, Y H:i') ?? '—' }}
                    </p>
                </div>
                <div>
                    @if ($product->is_available)
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i>Available
                        </span>
                    @else
                        <span class="badge bg-danger fs-6 px-3 py-2">
                            <i class="bi bi-x-circle me-1"></i>Unavailable
                        </span>
                    @endif
                </div>
            </div>

            {{-- Alerts --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="bi bi-exclamation-triangle me-1"></i>Errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-3 p-md-4">

                    {{-- Product overview --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-5">
                            <div class="rounded-3 overflow-hidden bg-light d-flex align-items-center justify-content-center"
                                 style="height: 260px;">
                                @if ($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}"
                                         alt="{{ $product->name }}"
                                         class="img-fluid w-100 h-100" style="object-fit: cover;">
                                @else
                                    <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h2 class="h4 fw-bold text-success mb-2">{{ $product->name }}</h2>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-tag me-1"></i>
                                {{ optional($product->category)->name ?? 'Uncategorized' }}
                            </p>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold mb-1">Price</label>
                                    <input type="text" class="form-control"
                                           value="{{ number_format((float) $product->price, 2) }} DT" readonly>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold mb-1">Stock Quantity</label>
                                    <input type="text" class="form-control"
                                           value="{{ $product->stock_quantity }}" readonly>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold mb-1">Availability</label>
                                    <input type="text" class="form-control"
                                           value="{{ $product->is_available ? 'Available' : 'Unavailable' }}" readonly>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold mb-1">Orders</label>
                                    <input type="text" class="form-control"
                                           value="{{ $product->orders->count() }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <h2 class="h6 text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-info-circle me-1"></i>Description
                    </h2>
                    <p class="text-muted mb-4">{{ $product->description ?: 'No description provided.' }}</p>

                    {{-- Specifications --}}
                    @if ($product->specifications && is_array($product->specifications) && count($product->specifications))
                        <h2 class="h6 text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                            <i class="bi bi-list-check me-1"></i>Specifications
                        </h2>
                        <div class="row g-3 mb-4">
                            @foreach ($product->specifications as $key => $value)
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ ucfirst($key) }}</label>
                                    <input type="text" class="form-control"
                                           value="{{ is_array($value) ? implode(', ', $value) : $value }}" readonly>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Recycling process --}}
                    <h2 class="h6 text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-arrow-repeat me-1"></i>Recycling Process
                    </h2>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Method</label>
                            <input type="text" class="form-control"
                                   value="{{ optional($product->recyclingProcess)->method ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Source Waste</label>
                            <input type="text" class="form-control"
                                   value="{{ optional(optional($product->recyclingProcess)->waste)->type ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Output Quantity (kg)</label>
                            <input type="text" class="form-control"
                                   value="{{ optional($product->recyclingProcess)->output_quantity ?? 'N/A' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Output Quality</label>
                            <input type="text" class="form-control"
                                   value="{{ optional($product->recyclingProcess)->output_quality ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    {{-- Orders --}}
                    <h2 class="h6 text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-cart me-1"></i>Orders
                    </h2>
                    @if ($product->orders->isEmpty())
                        <p class="text-muted mb-4">No orders for this product yet.</p>
                    @else
                        <div class="table-responsive mb-4">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Order </th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product->orders->sortByDesc('order_date')->take(10) as $order)
                                        <tr>
                                            <td>    {{ $order->id }}</td>
                                            <td>{{ $order->quantity }}</td>
                                            <td>{{ number_format((float) $order->total_amount, 2) }} DT</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst(str_replace('_', ' ', $order->status ?? 'unknown')) }}
                                                </span>
                                            </td>
                                            <td>{{ optional($order->order_date)->format('M d, Y H:i') ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Metadata --}}
                    <h2 class="h6 text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                        <i class="bi bi-clock-history me-1"></i>Metadata
                    </h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Created At</label>
                            <input type="text" class="form-control"
                                   value="{{ optional($product->created_at)->format('M d, Y H:i') ?? 'Unknown' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Updated</label>
                            <input type="text" class="form-control"
                                   value="{{ optional($product->updated_at)->format('M d, Y H:i') ?? 'Unknown' }}" readonly>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                        <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-end">
                            <form action="{{ route('products.toggle-availability', $product->id) }}" method="POST"
                                  class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="bi bi-toggle-on me-1"></i>
                                    Mark as {{ $product->is_available ? 'Unavailable' : 'Available' }}
                                </button>
                            </form>
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil-square me-1"></i>Edit
                            </a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
