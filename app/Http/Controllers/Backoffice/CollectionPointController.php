<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\CollectionPoint;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CollectionPointController extends Controller
{
    /**
     * Colonnes autorisées pour le tri serveur.
     */
    protected array $sortable = [
        'id', 'name', 'address', 'city', 'postal_code',
        'contact_phone', 'status', 'created_at',
    ];

    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $sort = in_array($request->query('sort'), $this->sortable, true)
            ? $request->query('sort')
            : 'id';

        $query->orderBy($sort, $direction);

        $total = (clone $query)->count();
        $activeCount = (clone $query)->where('status', 'active')->count();
        $inactiveCount = (clone $query)->where('status', 'inactive')->count();

        $cities = CollectionPoint::query()
            ->whereNotNull('city')
            ->orderBy('city')
            ->distinct()
            ->pluck('city');

        $perPage = (int) $request->query('per_page', 10);
        if (! in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $collectionPoints = $query->paginate($perPage)->withQueryString();

        return view('back.collectionpoints.index', compact(
            'collectionPoints',
            'total',
            'activeCount',
            'inactiveCount',
            'cities'
        ));
    }

    public function create()
    {
        return view('back.collectionpoints.create');
    }

    public function store(Request $request)
    {
        $this->normalizeInput($request);

        $validatedData = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
        );

        $this->validateBusinessRules($request);

        CollectionPoint::create($validatedData);

        return redirect()
            ->route('collectionpoints.index')
            ->with('success', 'Point de collecte ajouté avec succès.');
    }

    public function show($id)
    {
        $collectionPoint = CollectionPoint::findOrFail($id);

        return view('back.collectionpoints.show', compact('collectionPoint'));
    }

    public function edit($id)
    {
        $collectionPoint = CollectionPoint::findOrFail($id);
        \Log::info('CollectionPoint chargé pour édition : ', $collectionPoint->toArray());

        return view('back.collectionpoints.edit', compact('collectionPoint'));
    }

    public function update(Request $request, $id)
    {
        $collectionPoint = CollectionPoint::findOrFail($id);
        \Log::info('Données reçues pour mise à jour : ', $request->all());

        $this->normalizeInput($request);

        $validatedData = $request->validate(
            $this->validationRules($id),
            $this->validationMessages()
        );

        $this->validateBusinessRules($request, $id);

        $updated = $collectionPoint->update($validatedData);
        \Log::info('Mise à jour effectuée : ', ['updated' => $updated, 'data' => $collectionPoint->toArray()]);

        if ($updated) {
            return redirect()
                ->route('collectionpoints.index')
                ->with('success', 'Point de collecte mis à jour avec succès.');
        }

        return redirect()
            ->back()
            ->with('error', 'Échec de la mise à jour du point de collecte.');
    }

    public function destroy($id)
    {
        $collectionPoint = CollectionPoint::findOrFail($id);
        $collectionPoint->delete();

        return redirect()
            ->route('collectionpoints.index')
            ->with('success', 'Point de collecte supprimé.');
    }

    public function predictions()
    {
        $collectionPoints = CollectionPoint::all();
        $pointLocations = $collectionPoints->mapWithKeys(fn ($p) => [
            $p->id => [
                'id' => $p->id,
                'name' => $p->name,
                'lat' => (float) $p->latitude,
                'lon' => (float) $p->longitude,
            ],
        ]);

        return view('back.collectionpoints.predictions_dashboard', compact('collectionPoints', 'pointLocations'));
    }

    // =========================================================================
    // EXPORT CSV
    // =========================================================================
    public function exportCsv(Request $request)
    {
        $query = $this->filteredQuery($request)->orderBy('id');
        $filename = 'points-de-collecte-' . date('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 pour une ouverture correcte dans Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Nom', 'Adresse', 'Ville', 'Code postal',
                'Latitude', 'Longitude', 'Téléphone', 'Statut',
                'Horaires', 'Catégories acceptées', 'Créé le',
            ], ';');

            $query->chunk(200, function ($points) use ($handle) {
                foreach ($points as $point) {
                    fputcsv($handle, [
                        $point->id,
                        $point->name,
                        $point->address,
                        $point->city,
                        $point->postal_code,
                        $point->latitude,
                        $point->longitude,
                        $point->contact_phone,
                        $point->status,
                        $this->encodeList($point->opening_hours),
                        $this->encodeList($point->accepted_categories),
                        optional($point->created_at)->format('Y-m-d H:i:s'),
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // =========================================================================
    // EXPORT PDF
    // =========================================================================
    public function exportPdf(Request $request)
    {
        $points = $this->filteredQuery($request)->orderBy('id')->get();

        $pdf = Pdf::loadView('back.collectionpoints.pdf', [
            'points' => $points,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('points-de-collecte-' . date('Y-m-d-His') . '.pdf');
    }

    // =========================================================================
    // IMPORT CSV
    // =========================================================================
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:4096',
        ]);

        $file = $request->file('csv_file');

        if (strtolower($file->getClientOriginalExtension()) !== 'csv') {
            return back()->with('error', 'Le fichier doit être au format CSV.');
        }

        $rows = $this->readCsv($file->getRealPath());

        if (empty($rows)) {
            return back()->with('error', 'Le fichier CSV est vide ou illisible.');
        }

        // Détecte si la première ligne est un en-tête reconnu.
        $columns = $this->mapColumns($rows[0]);
        $hasHeader = count($columns) >= 2;
        $dataRows = $hasHeader ? array_slice($rows, 1) : $rows;

        if (! $hasHeader) {
            $columns = array_flip([
                'name', 'address', 'city', 'postal_code', 'latitude',
                'longitude', 'contact_phone', 'status',
                'opening_hours', 'accepted_categories',
            ]);
        }

        $imported = 0;
        $errors = [];

        foreach ($dataRows as $line => $row) {
            $attributes = $this->buildAttributes($row, $columns);

            if ($attributes['name'] === '') {
                $errors[] = 'Ligne ' . ($line + ($hasHeader ? 2 : 1)) . ' : nom manquant.';
                continue;
            }

            // Validation ligne par ligne (contrôle de saisie)
            $rowErrors = $this->validateRow($attributes);

            if (! empty($rowErrors)) {
                foreach ($rowErrors as $err) {
                    $errors[] = 'Ligne ' . ($line + ($hasHeader ? 2 : 1)) . ' : ' . $err;
                }
                continue;
            }

            try {
                CollectionPoint::create($attributes);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = 'Ligne ' . ($line + ($hasHeader ? 2 : 1)) . ' : ' . $e->getMessage();
            }
        }

        $message = $imported . ' point(s) de collecte importé(s).';
        if (! empty($errors)) {
            $message .= ' ' . count($errors) . ' ligne(s) ignorée(s).';
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', array_slice($errors, 0, 20));
    }

    // =========================================================================
    // RÈGLES DE VALIDATION (Laravel)
    // =========================================================================
    protected function validationRules(?int $ignoreId = null): array
    {
        $uniqueNameRule = 'unique:collection_points,name';
        if ($ignoreId !== null) {
            $uniqueNameRule .= ',' . $ignoreId;
        }

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                $uniqueNameRule,
                'regex:/^[a-zA-ZÀ-ÿ0-9\s\-\'\.\,\(\)]+$/u',
            ],
            'address' => [
                'required',
                'string',
                'min:5',
                'max:500',
                'regex:/^[a-zA-ZÀ-ÿ0-9\s\-\'\.\,\(\)\/]+$/u',
            ],
            'city' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/u',
            ],
            'postal_code' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Za-z0-9\s\-]+$/',
            ],
            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],
            'contact_phone' => [
                'nullable',
                'string',
                'min:6',
                'max:25',
                'regex:/^[0-9\+\s\-\(\)\.]+$/',
            ],
            'status' => [
                'required',
                'string',
                'in:active,inactive',
            ],
            'opening_hours' => [
                'nullable',
                'json',
            ],
            'accepted_categories' => [
                'nullable',
                'json',
            ],
        ];
    }

    protected function validationMessages(): array
    {
        return [
            // name
            'name.required' => 'Le nom du point de collecte est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.min' => 'Le nom doit contenir au moins 3 caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'name.unique' => 'Ce nom de point de collecte existe déjà.',
            'name.regex' => 'Le nom contient des caractères non autorisés.',

            // address
            'address.required' => 'L\'adresse est obligatoire.',
            'address.min' => 'L\'adresse doit contenir au moins 5 caractères.',
            'address.max' => 'L\'adresse ne peut pas dépasser 500 caractères.',
            'address.regex' => 'L\'adresse contient des caractères non autorisés.',

            // city
            'city.required' => 'La ville est obligatoire.',
            'city.min' => 'La ville doit contenir au moins 2 caractères.',
            'city.max' => 'La ville ne peut pas dépasser 100 caractères.',
            'city.regex' => 'La ville ne peut contenir que des lettres, espaces, tirets et apostrophes.',

            // postal_code
            'postal_code.max' => 'Le code postal ne peut pas dépasser 20 caractères.',
            'postal_code.regex' => 'Le code postal contient des caractères non autorisés.',

            // latitude / longitude
            'latitude.numeric' => 'La latitude doit être un nombre.',
            'latitude.between' => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.numeric' => 'La longitude doit être un nombre.',
            'longitude.between' => 'La longitude doit être comprise entre -180 et 180.',

            // contact_phone
            'contact_phone.min' => 'Le numéro de téléphone doit contenir au moins 6 caractères.',
            'contact_phone.max' => 'Le numéro de téléphone ne peut pas dépasser 25 caractères.',
            'contact_phone.regex' => 'Le numéro de téléphone contient des caractères non autorisés.',

            // status
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être « active » ou « inactive ».',

            // json fields
            'opening_hours.json' => 'Les horaires doivent être au format JSON valide.',
            'accepted_categories.json' => 'Les catégories acceptées doivent être au format JSON valide.',
        ];
    }

    // =========================================================================
    // CONTRÔLE DE SAISIE — RÈGLES MÉTIER
    // =========================================================================
    protected function validateBusinessRules(Request $request, ?int $ignoreId = null): void
    {
        $errors = [];

        // 1. Interdire les chaînes vides / uniquement espaces
        foreach (['name', 'address', 'city', 'postal_code', 'contact_phone'] as $field) {
            if ($request->filled($field) && trim((string) $request->input($field)) === '') {
                $errors[$field] = 'Ce champ ne peut pas contenir uniquement des espaces.';
            }
        }

        // 2. Vérifier la cohérence latitude/longitude
        $lat = $request->input('latitude');
        $lon = $request->input('longitude');

        if (($lat === null || $lat === '') xor ($lon === null || $lon === '')) {
            $errors['latitude'] = 'La latitude et la longitude doivent être fournies ensemble.';
        }

        // 3. Vérifier le format JSON des horaires
        if ($request->filled('opening_hours')) {
            $decoded = json_decode($request->input('opening_hours'), true);
            if (! is_array($decoded)) {
                $errors['opening_hours'] = 'Les horaires doivent être un tableau JSON.';
            }
        }

        // 4. Vérifier le format JSON des catégories
        if ($request->filled('accepted_categories')) {
            $decoded = json_decode($request->input('accepted_categories'), true);
            if (! is_array($decoded)) {
                $errors['accepted_categories'] = 'Les catégories acceptées doivent être un tableau JSON.';
            }
        }

        // 5. Empêcher la modification du statut si le point a des déchets liés (exemple)
        if ($ignoreId !== null) {
            $point = CollectionPoint::find($ignoreId);
            if ($point && $request->input('status') === 'inactive') {
                // Exemple : avertir si des déchets sont actifs
                $wasteCount = \DB::table('wastes')
                    ->where('collection_point_id', $ignoreId)
                    ->where('status', 'recyclable')
                    ->count();

                if ($wasteCount > 0) {
                    \Log::warning("Point {$ignoreId} désactivé alors qu'il contient {$wasteCount} déchets recyclables.");
                }
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Validation ligne CSV.
     */
    protected function validateRow(array $attributes): array
    {
        $errors = [];

        if (mb_strlen($attributes['name']) < 3) {
            $errors[] = 'nom trop court (min 3 caractères).';
        }

        if (empty($attributes['address'])) {
            $errors[] = 'adresse manquante.';
        } elseif (mb_strlen($attributes['address']) < 5) {
            $errors[] = 'adresse trop courte (min 5 caractères).';
        }

        if (empty($attributes['city'])) {
            $errors[] = 'ville manquante.';
        }

        if ($attributes['latitude'] !== null && ($attributes['latitude'] < -90 || $attributes['latitude'] > 90)) {
            $errors[] = 'latitude hors limites (-90 à 90).';
        }

        if ($attributes['longitude'] !== null && ($attributes['longitude'] < -180 || $attributes['longitude'] > 180)) {
            $errors[] = 'longitude hors limites (-180 à 180).';
        }

        if ($attributes['contact_phone'] !== null && mb_strlen($attributes['contact_phone']) < 6) {
            $errors[] = 'téléphone trop court (min 6 caractères).';
        }

        return $errors;
    }

    // =========================================================================
    // NORMALISATION DES ENTRÉES
    // =========================================================================
    protected function normalizeInput(Request $request): void
    {
        $request->merge([
            'name' => $this->normalizeString($request->input('name')),
            'address' => $this->normalizeString($request->input('address')),
            'city' => $this->normalizeString($request->input('city')),
            'postal_code' => $this->normalizeString($request->input('postal_code')),
            'contact_phone' => $this->normalizeString($request->input('contact_phone')),
        ]);
    }

    protected function normalizeString($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return $value === '' ? null : $value;
    }

    // =========================================================================
    // CONSTRUCTION DE LA REQUÊTE FILTRÉE
    // =========================================================================
    protected function filteredQuery(Request $request): Builder
    {
        $query = CollectionPoint::query();

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('postal_code', 'like', "%{$search}%")
                    ->orWhere('contact_phone', 'like', "%{$search}%");
            });
        }

        if (in_array($request->query('status'), ['active', 'inactive'], true)) {
            $query->where('status', $request->query('status'));
        }

        if ($city = trim((string) $request->query('city', ''))) {
            $query->where('city', $city);
        }

        return $query;
    }

    // =========================================================================
    // LECTURE CSV
    // =========================================================================
    protected function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return [];
        }

        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        rewind($handle);

        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) === 1 && trim((string) $row[0]) === '') {
                continue;
            }

            $rows[] = array_map(
                fn ($value) => is_string($value) ? trim($value) : $value,
                $row
            );
        }

        fclose($handle);

        return $rows;
    }

    // =========================================================================
    // MAPPING DES COLONNES CSV
    // =========================================================================
    protected function mapColumns(array $header): array
    {
        $aliases = [
            'name' => ['name', 'nom', 'title', 'libelle'],
            'address' => ['address', 'adresse'],
            'city' => ['city', 'ville'],
            'postal_code' => ['postal code', 'code postal', 'postalcode', 'zip', 'zipcode'],
            'latitude' => ['latitude', 'lat'],
            'longitude' => ['longitude', 'lng', 'lon', 'long'],
            'contact_phone' => ['contact phone', 'phone', 'telephone', 'tel'],
            'status' => ['status', 'statut', 'etat'],
            'opening_hours' => ['opening hours', 'horaires', 'heures'],
            'accepted_categories' => ['accepted categories', 'categories', 'categorie'],
        ];

        $columns = [];
        foreach ($header as $index => $label) {
            $normalized = $this->normalizeHeader($label);
            foreach ($aliases as $attribute => $list) {
                if (in_array($normalized, $list, true)) {
                    $columns[$attribute] = $index;
                    break;
                }
            }
        }

        return $columns;
    }

    protected function normalizeHeader($value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['-', '_'], ' ', $value);

        return preg_replace('/\s+/', ' ', $value);
    }

    // =========================================================================
    // CONSTRUCTION DES ATTRIBUTS CSV
    // =========================================================================
    protected function buildAttributes(array $row, array $columns): array
    {
        $get = function (string $attribute) use ($row, $columns) {
            if (! array_key_exists($attribute, $columns)) {
                return null;
            }

            return $row[$columns[$attribute]] ?? null;
        };

        $status = strtolower(trim((string) ($get('status') ?? '')));
        $statusMap = ['actif' => 'active', 'inactif' => 'inactive', 'activé' => 'active', 'désactivé' => 'inactive'];
        $status = $statusMap[$status] ?? $status;
        if (! in_array($status, ['active', 'inactive'], true)) {
            $status = 'active';
        }

        return [
            'name' => trim((string) ($get('name') ?? '')),
            'address' => $this->nullIfEmpty($get('address')),
            'city' => $this->nullIfEmpty($get('city')),
            'postal_code' => $this->nullIfEmpty($get('postal_code')),
            'latitude' => $this->parseCoordinate($get('latitude'), -90, 90),
            'longitude' => $this->parseCoordinate($get('longitude'), -180, 180),
            'contact_phone' => $this->nullIfEmpty($get('contact_phone')),
            'status' => $status,
            'opening_hours' => $this->parseList($get('opening_hours')),
            'accepted_categories' => $this->parseList($get('accepted_categories')),
        ];
    }

    protected function nullIfEmpty($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function parseCoordinate($value, float $min, float $max): ?float
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value)) {
            return null;
        }

        $number = (float) $value;

        return ($number >= $min && $number <= $max) ? $number : null;
    }

    protected function parseList($value): ?array
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $parts = array_filter(array_map('trim', preg_split('/[;,|]/', $value)), fn ($p) => $p !== '');

        return $parts ? array_values($parts) : null;
    }

    protected function encodeList($value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    }
}