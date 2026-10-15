{{-- resources/views/back/donations/show.blade.php --}}
@extends('back.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 fw-bold mb-4 text-success text-center">Donation {{ $donation->id }}</h1>

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

                    <!-- User -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">User</label>
                        <input type="text" class="form-control" value="{{ $donation->user->name ?? 'Unknown' }}" readonly>
                    </div>

                    <!-- Waste Category -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Waste Category</label>
                        <input type="text" class="form-control" value="{{ $donation->waste->category->name ?? 'N/A' }}" readonly>
                    </div>

                    <!-- Item Name -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Item Name</label>
                        <input type="text" class="form-control" value="{{ $donation->item_name ?? 'N/A' }}" readonly>
                    </div>

                    <!-- Condition -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Condition</label>
                        <input type="text" class="form-control" value="{{ ucfirst($donation->condition ?? 'N/A') }}" readonly>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <div class="form-control bg-light">
                            <span class="badge bg-info">{{ ucfirst($donation->status->value ?? $donation->status) }}</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" rows="4" readonly>{{ $donation->description ?: 'No description provided.' }}</textarea>
                    </div>

                    <!-- Pickup Required -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pickup Required</label>
                        <input type="text" class="form-control" value="{{ $donation->pickup_required ? 'Yes' : 'No' }}" readonly>
                    </div>

                    <!-- Pickup Address -->
                    @if($donation->pickup_required && $donation->pickup_address)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pickup Address</label>
                            <input type="text" class="form-control" value="{{ $donation->pickup_address }}" readonly>
                        </div>
                    @endif

                    <!-- Images -->
                    @if($donation->images && is_array($donation->images) && count($donation->images))
                        <div class="mb-3">
                            <label class="form-label fw-bold">Images</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($donation->images as $imagePath)
                                    <a href="{{ asset('storage/' . $imagePath) }}" target="_blank" rel="noopener">
                                        <img src="{{ asset('storage/' . $imagePath) }}"
                                             alt="Donation image"
                                             class="img-thumbnail"
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Created at -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Created At</label>
                        <input type="text" class="form-control" value="{{ optional($donation->created_at)->format('d M Y, H:i') ?? 'Unknown' }}" readonly>
                    </div>

                    <!-- Updated at -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Last Updated</label>
                        <input type="text" class="form-control" value="{{ optional($donation->updated_at)->format('d M Y, H:i') ?? 'Unknown' }}" readonly>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                        <a href="{{ route('back.donations.index') }}" class="btn btn-secondary w-100 w-md-auto">Back</a>
                        <a href="{{ $editRoute }}" class="btn btn-warning w-100 w-md-auto">Edit</a>
                        <form action="{{ $destroyRoute }}" method="POST" class="d-inline w-100 w-md-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this donation?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection