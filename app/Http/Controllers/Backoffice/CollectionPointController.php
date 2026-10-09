<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\CollectionPoint;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_phone' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'opening_hours' => 'nullable|json',
            'accepted_categories' => 'nullable|json',
        ]);

        CollectionPoint::create($request->all());

        return redirect()->route('collectionpoints.index')->with('success', 'Point de collecte ajouté avec succès.');
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

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_phone' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'opening_hours' => 'nullable|json',
            'accepted_categories' => 'nullable|json',
        ]);

        $updated = $collectionPoint->update($validatedData);
        \Log::info('Mise à jour effectuée : ', ['updated' => $updated, 'data' => $collectionPoint->toArray()]);

        if ($updated) {
            return redirect()->route('collectionpoints.index')->with('success', 'Point de collecte mis à jour avec succès.');
        } else {
            return redirect()->back()->with('error', 'Échec de la mise à jour du point de collecte.');
        }
    }

    public function destroy($id)
    {
        $collectionPoint = CollectionPoint::findOrFail($id);
        $collectionPoint->delete();

        return redirect()->route('collectionpoints.index')->with('success', 'Point de collecte supprimé.');
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

    /**
     * Export CSV des points de collecte (respecte recherche + filtre statut).
     */
    public function exportCsv(Request $request)
    {
        $query = $this->filteredQuery($request)->orderBy('id');
        $filename = 'points-de-collecte-'.date('Y-m-d-His').'.csv';

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

    /**
     * Export PDF des points de collecte (respecte recherche + filtre statut).
     */
    public function exportPdf(Request $request)
    {
        $points = $this->filteredQuery($request)->orderBy('id')->get();

        $pdf = Pdf::loadView('back.collectionpoints.pdf', [
            'points' => $points,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('points-de-collecte-'.date('Y-m-d-His').'.pdf');
    }

    /**
     * Import CSV des points de collecte.
     */
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
                $errors[] = 'Ligne '.($line + ($hasHeader ? 2 : 1)).' : nom manquant.';

                continue;
            }

            try {
                CollectionPoint::create($attributes);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = 'Ligne '.($line + ($hasHeader ? 2 : 1)).' : '.$e->getMessage();
            }
        }

        $message = $imported.' point(s) de collecte importé(s).';
        if (! empty($errors)) {
            $message .= ' '.count($errors).' ligne(s) ignorée(s).';
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', array_slice($errors, 0, 20));
    }

    /**
     * Construit la requête filtrée (recherche + statut + ville).
     */
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

    /**
     * Lit un fichier CSV en détectant le séparateur.
     */
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

    /**
     * Associe les en-têtes CSV aux attributs du modèle.
     */
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

    /**
     * Construit les attributs d'une ligne CSV.
     */
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
