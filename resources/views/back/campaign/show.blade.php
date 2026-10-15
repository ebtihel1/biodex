{{-- resources/views/back/campaign/show.blade.php --}}
@extends('back.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card shadow">
                <div class="card-body p-3 p-md-4">
                    <h1 class="h3 fw-bold mb-4 text-success text-center">Campaign #{{ $campaign->id }}</h1>

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

                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" class="form-control" value="{{ $campaign->title ?? 'N/A' }}" readonly>
                    </div>

                    <!-- Created by -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Created by</label>
                        <input type="text" class="form-control" value="{{ $campaign->user->name ?? 'Unknown' }}" readonly>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <div class="form-control bg-light">
                            @php
                                $statusClass = match ($campaign->status) {
                                    'active' => 'bg-success',
                                    'draft'  => 'bg-warning text-dark',
                                    'closed' => 'bg-secondary',
                                    default  => 'bg-info',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($campaign->status ?? 'N/A') }}</span>
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Start Date</label>
                            <input type="text" class="form-control" value="{{ $campaign->start_date?->format('d M Y') ?? 'N/A' }}" readonly>
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">End Date</label>
                            <input type="text" class="form-control" value="{{ $campaign->end_date?->format('d M Y') ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <!-- Registration Deadline -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Registration Deadline</label>
                        <input type="text" class="form-control" value="{{ $campaign->deadline_registration?->format('d M Y') ?? 'N/A' }}" readonly>
                    </div>

                    <!-- City / Region -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">City</label>
                            <input type="text" class="form-control" value="{{ $campaign->city ?? 'N/A' }}" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Region</label>
                            <input type="text" class="form-control" value="{{ $campaign->region ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <!-- Participants Count -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Participants</label>
                        <input type="text" class="form-control" value="{{ $campaign->participants_count ?? 0 }}" readonly>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" rows="5" readonly>{{ $campaign->description ?: 'No description provided.' }}</textarea>
                    </div>

                    <!-- Image -->
                    @if($campaign->image)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Image</label>
                            <div>
                                @php
                                    $imageUrl = \Illuminate\Support\Str::startsWith($campaign->image, ['http://', 'https://'])
                                        ? $campaign->image
                                        : asset('storage/' . $campaign->image);
                                @endphp
                                <a href="{{ $imageUrl }}" target="_blank" rel="noopener">
                                    <img src="{{ $imageUrl }}"
                                         alt="Campaign image"
                                         class="img-thumbnail"
                                         style="max-width: 100%; max-height: 300px; object-fit: cover;">
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Participants List -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Participants List</label>
                        @if ($campaign->participations->isEmpty())
                            <input type="text" class="form-control" value="No participants yet." readonly>
                        @else
                            <ul class="list-group">
                                @foreach ($campaign->participations as $participation)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="bi bi-person-circle me-2 text-success"></i>
                                            {{ optional($participation->user)->name ?? 'Unknown' }}
                                        </span>
                                        <span class="badge {{ $participation->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($participation->status) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <!-- Created at -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Created At</label>
                        <input type="text" class="form-control" value="{{ optional($campaign->created_at)->format('d M Y, H:i') ?? 'Unknown' }}" readonly>
                    </div>

                    <!-- Updated at -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Last Updated</label>
                        <input type="text" class="form-control" value="{{ optional($campaign->updated_at)->format('d M Y, H:i') ?? 'Unknown' }}" readonly>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                        <a href="{{ $indexRoute }}" class="btn btn-secondary w-100 w-md-auto">Back</a>
                        <a href="{{ $editRoute }}" class="btn btn-warning w-100 w-md-auto">Edit</a>
                        <form action="{{ $destroyRoute }}" method="POST" class="d-inline w-100 w-md-auto"
                              onsubmit="return confirm('Are you sure you want to delete this campaign?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection