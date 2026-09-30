
@extends('front.layout')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-center text-success">Add New Waste Category</h1>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <!-- Formulaire de création -->
                    <form action="{{ route('front.waste-categories.store') }}" method="POST">
                        @csrf

                        <!-- Nom de la catégorie -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Name catégorie</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Recycling Instructions -->
                        <div class="mb-3">
                            <label for="recycling_instructions" class="form-label">Recycling_Instructions</label>
                            <textarea name="recycling_instructions" id="recycling_instructions" rows="2" class="form-control">{{ old('recycling_instructions') }}</textarea>
                            @error('recycling_instructions')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('front.waste-categories.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                Add
                            </button>
                        </div>
                    </form>
                    <!-- Fin formulaire -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
