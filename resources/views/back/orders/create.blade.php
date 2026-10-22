{{-- resources/views/back/orders/create.blade.php --}}
@extends('back.layout')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4 text-center">
                    <h1 class="h3 fw-bold mb-4 text-success">Create Order</h1>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Orders are now created by customers through the shopping cart. Admins can only view, edit status, and manage existing orders.
                    </div>
                    <a href="{{ $indexRoute }}" class="btn btn-secondary">Back to Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
