<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodex — Classification IA des déchets</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: system-ui, sans-serif;
            background: linear-gradient(120deg, #f8fff8, #f5fff2);
            min-height: 100vh;
        }

        .navbar {
            background-color: #198754 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        .nav-link {
            color: #fff !important;
        }

        .nav-link:hover {
            color: #a7f3d0 !important;
        }

        .page-wrap {
            max-width: 860px;
            margin: 2.5rem auto;
        }

        .panel {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .panel-head {
            background: #198754;
            color: #fff;
            padding: 1.1rem 1.4rem;
        }

        .panel-head h1 {
            font-size: 1.35rem;
            margin: 0;
        }

        .panel-head p {
            margin: 0.25rem 0 0;
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .panel-body {
            padding: 1.5rem;
        }

        .dropzone {
            border: 2px dashed #b7d8c6;
            border-radius: 12px;
            padding: 2rem 1rem;
            text-align: center;
            cursor: pointer;
            background: #f8fbfa;
            transition: all 0.25s ease;
        }

        .dropzone:hover,
        .dropzone.dragover {
            border-color: #198754;
            background: #eef8f2;
        }

        .dropzone i {
            font-size: 2.6rem;
            color: #198754;
        }

        #imagePreview {
            display: none;
            max-height: 300px;
            border-radius: 10px;
            margin: 1rem auto 0;
            box-shadow: 0 3px 12px rgba(0,0,0,0.15);
        }

        .result-hidden {
            display: none;
        }

        .result-category {
            font-size: 1.5rem;
            font-weight: 700;
            color: #153f3a;
        }

        .confidence-bar {
            height: 12px;
            background: #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            margin: 0.5rem 0;
        }

        .confidence-bar > div {
            height: 100%;
            border-radius: 8px;
            transition: width 0.6s ease;
        }

        .top-list .list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }

        .top-list .list-item:last-child {
            border-bottom: 0;
        }

        .badge-ai {
            background: #e6f5ed;
            color: #176b43;
            font-weight: 600;
        }

        .spinner-wrap {
            display: none;
            text-align: center;
            padding: 2rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center bg-white rounded-3 px-2 py-1" href="{{ url('/') }}">
                <img src="{{ asset('images/biodex-logo.png') }}" alt="Biodex" height="32">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="{{ url('/biodex') }}" class="nav-link"><i class="bi bi-house-door me-1"></i>Home</a></li>
                    <li class="nav-item"><a href="{{ url('/biodex/collectionpoints/map') }}" class="nav-link"><i class="bi bi-map me-1"></i>Carte des points</a></li>
                    <li class="nav-item"><a href="{{ route('front.collectionpoints.index') }}" class="nav-link"><i class="bi bi-grid me-1"></i>Points de collecte</a></li>
                    <li class="nav-item"><a href="{{ url('/login') }}" class="nav-link"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="page-wrap container">
        <div class="panel">
            <div class="panel-head">
                <h1><i class="bi bi-cpu me-2"></i>Classification automatique des déchets</h1>
                <p>Upload de la photo d'un déchet → catégorie détectée par un modèle CNN (MobileNetV2 / ImageNet) en complément de la prédiction des volumes.</p>
            </div>

            <div class="panel-body">
                <input type="file" id="imageInput" name="image" accept="image/*" class="d-none">
                <div class="dropzone" id="dropzone">
                    <i class="bi bi-image me-1"></i>
                    <p class="mb-1 mt-2 fw-semibold">Cliquez ou déposez une photo de déchet</p>
                    <small class="text-muted">JPG, PNG, GIF ou WEBP — max 6 Mo</small>
                </div>

                <div class="text-center">
                    <img id="imagePreview" alt="Aperçu de l'image">
                </div>

                <div class="d-grid mt-3">
                    <button id="analyzeBtn" class="btn btn-success btn-lg" disabled>
                        <i class="bi bi-search me-2"></i>Analyser l'image
                    </button>
                </div>

                <div id="loading" class="spinner-wrap">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2 text-muted mb-0">Analyse en cours (réseau de neurones)…</p>
                </div>

                <!-- Résultat -->
                <div id="result" class="result-hidden row g-4 mt-2">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="text-muted small mb-1">Catégorie détectée</div>
                            <div class="result-category" id="resCategory">—</div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between small">
                                    <span>Confiance</span>
                                    <span id="resConfidencePct">0%</span>
                                </div>
                                <div class="confidence-bar">
                                    <div id="resConfidenceBar" style="width:0%;background:#198754"></div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3 small">
                                <span class="badge badge-ai" id="resModel">—</span>
                                <span class="badge bg-light text-dark border" id="resSource">—</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-white h-100">
                            <div class="text-muted small mb-2">Résultat détaillé (top catégories)</div>
                            <div class="top-list" id="resTop"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    (() => {
        const input = document.getElementById('imageInput');
        const dropzone = document.getElementById('dropzone');
        const preview = document.getElementById('imagePreview');
        const analyzeBtn = document.getElementById('analyzeBtn');
        const loading = document.getElementById('loading');
        const result = document.getElementById('result');

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
                const response = await fetch('{{ route('waste.classify') }}', {method: 'POST', body: formData});
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
            const list = Array.isArray(data.top_categories) && data.top_categories.length ? data.top_categories : [
                {label: data.category_label || data.category, confidence: data.confidence || 0}
            ];
            top.replaceChildren(...list.map(item => {
                const row = document.createElement('div');
                row.className = 'list-item';
                row.innerHTML = `<span>${item.label}</span><strong>${Math.round((item.confidence || 0) * 100)}%</strong>`;
                return row;
            }));

            result.classList.remove('result-hidden');
        }
    })();
    </script>
</body>
</html>