{{-- resources/views/back/orders/index.blade.php --}}
@extends('back.layout')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 fw-bold mb-4 text-success text-center">Manage Orders</h1>
                    @if (session('success'))
                        <div id="success-message" class="alert alert-success mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                        <script>
                            setTimeout(() => {
                                document.getElementById('success-message').style.display = 'none';
                            }, 3000);
                        </script>
                    @endif
                    
                    <!-- Add New Order Button -->
                    <div class="text-start mb-4">
                        <a href="{{ $createRoute }}" class="btn btn-success">Add New Order</a>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('back.orders.index') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label">Product Search</label>
                                <input type="text" name="product_search" value="{{ request('product_search') }}" class="form-control" placeholder="Search products...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    @foreach(\App\Enums\OrderStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>{{ ucfirst($status->value) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex flex-column flex-md-row gap-2 h-100 align-items-start align-items-md-end justify-content-md-end">
                                    <button type="submit" class="btn btn-success w-100 w-md-auto">Filter</button>
                                    <a href="{{ route('back.orders.index') }}" class="btn btn-outline-secondary w-100 w-md-auto">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($orders->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No orders found.</td>
                                    </tr>
                                @else
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->user->name ?? 'Unknown' }}</td>
                                            <td>{{ $order->product->name ?? 'N/A' }}</td>
                                            <td>{{ $order->quantity }}</td>
                                            <td>${{ number_format($order->total_amount, 2) }}</td>
                                            <td><span class="badge bg-info">{{ ucfirst($order->status->value ?? $order->status) }}</span></td>
                                            <td>{{ $order->order_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('back.orders.show', $order) }}" class="btn btn-warning btn-sm">View</a>
                                                    <a href="{{ route('back.orders.edit', $order) }}" class="btn btn-outline-success btn-sm">Edit</a>
                                                    <form action="{{ route('back.orders.destroy', $order) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?')">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        @if ($orders->hasPages())
                            <nav aria-label="Orders pagination">
                                <ul class="pagination justify-content-center mt-4 mb-0 shadow-sm">
                                    {{-- Previous Page Link --}}
                                    @if ($orders->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d; padding: 10px 20px;">
                                                <i class="fas fa-chevron-left me-1"></i>Previous
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->previousPageUrl() }}" 
                                               style="background-color: #10b981; border-color: #10b981; color: white; font-weight: bold; padding: 10px 20px; transition: all 0.3s ease;">
                                                <i class="fas fa-chevron-left me-1"></i>Previous
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                        @if ($page == $orders->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link" style="background-color: #10b981; border-color: #10b981; font-weight: bold; padding: 10px 15px;">
                                                    {{ $page }}
                                                </span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}" 
                                                   style="background-color: white; border-color: #10b981; color: #10b981; font-weight: bold; padding: 10px 15px; transition: all 0.3s ease;">
                                                    {{ $page }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($orders->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->nextPageUrl() }}" 
                                               style="background-color: #10b981; border-color: #10b981; color: white; font-weight: bold; padding: 10px 20px; transition: all 0.3s ease;">
                                                Next<i class="fas fa-chevron-right ms-1"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link" style="background-color: #f8f9fa; border-color: #dee2e6; color: #6c757d; padding: 10px 20px;">
                                                Next<i class="fas fa-chevron-right ms-1"></i>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Pagination Hover Effects */
    .page-link:hover:not(.disabled):not(.active) {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    /* Responsive Pagination */
    @media (max-width: 576px) {
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
        .page-link {
            padding: 8px 15px !important;
            font-size: 0.875rem;
        }
    }
</style>
@endsection
