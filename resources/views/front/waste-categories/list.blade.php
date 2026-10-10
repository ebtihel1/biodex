@extends('front.layout')

@section('content')

{{-- ============================================================
     STYLES
     ============================================================ --}}
<style>
    /* ===== Page shell ===== */
    .wc-page {
        background: linear-gradient(120deg, #f8fff8, #f0faf4);
        min-height: 100vh;
        padding: 2rem 0 4rem;
    }

    .wc-title {
        text-align: center;
        font-weight: 800;
        color: #153f3a;
        font-size: clamp(1.6rem, 3vw, 2.2rem);
        margin-bottom: 0.35rem;
    }
    .wc-subtitle {
        text-align: center;
        color: #6b7280;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    /* ===== Module IA ===== */
    .ai-classifier-panel {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.07);
        overflow: hidden;
        border: 1px solid #e7f0ea;
        margin-bottom: 3rem;
    }
    .ai-panel-head {
        background: linear-gradient(135deg, #198754 0%, #157347 100%);
        color: #fff;
        padding: 1.4rem 1.75rem;
        position: relative;
        overflow: hidden;
    }
    .ai-panel-head::after {
        content: "";
        position: absolute;
        top: -60%;
        right: -10%;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.16), transparent 70%);
        pointer-events: none;
    }
    .ai-panel-head h2 {
        font-size: 1.25rem;
        margin: 0;
        font-weight: 700;
        position: relative;
    }
    .ai-panel-head p {
        margin: 0.4rem 0 0;
        font-size: 0.86rem;
        opacity: 0.9;
        position: relative;
        max-width: 640px;
    }
    .ai-panel-body { padding: 1.75rem; }

    .ai-dropzone {
        border: 2px dashed #b7d8c6;
        border-radius: 14px;
        padding: 2.25rem 1rem;
        text-align: center;
        cursor: pointer;
        background: linear-gradient(180deg, #f8fbfa, #f2f9f5);
        transition: all 0.25s ease;
    }
    .ai-dropzone:hover,
    .ai-dropzone.dragover {
        border-color: #198754;
        background: #eef8f2;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(25, 135, 84, 0.12);
    }
    .ai-dropzone i {
        font-size: 2.8rem;
        color: #198754;
        display: block;
        margin-bottom: 0.5rem;
    }
    .ai-dropzone p { color: #153f3a; font-size: 0.98rem; margin: 0; }
    .ai-dropzone small { color: #9ca3af; font-size: 0.82rem; }

    #imagePreview {
        display: none;
        max-height: 300px;
        border-radius: 12px;
        margin: 1rem auto 0;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .result-hidden { display: none; }

    .ai-result-category {
        font-size: 1.6rem;
        font-weight: 800;
        color: #153f3a;
        line-height: 1.2;
    }
    .ai-result-card {
        border-radius: 14px;
        border: 1px solid #e7f0ea;
        background: #f8fbfa;
        padding: 1.25rem;
        height: 100%;
    }
    .ai-result-card .label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .ai-confidence-bar {
        height: 10px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        margin: 0.6rem 0;
    }
    .ai-confidence-bar > div {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease;
    }

    .ai-top-list .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.65rem 0.25rem;
        border-bottom: 1px solid #eef2ef;
        font-size: 0.9rem;
    }
    .ai-top-list .list-item:last-child { border-bottom: 0; }
    .ai-top-list .list-item strong { color: #198754; }

    .badge-ai {
        background: #e6f5ed;
        color: #176b43;
        font-weight: 600;
        padding: 0.45rem 0.85rem;
        border-radius: 8px;
        font-size: 0.78rem;
    }

    .ai-spinner-wrap {
        display: none;
        text-align: center;
        padding: 2rem;
    }

    /* ===== Barre de filtres ===== */
    .wc-toolbar {
        background: #fff;
        border-radius: 16px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        border: 1px solid #eef2ef;
        margin-bottom: 1.5rem;
    }

    .wc-toolbar .input-group-text {
        background: #198754;
        color: #fff;
        border: none;
        border-radius: 10px 0 0 10px;
    }
    .wc-toolbar .form-control {
        border: 1px solid #e2ebe6;
        border-radius: 0;
        padding: 0.65rem 0.9rem;
    }
    .wc-toolbar .form-control:focus {
        box-shadow: none;
        border-color: #198754;
    }
    .wc-toolbar .btn-outline-secondary {
        border: 1px solid #e2ebe6;
        border-left: none;
        border-radius: 0 10px 10px 0;
    }
    .wc-toolbar .form-select {
        border: 1px solid #e2ebe6;
        border-radius: 10px;
        padding: 0.65rem 0.9rem;
        font-size: 0.92rem;
    }
    .wc-toolbar .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.12);
    }

    /* ===== Barre d'info résultats ===== */
    .wc-results-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.5rem;
    }
    .results-count {
        background: #e8f5e8;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        color: #065f46;
        font-weight: 600;
        font-size: 0.88rem;
    }
    .results-count i { margin-right: 0.4rem; }

    .filter-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .filter-tag {
        background: #e3f2fd;
        color: #1976d2;
        padding: 0.35rem 0.7rem;
        border-radius: 20px;
        font-size: 0.78rem;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }
    .filter-tag .close {
        cursor: pointer;
        margin-left: 4px;
        font-weight: 700;
        opacity: 0.7;
    }
    .filter-tag .close:hover { opacity: 1; }

    /* ===== Cartes de catégories ===== */
    .collection-card {
        border: 1px solid #e7f0ea;
        border-radius: 16px;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        height: 100%;
        overflow: hidden;
        background: #fff;
    }
    .collection-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(25, 135, 84, 0.14);
        border-color: #198754;
    }
    .card-header {
        background: linear-gradient(135deg, #198754, #157347);
        color: #fff;
        border-radius: 0 !important;
        padding: 1rem 1.25rem;
        border: none;
    }
    .card-header h5 {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
    }
    .card-body { padding: 1.25rem; }
    .card-text {
        color: #4b5563;
        font-size: 0.92rem;
        line-height: 1.6;
        min-height: 60px;
    }
    .card-body small { font-size: 0.82rem; line-height: 1.5; }

    .category-card { transition: all 0.3s ease; }
    .category-card.hidden { display: none; }

    .highlight {
        background: #fff3cd;
        padding: 1px 4px;
        border-radius: 3px;
    }

    /* ===== Empty state ===== */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    .empty-state-icon {
        font-size: 4rem;
        color: #9ca3af;
        margin-bottom: 20px;
    }

    /* ===== Modales ===== */
    .modal-header {
        border-radius: 12px 12px 0 0;
        border: none;
    }
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
    }
    .modal-body h6 { font-size: 0.95rem; }

    /* ============================================================
       PAGINATION
       ============================================================ */
    .wc-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        margin-top: 2.5rem;
        flex-wrap: wrap;
    }
    .wc-pagination .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        height: 42px;
        padding: 0 0.85rem;
        border-radius: 10px;
        border: 1px solid #e2ebe6;
        background: #fff;
        color: #198754;
        font-weight: 600;
        font-size: 0.92rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .wc-pagination .page-btn:hover:not(:disabled):not(.active) {
        background: #f0faf4;
        border-color: #198754;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.15);
    }
    .wc-pagination .page-btn.active {
        background: linear-gradient(135deg, #198754, #157347);
        border-color: #198754;
        color: #fff;
        box-shadow: 0 4px 14px rgba(25, 135, 84, 0.35);
    }
    .wc-pagination .page-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: #f8f9fa;
    }
    .wc-pagination .page-btn i { font-size: 0.85rem; }
    .wc-pagination .page-info {
        font-size: 0.88rem;
        color: #6b7280;
        padding: 0 0.75rem;
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .wc-page { padding: 1.5rem 0 3rem; }
        .ai-panel-body { padding: 1.25rem; }
        .ai-panel-head { padding: 1.1rem 1.25rem; }
        .wc-toolbar { padding: 0.9rem; }
        .wc-pagination .page-btn {
            min-width: 38px;
            height: 38px;
            font-size: 0.85rem;
        }
    }
