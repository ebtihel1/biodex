{{-- resources/views/wastecategory/show.blade.php --}}
@extends('back.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 fw-bold mb-4 text-success text-center">Waste Category Details</h1>

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
                        <label class="form-label fw-bold">Category Name</label>
                        <input type="text" class="form-control" value="{{ $category->name }}" readonly>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" rows="4" readonly>{{ $category->description ?: 'No description provided.' }}</textarea>
                    </div>

                    {{-- Recycling instructions --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Recycling Instructions</label>
                        <textarea class="form-control" rows="5" readonly>{{ $category->recycling_instructions ?: 'No recycling instructions provided.' }}</textarea>
                    </div>

                    {{-- Created at --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Created At</label>
                        <input type="text" class="form-control" value="{{ optional($category->created_at)->format('d M Y, H:i') ?? 'Unknown' }}" readonly>
                    </div>

                    {{-- Updated at --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Last Updated</label>
                        <input type="text" class="form-control" value="{{ optional($category->updated_at)->format('d M Y, H:i') ?? 'Unknown' }}" readonly>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                        <a href="{{ route('waste_categories.index') }}" class="btn btn-secondary w-100 w-md-auto">Back to list</a>
                        <a href="{{ route('waste_categories.edit', $category->id) }}" class="btn btn-success w-100 w-md-auto">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection