@extends('front.layout')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-success mb-0">Waste #{{ $wastes->id }}</h1>
        <a href="{{ route('front.wastes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to list
        </a>
    </div>

    <div class="row g-4">
        <!-- Image -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                @if($wastes->image_path)
                    <img src="{{ asset('storage/' . $wastes->image_path) }}"
                         alt="{{ $wastes->type }}"
                         class="card-img-top rounded"
                         style="height: 280px; object-fit: cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 280px;">
                        <i class="fas fa-trash text-muted" style="font-size: 3rem;"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- Details -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4">
                <div class="row mb-3">
                    <div class="col-5 text-muted">Type</div>
                    <div class="col-7 fw-semibold">{{ $wastes->type ?? '—' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-5 text-muted">Category</div>
                    <div class="col-7">
                        <span class="badge bg-success">
                            <i class="fas fa-folder me-1"></i>{{ $wastes->category->name ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                @if($wastes->ai_classification)
                <div class="row mb-3">
                    <div class="col-5 text-muted">Category (AI)</div>
                    <div class="col-7">
                        <span class="badge bg-info text-dark">
                            <i class="fas fa-cpu me-1"></i>{{ $wastes->ai_classification }}
                            @if($wastes->ai_confidence !== null)
                                ({{ round($wastes->ai_confidence * 100) }}%)
                            @endif
                        </span>
                        @if($wastes->ai_confidence !== null)
                            <div class="progress mt-2" style="height: 8px;" role="progressbar"
                                 aria-valuenow="{{ round($wastes->ai_confidence * 100) }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-info"
                                     style="width: {{ max(4, round($wastes->ai_confidence * 100)) }}%"></div>
                            </div>
                            <small class="text-muted">Confiance du modèle CNN</small>
                        @endif
                    </div>
                </div>
                @endif

                <div class="row mb-3">
                    <div class="col-5 text-muted">Weight</div>
                    <div class="col-7 fw-semibold">{{ $wastes->weight }} kg</div>
                </div>

                <div class="row mb-3">
                    <div class="col-5 text-muted">Status</div>
                    <div class="col-7">
                        <span class="badge bg-secondary text-capitalize">{{ $wastes->status ?? '—' }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-5 text-muted">Collection point</div>
                    <div class="col-7">
                        @if($wastes->collectionPoint)
                            <a href="{{ route('front.collectionpoints.show', $wastes->collection_point_id) }}">
                                {{ $wastes->collectionPoint->name }}
                            </a>
                        @else
                            —
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-5 text-muted">Created</div>
                    <div class="col-7">{{ optional($wastes->created_at)->format('d/m/Y H:i') }}</div>
                </div>

                @if($wastes->description)
                <div class="info-box p-3 rounded bg-light mt-2">
                    <h6 class="text-success mb-2"><i class="fas fa-align-left me-2"></i>Description</h6>
                    <p class="mb-0">{{ $wastes->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection