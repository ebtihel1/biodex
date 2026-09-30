@extends('front.layout')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-center text-success">Edit Waste</h1>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('front.wastes.update', $waste->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                
                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <input type="text" name="type" id="type" 
                           class="form-control @error('type') is-invalid @enderror" 
                           value="{{ old('type', $waste->type) }}">
                    @error('type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

               
                <div class="mb-3">
                    <label for="weight" class="form-label">Weight (kg)</label>
                    <input type="number" step="0.01" name="weight" id="weight" 
                           class="form-control @error('weight') is-invalid @enderror" 
                           value="{{ old('weight', $waste->weight) }}">
                    @error('weight')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

               
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="">Select Status</option>
                        <option value="recyclable" {{ old('status', $waste->status) == 'recyclable' ? 'selected' : '' }}>Recyclable</option>
                        <option value="reusable" {{ old('status', $waste->status) == 'reusable' ? 'selected' : '' }}>Reusable</option>
                        <option value="non-recyclable" {{ old('status', $waste->status) == 'non-recyclable' ? 'selected' : '' }}>Non Recyclable</option>
                    </select>
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

              
                <div class="mb-3">
                    <label for="waste_category_id" class="form-label">Category</label>
                    <select name="waste_category_id" id="waste_category_id" 
                            class="form-select @error('waste_category_id') is-invalid @enderror">
                        <option value="">Select Category</option>
                        @foreach(App\Models\WasteCategory::all() as $category)
                            <option value="{{ $category->id }}" 
                                {{ old('waste_category_id', $waste->waste_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('waste_category_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" 
                              class="form-control @error('description') is-invalid @enderror" 
                              rows="3">{{ old('description', $waste->description) }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

               
                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" name="image" id="image" 
                           class="form-control @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                    @if($waste->image_path)
                        <div class="mt-2">
                            <p>Current Image:</p>
                            <img src="{{ asset('storage/' . $waste->image_path) }}" 
                                 alt="Waste Image" class="img-fluid" style="max-width: 200px;">
                        </div>
                    @endif
                </div>

               
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                <input type="hidden" name="collection_point_id" value="2">

                <button type="submit" class="btn btn-primary">Update Waste</button>
                <a href="{{ route('front.wastes.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
