{{-- resources/views/back/donations/create.blade.php --}}
@extends('back.layout')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 font-weight-bold mb-4 text-success text-center">Create Donation</h1>
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
                    <form action="{{ route('back.donations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- User -->
                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-bold">User</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
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
                                        <option value="{{ $wasteCategory->id }}" {{ old('waste_category_id') == $wasteCategory->id ? 'selected' : '' }}>{{ $wasteCategory->name }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled selected>No waste categories available</option>
                                @endif
                            </select>
                            @error('waste_category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Item Name -->
                        <div class="mb-3">
                            <label for="item_name" class="form-label fw-bold">Item Name</label>
                            <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}" class="form-control" required>
                            @error('item_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Condition -->
                        <div class="mb-3">
                            <label for="condition" class="form-label fw-bold">Condition</label>
                            <select name="condition" id="condition" class="form-select" required>
                                <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>New</option>
                                <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>Used</option>
                                <option value="damaged" {{ old('condition') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                            </select>
                            @error('condition')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- AI Sentiment Preview -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">AI Sentiment Preview</label>
                            <div id="sentiment-preview" class="alert alert-info">
                                Enter description to analyze sentiment...
                            </div>
                        </div>
                        <!-- Images -->
                        <div class="mb-3">
                            <label for="images" class="form-label fw-bold">Images</label>
                            <input type="file" name="images[]" id="images" multiple class="form-control">
                            @error('images.*')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Pickup Required -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="pickup_required" id="pickup_required" class="form-check-input" value="1" {{ old('pickup_required') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="pickup_required">Pickup Required</label>
                            @error('pickup_required')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Pickup Address -->
                        <div class="mb-3">
                            <label for="pickup_address" class="form-label fw-bold">Pickup Address</label>
                            <input type="text" name="pickup_address" id="pickup_address" value="{{ old('pickup_address') }}" class="form-control">
                            @error('pickup_address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Buttons -->
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                            <a href="{{ route('back.donations.index') }}" class="btn btn-secondary w-100 w-md-auto">Cancel</a>
                            <button type="submit" class="btn btn-success w-100 w-md-auto">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // AI Sentiment Preview on Description Input
    document.getElementById('description').addEventListener('input', function() {
        const desc = this.value;
        const preview = document.getElementById('sentiment-preview');
        if (desc.length > 10) {  // Trigger after 10 chars
            fetch('{{ route('back.donations.analyze-sentiment') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({description: desc})
            })
            .then(res => res.json())
            .then(data => {
                const badgeClass = data.sentiment === 'positive' ? 'badge bg-success' : data.sentiment === 'negative' ? 'badge bg-danger' : 'badge bg-warning';
                preview.innerHTML = `<span class="${badgeClass}">${data.sentiment.charAt(0).toUpperCase() + data.sentiment.slice(1)}</span>`;
            })
            .catch(err => {
                preview.innerHTML = 'Error analyzing sentiment.';
                console.error(err);
            });
        } else {
            preview.innerHTML = 'Enter description to analyze sentiment...';
        }
    });
</script>
@endsection