</style>

<div class="wc-page">
    <div class="container">

        {{-- ============ TITRE ============ --}}
        <h1 class="wc-title"><i class="fas fa-recycle me-2 text-success"></i>Waste Categories</h1>
        <p class="wc-subtitle">Explorez, recherchez et classez les catégories de déchets — assisté par IA</p>

        {{-- ============================================================
             MODULE IA — CLASSIFICATION AUTOMATIQUE
             ============================================================ --}}
        <div class="ai-classifier-panel">
            <div class="ai-panel-head">
                <h2><i class="bi bi-cpu me-2"></i>Classification automatique par IA</h2>
                <p>Uploadez une photo de déchet — la catégorie est détectée par un réseau de neurones (MobileNetV2 / ImageNet).</p>
            </div>

            <div class="ai-panel-body">
                <input type="file" id="imageInput" name="image" accept="image/*" class="d-none">
                <div class="ai-dropzone" id="dropzone">
                    <i class="bi bi-image"></i>
                    <p class="fw-semibold">Cliquez ou déposez une photo de déchet</p>
                    <small>JPG, PNG, GIF ou WEBP — max 6 Mo</small>
                </div>

                <div class="text-center">
                    <img id="imagePreview" alt="Aperçu de l'image">
                </div>

                <div class="d-grid mt-3">
                    <button id="analyzeBtn" class="btn btn-success btn-lg" disabled>
                        <i class="bi bi-search me-2"></i>Analyser l'image
                    </button>
                </div>

                <div id="loading" class="ai-spinner-wrap">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2 text-muted mb-0">Analyse en cours…</p>
                </div>

                {{-- Résultat --}}
                <div id="result" class="result-hidden row g-3 mt-2">
                    <div class="col-md-6">
                        <div class="ai-result-card">
                            <div class="label">Catégorie détectée</div>
                            <div class="ai-result-category" id="resCategory">—</div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Confiance</span>
                                    <span id="resConfidencePct" class="fw-bold">0%</span>
                                </div>
                                <div class="ai-confidence-bar">
                                    <div id="resConfidenceBar" style="width:0%;background:#198754"></div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3 small flex-wrap">
                                <span class="badge-ai" id="resModel">—</span>
                                <span class="badge bg-light text-dark border" id="resSource">—</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ai-result-card" style="background:#fff;">
                            <div class="label">Top catégories détectées</div>
                            <div class="ai-top-list" id="resTop"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             FILTRES — Recherche + Tri
             ============================================================ --}}
        <div class="wc-toolbar">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control"
                               placeholder="Rechercher par nom, description ou instructions…">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <select id="sortSelect" class="form-select">
                        <option value="name_asc">Tri : Nom A → Z</option>
                        <option value="name_desc">Tri : Nom Z → A</option>
                        <option value="newest">Tri : Plus récentes</option>
                        <option value="oldest">Tri : Plus anciennes</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Barre de résultats --}}
        <div class="wc-results-bar">
            <div class="results-count">
                <i class="fas fa-info-circle"></i>
                <span id="resultsCount">{{ $categories->count() }}</span> catégorie(s) trouvée(s)
            </div>
            <div class="filter-tags" id="filterTags"></div>
        </div>

        @if($categories->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-folder-open"></i></div>
                <h3 class="mb-3">Aucune catégorie disponible</h3>
                <p class="text-muted mb-4">Il n'y a pas encore de catégories de déchets.</p>
            </div>
        @else
            {{-- ============ GRILLE DES CATÉGORIES ============ --}}
            <div class="row g-4" id="categoriesContainer">
                @foreach($categories as $category)
                    <div class="col-lg-4 col-md-6 category-card"
                         data-name="{{ strtolower($category->name) }}"
                         data-description="{{ strtolower($category->description) }}"
                         data-instructions="{{ strtolower($category->recycling_instructions) }}"
                         data-created="{{ $category->created_at->timestamp }}">
                        <div class="collection-card card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-recycle me-2"></i>{{ $category->name }}
                                </h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <p class="card-text">{{ Str::limit($category->description, 100) }}</p>

                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ Str::limit($category->recycling_instructions, 80) }}
                                    </small>
                                </div>

                                <div class="d-grid mt-auto">
                                    <button class="btn btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#categoryModal{{ $category->id }}">
                                        <i class="fas fa-eye me-2"></i>Voir les détails
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal --}}
                    <div class="modal fade" id="categoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-recycle me-2"></i>{{ $category->name }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="text-success mb-3"><i class="fas fa-align-left me-2"></i>Description</h6>
                                    <p class="mb-4">{{ $category->description }}</p>

                                    <h6 class="text-success mb-3"><i class="fas fa-recycle me-2"></i>Instructions de recyclage</h6>
                                    <p class="mb-4">{{ $category->recycling_instructions }}</p>

                                    @if($category->created_at)
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                Créé le {{ $category->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Fermer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ============ PAGINATION ============ --}}
            <div id="paginationContainer">
                <div class="wc-pagination" id="wcPagination"></div>
            </div>

            {{-- No Results --}}
            <div id="noResults" class="empty-state" style="display: none;">
                <div class="empty-state-icon"><i class="fas fa-search"></i></div>
                <h3 class="mb-3">Aucune catégorie trouvée</h3>
                <p class="text-muted mb-4">Essayez de modifier votre recherche ou vos filtres.</p>
                <button class="btn btn-outline-success" id="resetFilters">
                    <i class="fas fa-redo me-2"></i>Réinitialiser
                </button>
            </div>
        @endif
    </div>
</div>

{{-- ============================================================
     SCRIPTS
     ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    /* ============================================================
       IA — CLASSIFICATION D'IMAGE
       ============================================================ */
    (function () {
        const input = document.getElementById('imageInput');
        const dropzone = document.getElementById('dropzone');
        const preview = document.getElementById('imagePreview');
        const analyzeBtn = document.getElementById('analyzeBtn');
        const loading = document.getElementById('loading');
        const result = document.getElementById('result');

        if (!input || !dropzone) return;

        let selectedFile = null;

        function setFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            selectedFile = file;
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            analyzeBtn.disabled = false;
        }

        dropzone.addEventListener('click', () => input.click());
        dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('dragover'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            setFile(e.dataTransfer.files[0]);
        });
        input.addEventListener('change', () => setFile(input.files[0]));

        analyzeBtn.addEventListener('click', async () => {
            if (!selectedFile) return;
            result.classList.add('result-hidden');
            loading.style.display = 'block';
            analyzeBtn.disabled = true;

            const formData = new FormData();
            formData.append('image', selectedFile);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch('{{ route('waste.classify') }}', { method: 'POST', body: formData });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Échec de la classification');
                }
                renderResult(data);
            } catch (error) {
                alert('Erreur : ' + error.message);
            } finally {
                loading.style.display = 'none';
                analyzeBtn.disabled = false;
            }
        });

        function renderResult(data) {
            document.getElementById('resCategory').textContent = data.category_label || data.category;
            const confidence = Math.round((data.confidence || 0) * 100);
            document.getElementById('resConfidencePct').textContent = confidence + '%';
            const bar = document.getElementById('resConfidenceBar');
            bar.style.width = Math.max(4, confidence) + '%';
            bar.style.background = confidence >= 60 ? '#198754' : confidence >= 35 ? '#d89b28' : '#bd4b43';

            document.getElementById('resModel').textContent = 'modèle : ' + (data.model || 'n/a');
            document.getElementById('resSource').textContent =
                data.model === 'mobilenetv2-imagenet' ? 'réseau CNN'
                    : data.model === 'heuristic-pil' ? 'heuristique (repli)'
                        : 'service indisponible';

            const top = document.getElementById('resTop');
            const list = Array.isArray(data.top_categories) && data.top_categories.length
                ? data.top_categories
                : [{ label: data.category_label || data.category, confidence: data.confidence || 0 }];

            top.replaceChildren(...list.map(item => {
                const row = document.createElement('div');
                row.className = 'list-item';
                row.innerHTML = `<span>${item.label}</span><strong>${Math.round((item.confidence || 0) * 100)}%</strong>`;
                return row;
            }));

            result.classList.remove('result-hidden');
        }
    })();

    /* ============================================================
       SEARCH + SORT + FILTRES + PAGINATION
       ============================================================ */
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const sortSelect = document.getElementById('sortSelect');
    const categoriesContainer = document.getElementById('categoriesContainer');
    const categoryCards = Array.from(document.querySelectorAll('.category-card'));
    const resultsCount = document.getElementById('resultsCount');
    const noResults = document.getElementById('noResults');
    const filterTags = document.getElementById('filterTags');
    const resetFilters = document.getElementById('resetFilters');
    const paginationEl = document.getElementById('wcPagination');

    const PER_PAGE = 6;
    let currentSearch = '';
    let currentSort = 'name_asc';
    let currentPage = 1;
    let filteredCards = [...categoryCards];

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            currentSearch = e.target.value.toLowerCase().trim();
            currentPage = 1;
            updateFilterTags();
            applyFilters();
        });

        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            currentSearch = '';
            currentPage = 1;
            updateFilterTags();
            applyFilters();
        });

        sortSelect.addEventListener('change', function(e) {
            currentSort = e.target.value;
            currentPage = 1;
            updateFilterTags();
            applyFilters();
        });

        resetFilters?.addEventListener('click', function() {
            searchInput.value = '';
            sortSelect.value = 'name_asc';
            currentSearch = '';
            currentSort = 'name_asc';
            currentPage = 1;
            updateFilterTags();
            applyFilters();
        });

        applyFilters();
    }

    function applyFilters() {
        /* 1. Filtrer */
        filteredCards = categoryCards.filter(card => {
            const name = card.dataset.name;
            const description = card.dataset.description;
            const instructions = card.dataset.instructions;

            return !currentSearch ||
                name.includes(currentSearch) ||
                description.includes(currentSearch) ||
                instructions.includes(currentSearch);
        });

        /* 2. Trier */
        filteredCards.sort((a, b) => {
            switch (currentSort) {
                case 'name_asc':  return a.dataset.name.localeCompare(b.dataset.name);
                case 'name_desc': return b.dataset.name.localeCompare(a.dataset.name);
                case 'newest':    return parseInt(b.dataset.created) - parseInt(a.dataset.created);
                case 'oldest':    return parseInt(a.dataset.created) - parseInt(b.dataset.created);
                default:          return 0;
            }
        });

        /* 3. Mettre à jour le compteur */
        if (resultsCount) resultsCount.textContent = filteredCards.length;

        /* 4. Vérifier si vide */
        if (filteredCards.length === 0) {
            if (noResults) noResults.style.display = 'block';
            if (categoriesContainer) categoriesContainer.style.display = 'none';
            if (paginationEl) paginationEl.innerHTML = '';
            return;
        }

        if (noResults) noResults.style.display = 'none';
        if (categoriesContainer) categoriesContainer.style.display = 'flex';

        /* 5. Ajuster la page courante si hors limites */
        const totalPages = Math.ceil(filteredCards.length / PER_PAGE);
        if (currentPage > totalPages) currentPage = totalPages;

        /* 6. Masquer toutes les cartes */
        categoryCards.forEach(card => {
            card.style.display = 'none';
            card.classList.add('hidden');
        });

        /* 7. Afficher les cartes de la page courante */
        const start = (currentPage - 1) * PER_PAGE;
        const end = start + PER_PAGE;
        const pageCards = filteredCards.slice(start, end);

        pageCards.forEach(card => {
            card.style.display = '';
            card.classList.remove('hidden');
            categoriesContainer.appendChild(card);
        });

        /* 8. Rendu de la pagination */
        renderPagination(totalPages);

        /* 9. Highlight */
        if (currentSearch) highlightSearchTerms(currentSearch);
        else removeHighlights();
    }

    function renderPagination(totalPages) {
        if (!paginationEl) return;

        if (totalPages <= 1) {
            paginationEl.innerHTML = '';
            return;
        }

        const frag = document.createDocumentFragment();

        /* Précédent */
        const prev = document.createElement('button');
        prev.className = 'page-btn';
        prev.disabled = currentPage === 1;
        prev.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prev.addEventListener('click', () => goToPage(currentPage - 1));
        frag.appendChild(prev);

        /* Pages numérotées */
        const pages = buildPageRange(currentPage, totalPages);
        pages.forEach(p => {
            const btn = document.createElement('button');
            btn.className = 'page-btn' + (p === currentPage ? ' active' : '');
            if (p === '...') {
                btn.textContent = '…';
                btn.disabled = true;
                btn.style.cursor = 'default';
                btn.style.border = 'none';
                btn.style.background = 'transparent';
            } else {
                btn.textContent = p;
                btn.addEventListener('click', () => goToPage(p));
            }
            frag.appendChild(btn);
        });

        /* Suivant */
        const next = document.createElement('button');
        next.className = 'page-btn';
        next.disabled = currentPage === totalPages;
        next.innerHTML = '<i class="fas fa-chevron-right"></i>';
        next.addEventListener('click', () => goToPage(currentPage + 1));
        frag.appendChild(next);

        /* Info */
        const info = document.createElement('span');
        info.className = 'page-info';
        info.textContent = `Page ${currentPage} / ${totalPages}`;
        frag.appendChild(info);

        paginationEl.replaceChildren(frag);
    }

    function buildPageRange(current, total) {
        const delta = 1;
        const range = [];
        const left = Math.max(2, current - delta);
        const right = Math.min(total - 1, current + delta);

        range.push(1);
        if (left > 2) range.push('...');
        for (let i = left; i <= right; i++) range.push(i);
        if (right < total - 1) range.push('...');
        if (total > 1) range.push(total);

        return range;
    }

    function goToPage(page) {
        const totalPages = Math.ceil(filteredCards.length / PER_PAGE);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        applyFilters();

        /* Scroll doux vers le haut de la grille */
        const top = document.querySelector('.wc-results-bar');
        if (top) top.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function highlightSearchTerms(term) {
        removeHighlights();
        const cards = document.querySelectorAll('.category-card:not(.hidden)');
        cards.forEach(card => {
            card.querySelectorAll('.card-title, .card-text, small').forEach(el => {
                if (el.dataset.original === undefined) {
                    el.dataset.original = el.innerHTML;
                }
                const regex = new RegExp(`(${term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                el.innerHTML = el.dataset.original.replace(regex, '<span class="highlight">$1</span>');
            });
        });
    }

    function removeHighlights() {
        document.querySelectorAll('.highlight').forEach(h => {
            const parent = h.parentNode;
            parent.replaceChild(document.createTextNode(h.textContent), h);
            parent.normalize();
        });
    }

    function updateFilterTags() {
        if (!filterTags) return;
        filterTags.innerHTML = '';

        if (currentSearch) {
            const searchTag = document.createElement('span');
            searchTag.className = 'filter-tag';
            searchTag.innerHTML = `Recherche : "${currentSearch}" <span class="close" onclick="clearSearchTag('search')">×</span>`;
            filterTags.appendChild(searchTag);
        }

        if (currentSort && currentSort !== 'name_asc') {
            const sortText = sortSelect.options[sortSelect.selectedIndex].text;
            const sortTag = document.createElement('span');
            sortTag.className = 'filter-tag';
            sortTag.innerHTML = `${sortText} <span class="close" onclick="clearSearchTag('sort')">×</span>`;
            filterTags.appendChild(sortTag);
        }
    }

    window.clearSearchTag = function(type) {
        if (type === 'search') {
            searchInput.value = '';
            currentSearch = '';
        } else if (type === 'sort') {
            sortSelect.value = 'name_asc';
            currentSort = 'name_asc';
        }
        currentPage = 1;
        updateFilterTags();
        applyFilters();
    };
});
</script>
@endsection