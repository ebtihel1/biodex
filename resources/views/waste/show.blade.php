@extends('back.layout')

@section('content')
<style>
    .waste-detail {
        --waste-ink: #153f3a;
        --waste-muted: #68817c;
        --waste-line: rgba(18, 63, 55, 0.1);
        color: var(--waste-ink);
    }

    .waste-detail-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .waste-detail-eyebrow {
        margin-bottom: 0.25rem;
        color: var(--waste-muted);
        font-size: 0.75rem;
        font-weight: 650;
        text-transform: uppercase;
    }

    .waste-detail-title {
        margin: 0;
        color: var(--waste-ink);
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .waste-detail-context {
        margin: 0.4rem 0 0;
        color: var(--waste-muted);
        font-size: 0.84rem;
    }

    .waste-detail-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 0.85rem 1rem;
        border: 1px solid var(--waste-line);
        border-left: 3px solid #1fa26a;
        border-radius: 8px;
        background: #fff;
    }

    .waste-detail-weight-label {
        display: block;
        color: var(--waste-muted);
        font-size: 0.72rem;
        font-weight: 600;
    }

    .waste-detail-weight {
        display: block;
        margin-top: 0.1rem;
        color: var(--waste-ink);
        font-size: 1.3rem;
        font-weight: 750;
        line-height: 1.2;
    }

    .waste-detail-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .waste-detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(260px, 0.8fr);
        gap: 1rem;
        align-items: start;
    }

    .waste-detail-panel {
        border: 1px solid var(--waste-line);
        border-radius: 8px;
        background: #fff;
    }

    .waste-detail-panel + .waste-detail-panel {
        margin-top: 1rem;
    }

    .waste-detail-panel-title {
        margin: 0;
        padding: 0.9rem 1rem;
        border-bottom: 1px solid var(--waste-line);
        color: var(--waste-ink);
        font-size: 0.92rem;
        font-weight: 700;
    }

    .waste-detail-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin: 0;
    }

    .waste-detail-field {
        min-width: 0;
        padding: 0.9rem 1rem;
        border-bottom: 1px solid var(--waste-line);
    }

    .waste-detail-field:nth-child(odd) {
        border-right: 1px solid var(--waste-line);
    }

    .waste-detail-field dt {
        margin-bottom: 0.28rem;
        color: var(--waste-muted);
        font-size: 0.75rem;
        font-weight: 600;
    }

    .waste-detail-field dd {
        margin: 0;
        overflow-wrap: anywhere;
        color: var(--waste-ink);
        font-size: 0.9rem;
        font-weight: 550;
    }

    .waste-detail-description {
        padding: 1rem;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
        color: var(--waste-ink);
        font-size: 0.9rem;
        line-height: 1.65;
    }

    .waste-status {
        display: inline-flex;
        align-items: center;
        padding: 0.3rem 0.65rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 650;
        text-transform: capitalize;
    }

    .waste-status-recyclable { background: #e6f5ed; color: #176b43; }
    .waste-status-reusable { background: #e8f1fb; color: #285d8f; }
    .waste-status-other { background: #eef2f1; color: #526762; }

    .waste-detail-image {
        display: grid;
        min-height: 210px;
        place-items: center;
        overflow: hidden;
        border-bottom: 1px solid var(--waste-line);
        background: #f4f8f6;
    }

    .waste-detail-image img {
        display: block;
        width: 100%;
        max-height: 340px;
        object-fit: contain;
    }

    .waste-detail-image-empty {
        display: grid;
        min-height: 210px;
        place-items: center;
        color: var(--waste-muted);
        font-size: 0.85rem;
    }

    .waste-detail-meta {
        display: grid;
        gap: 0.65rem;
        padding: 1rem;
        color: var(--waste-muted);
        font-size: 0.78rem;
    }

    .waste-detail-meta strong {
        display: block;
        margin-bottom: 0.15rem;
        color: var(--waste-ink);
        font-size: 0.8rem;
    }

    @media (max-width: 760px) {
        .waste-detail-header { flex-direction: column; }
        .waste-detail-title { font-size: 1.3rem; }
        .waste-detail-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 480px) {
        .waste-detail-fields { grid-template-columns: 1fr; }
        .waste-detail-field:nth-child(odd) { border-right: 0; }
    }
</style>

<main class="waste-detail">
    @php
        $statusClass = match ($waste->status) {
            'recyclable' => 'waste-status-recyclable',
            'reusable' => 'waste-status-reusable',
            default => 'waste-status-other',
        };
    @endphp
    <header class="waste-detail-header">
        <div>
            <div class="waste-detail-eyebrow">Waste record #{{ $waste->id }}</div>
            <h1 class="waste-detail-title">{{ $waste->type }}</h1>
            <p class="waste-detail-context">
                {{ $waste->category->name ?? 'Unknown category' }}
                <span aria-hidden="true">·</span>
                {{ $waste->collectionPoint->name ?? 'Unknown collection point' }}
            </p>
        </div>
        <div class="waste-detail-actions">
            <span class="waste-status {{ $statusClass }} align-self-center">{{ $waste->status }}</span>
            <a href="{{ route('wastes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Back to list
            </a>
            <a href="{{ route('wastes.edit', $waste->id) }}" class="btn btn-success btn-sm">
                <i class="bi bi-pencil me-1" aria-hidden="true"></i>Edit record
            </a>
        </div>
    </header>

    <section class="waste-detail-summary" aria-label="Waste summary">
        <div>
            <span class="waste-detail-weight-label">Reported weight</span>
            <strong class="waste-detail-weight">{{ number_format((float) $waste->weight, 2) }} kg</strong>
        </div>
        <div class="text-end small text-muted">
            Collection point
            <strong class="d-block text-body">{{ $waste->collectionPoint->name ?? 'Unknown collection point' }}</strong>
        </div>
    </section>

    <div class="waste-detail-grid">
        <div>
            <section class="waste-detail-panel" aria-labelledby="waste-information-title">
                <h2 id="waste-information-title" class="waste-detail-panel-title">Record information</h2>
                <dl class="waste-detail-fields">
                    <div class="waste-detail-field">
                        <dt>Category</dt>
                        <dd>{{ $waste->category->name ?? 'Unknown category' }}</dd>
                    </div>
                    <div class="waste-detail-field">
                        <dt>Collection point</dt>
                        <dd>{{ $waste->collectionPoint->name ?? 'Unknown collection point' }}</dd>
                    </div>
                    <div class="waste-detail-field">
                        <dt>Submitted by</dt>
                        <dd>{{ $waste->user->name ?? 'Unknown user' }}</dd>
                    </div>
                </dl>
                <h2 class="waste-detail-panel-title">Description</h2>
                <div class="waste-detail-description">{{ $waste->description ?: 'No description provided.' }}</div>
            </section>
        </div>

        <aside>
            <section class="waste-detail-panel" aria-label="Waste image and record dates">
                @if ($waste->image_path)
                    <figure class="waste-detail-image mb-0">
                        <img src="{{ asset('storage/' . $waste->image_path) }}" alt="Photo of {{ $waste->type }} waste" loading="lazy">
                    </figure>
                @else
                    <div class="waste-detail-image-empty">
                        <span><i class="bi bi-image me-2" aria-hidden="true"></i>No image available</span>
                    </div>
                @endif
                <div class="waste-detail-meta">
                    <div>
                        <strong>Created</strong>
                        {{ optional($waste->created_at)->format('d M Y, H:i') ?? 'Unknown' }}
                    </div>
                    <div>
                        <strong>Last updated</strong>
                        {{ optional($waste->updated_at)->format('d M Y, H:i') ?? 'Unknown' }}
                    </div>
                </div>
            </section>
        </aside>
    </div>
</main>
@endsection
