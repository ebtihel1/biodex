{{-- resources/views/collectionpoints/show.blade.php --}}
@extends('back.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 fw-bold mb-4 text-success text-center">Collection Point Details</h1>

                    {{-- Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4" role="alert">
                            <ul class="list-unstyled mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Success --}}
                    @if (session('success'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Name --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Collection Point Name</label>
                        <input type="text" class="form-control" value="{{ $collectionPoint->name }}" readonly>
                    </div>

                    {{-- Address --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Address</label>
                        <textarea class="form-control" rows="3" readonly>{{ $collectionPoint->address ?: 'No address provided.' }}</textarea>
                    </div>

                    {{-- City --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">City</label>
                        <input type="text" class="form-control" value="{{ $collectionPoint->city ?: 'Undefined' }}" readonly>
                    </div>

                    {{-- Contact Phone --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contact Phone</label>
                        <input type="text" class="form-control" value="{{ $collectionPoint->contact_phone ?: 'Undefined' }}" readonly>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <input type="text" class="form-control" value="{{ ucfirst($collectionPoint->status ?? 'undefined') }}" readonly>
                    </div>

                    {{-- Created at --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Created At</label>
                        <input type="text" class="form-control" value="{{ optional($collectionPoint->created_at)->format('d M Y, H:i') ?? 'Unknown' }}" readonly>
                    </div>

                    {{-- Updated at --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Last Updated</label>
                        <input type="text" class="form-control" value="{{ optional($collectionPoint->updated_at)->format('d M Y, H:i') ?? 'Unknown' }}" readonly>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                        <a href="{{ route('collectionpoints.index') }}" class="btn btn-secondary w-100 w-md-auto">Back to list</a>
                        <a href="{{ route('collectionpoints.edit', $collectionPoint->id) }}" class="btn btn-success w-100 w-md-auto">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection