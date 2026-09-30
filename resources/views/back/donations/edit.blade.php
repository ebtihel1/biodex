{{-- resources/views/back/donations/edit.blade.php --}}
@extends('back.layout')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 fw-bold mb-4 text-success text-center">Edit Donation</h1>
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
                    <form action="{{ $updateRoute }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- User -->
                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-bold">User</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ ($donation->user_id ?? old('user_id')) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Waste Category -->
                        <div class="mb-3">
                            <label for="waste_category_id" class="form-label fw-bold">Waste Category</label>
                            <select name="waste_category_id" id="waste_category_id" class="form-select" required>
                                @if(isset($wasteCategories) && $wasteCategories->count() > 0)
                                    @foreach($wasteCategories as $wasteCategory)
                                        <option value="{{ $wasteCategory->id }}" {{ ($donation->waste->category->id ?? old('waste_category_id')) == $wasteCategory->id ? 'selected' : '' }}>{{ $wasteCategory->name }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No waste categories available</option>
                                @endif
                            </select>
                            @error('waste_category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Item Name -->
                        <div class="mb-3">
                            <label for="item_name" class="form-label fw-bold">Item Name</label>
                            <input type="text" name="item_name" id="item_name" value="{{ $donation->item_name ?? old('item_name') }}" class="form-control" required>
                            @error('item_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Condition -->
                        <div class="mb-3">
                            <label for="condition" class="form-label fw-bold">Condition</label>
                            <select name="condition" id="condition" class="form-select" required>
                                <option value="new" {{ ($donation->condition ?? old('condition')) == 'new' ? 'selected' : '' }}>New</option>
                                <option value="used" {{ ($donation->condition ?? old('condition')) == 'used' ? 'selected' : '' }}>Used</option>
                                <option value="damaged" {{ ($donation->condition ?? old('condition')) == 'damaged' ? 'selected' : '' }}>Damaged</option>
                            </select>
                            @error('condition')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control">{{ $donation->description ?? old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Existing Images -->
                        @if($donation->images && is_array($donation->images) && count($donation->images) > 0)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Current Images</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($donation->images as $imagePath)
                                        <img src="{{ Storage::url($imagePath) }}" alt="Donation Image" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <!-- New Images -->
                        <div class="mb-3">
                            <label for="images" class="form-label fw-bold">Add/Replace Images (Optional)</label>
                            <input type="file" name="images[]" id="images" multiple class="form-control">
                            <small class="text-muted">Upload new images to replace existing ones.</small>
                            @error('images.*')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Pickup Required -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="pickup_required" id="pickup_required" class="form-check-input" value="1" {{ ($donation->pickup_required ?? old('pickup_required')) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="pickup_required">Pickup Required</label>
                            @error('pickup_required')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Pickup Address -->
                        <div class="mb-3">
                            <label for="pickup_address" class="form-label fw-bold">Pickup Address</label>
                            <input type="text" name="pickup_address" id="pickup_address" value="{{ $donation->pickup_address ?? old('pickup_address') }}" class="form-control">
                            @error('pickup_address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Status (Admin-only field) -->
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                @foreach(\App\Enums\DonationStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ ($donation->status ?? old('status')) == $status->value ? 'selected' : '' }}>{{ ucfirst($status->value) }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Buttons -->
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                            <a href="{{ route($indexRoute) }}" class="btn btn-secondary w-100 w-md-auto">Cancel</a>
                            <button type="submit" class="btn btn-success w-100 w-md-auto">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
