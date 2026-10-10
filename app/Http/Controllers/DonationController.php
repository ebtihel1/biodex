<?php

namespace App\Http\Controllers;

use App\Enums\DonationStatus;
use App\Models\CollectionPoint;
use App\Models\Donation;
use App\Models\User;
use App\Models\Waste;
use App\Models\WasteCategory;
use App\Services\SentimentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DonationController extends Controller
{
    /**
     * Colonnes autorisées pour le tri serveur.
     */
    protected array $sortable = [
        'id', 'user_id', 'item_name', 'condition',
        'status', 'created_at',
    ];

    // ======================= HELPERS ROUTE PREFIX =======================
    private function isFrontRoute()
    {
        return request()->route() && str_starts_with(request()->route()->getName(), 'front.');
    }

    private function getViewPrefix()
    {
        return $this->isFrontRoute() ? 'front.' : 'back.';
    }

    private function getRoutePrefix()
    {
        return $this->isFrontRoute() ? 'front.' : 'back.';
    }

    private function getPrefixedRoute($baseName)
    {
        return $this->getRoutePrefix() . $baseName;
    }

    private function getIndexRoute()
    {
        return $this->getPrefixedRoute('donations.index');
    }

    private function getCreateRoute()
    {
        return $this->getPrefixedRoute('donations.create');
    }

    private function getStoreRoute()
    {
        return $this->getPrefixedRoute('donations.store');
    }

    private function getShowRoute($donation = null)
    {
        $name = $this->getPrefixedRoute('donations.show');
        return $donation ? route($name, $donation) : $name;
    }

    private function getEditRoute($donation = null)
    {
        $name = $this->getPrefixedRoute('donations.edit');
        return $donation ? route($name, $donation) : $name;
    }

    private function getUpdateRoute($donation = null)
    {
        $name = $this->getPrefixedRoute('donations.update');
        return $donation ? route($name, $donation) : $name;
    }

    private function getDestroyRoute($donation = null)
    {
        $name = $this->getPrefixedRoute('donations.destroy');
        return $donation ? route($name, $donation) : $name;
    }

    // Retourne les NOMS de route (pour usage dans route(...) côté vue)
    private function getExportCsvRoute()
    {
        return $this->getPrefixedRoute('donations.export.csv');
    }

    private function getExportPdfRoute()
    {
        return $this->getPrefixedRoute('donations.export.pdf');
    }

    private function getImportRoute()
    {
        return $this->getPrefixedRoute('donations.import');
    }

    // ======================= INDEX =======================
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $sort = in_array($request->query('sort'), $this->sortable, true)
            ? $request->query('sort')
            : 'id';

        $query->orderBy($sort, $direction);

        // Summary (basé sur la requête filtrée)
        $summary = (clone $query)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_count")
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count")
            ->selectRaw("SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_count")
            ->selectRaw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count")
            ->first();

        $perPage = (int) $request->query('per_page', 10);
        if (! in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $donations = $query->paginate($perPage)->withQueryString();

        $service = new SentimentService();
        $sentiments = $service->getSentimentsForDonations($donations->items());

        $wasteCategories = WasteCategory::pluck('name', 'id')->toArray();

        $viewPrefix = $this->getViewPrefix();

        return view($viewPrefix . 'donations.index', [
            'donations' => $donations,
            'summary' => $summary,
            'wasteCategories' => $wasteCategories,
            'sentiments' => $sentiments,
            'createRoute' => $this->getCreateRoute(),
            'exportCsvRoute' => $this->getExportCsvRoute(),
            'exportPdfRoute' => $this->getExportPdfRoute(),
            'importRoute' => $this->getImportRoute(),
        ]);
    }

    // ======================= CREATE =======================
    public function create()
    {
        $wasteCategories = WasteCategory::all(['id', 'name']);
        $collectionPoints = CollectionPoint::all(['id', 'name']);
        $users = User::all();
        $viewPrefix = $this->getViewPrefix();
        $storeRoute = $this->getStoreRoute();
        $indexRoute = $this->getIndexRoute();
        return view($viewPrefix . 'donations.create', compact('wasteCategories', 'collectionPoints', 'users', 'storeRoute', 'indexRoute'));
    }

    // ======================= STORE =======================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'waste_category_id' => 'required|exists:waste_categories,id',
            'collection_point_id' => 'nullable|exists:collection_points,id',
            'item_name' => 'required|string|max:255',
            'condition' => 'required|string|in:new,used,damaged',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'pickup_required' => 'nullable|boolean',
            'pickup_address' => 'required_if:pickup_required,true|string|nullable|max:255',
        ]);

        $defaultCollectionPointId = CollectionPoint::value('id') ?? 1;
        $collectionPointId = $validated['collection_point_id'] ?? $defaultCollectionPointId;

        $waste = Waste::create([
            'type' => $validated['item_name'],
            'weight' => 0,
            'status' => 'reusable',
            'user_id' => $validated['user_id'],
            'waste_category_id' => $validated['waste_category_id'],
            'collection_point_id' => $collectionPointId,
            'image_path' => null,
            'description' => $validated['description'],
        ]);

        $validated['waste_id'] = $waste->id;
        unset($validated['waste_category_id']);
        unset($validated['collection_point_id']);

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('donations', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $donation = Donation::create($validated + ['status' => DonationStatus::Available]);

        $service = new SentimentService();
        $sentiment = $service->analyzeSentiment($validated['description'] ?? '');

        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Donation created successfully! Sentiment: ' . ucfirst($sentiment));
    }

    // ======================= ANALYZE SENTIMENT =======================
    public function analyzeSentiment(Request $request)
    {
        $request->validate(['description' => 'required|string']);
        $service = new SentimentService();
        $sentiment = $service->analyzeSentiment($request->description);
        return response()->json(['sentiment' => $sentiment]);
    }

    // ======================= SHOW =======================
    public function show(Donation $donation)
    {
        $donation->load(['user', 'waste.category']);
        $viewPrefix = $this->getViewPrefix();
        $indexRoute = $this->getIndexRoute();
        $editRoute = $this->getEditRoute($donation);
        $destroyRoute = $this->getDestroyRoute($donation);
        return view($viewPrefix . 'donations.show', compact('donation', 'indexRoute', 'editRoute', 'destroyRoute'));
    }

    // ======================= EDIT =======================
    public function edit(Donation $donation)
    {
        $donation->load(['user', 'waste.category']);
        $wasteCategories = WasteCategory::all(['id', 'name']);
        $collectionPoints = CollectionPoint::all(['id', 'name']);
        $users = User::all();
        $viewPrefix = $this->getViewPrefix();
        $updateRoute = $this->getUpdateRoute($donation);
        $showRoute = $this->getShowRoute($donation);
        $indexRoute = $this->getIndexRoute();
        return view($viewPrefix . 'donations.edit', compact('donation', 'wasteCategories', 'collectionPoints', 'users', 'updateRoute', 'showRoute', 'indexRoute'));
    }

    // ======================= UPDATE =======================
    public function update(Request $request, Donation $donation)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'waste_category_id' => 'required|exists:waste_categories,id',
            'collection_point_id' => 'nullable|exists:collection_points,id',
            'item_name' => 'required|string|max:255',
            'condition' => 'required|string|in:new,used,damaged',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'pickup_required' => 'nullable|boolean',
            'pickup_address' => 'required_if:pickup_required,true|string|nullable|max:255',
        ];

        if (!$this->isFrontRoute()) {
            $rules['status'] = ['required', Rule::enum(DonationStatus::class)];
        }

        $validated = $request->validate($rules);

        if ($this->isFrontRoute() && !isset($validated['status'])) {
            $validated['status'] = $donation->status;
        }

        $wasteUpdateData = [];
        if (isset($validated['waste_category_id'])) {
            $wasteUpdateData['waste_category_id'] = $validated['waste_category_id'];
        }
        if (isset($validated['collection_point_id'])) {
            $wasteUpdateData['collection_point_id'] = $validated['collection_point_id'];
        }
        if (!empty($wasteUpdateData)) {
            $donation->waste->update($wasteUpdateData);
        }

        unset($validated['waste_category_id']);
        unset($validated['collection_point_id']);

        if ($request->hasFile('images')) {
            if ($donation->images && is_array($donation->images)) {
                foreach ($donation->images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('donations', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $donation->update($validated);

        $service = new SentimentService();
        $sentiment = $service->analyzeSentiment($validated['description'] ?? '');

        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Donation updated! Sentiment: ' . ucfirst($sentiment));
    }

    // ======================= DESTROY =======================
    public function destroy(Donation $donation)
    {
        if ($donation->images && is_array($donation->images)) {
            foreach ($donation->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        $donation->delete();
        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Donation deleted!');
    }

    // ======================= EXPORT CSV =======================
    public function exportCsv(Request $request)
    {
        $query = $this->filteredQuery($request)->orderBy('id');
        $filename = 'dons-' . date('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Utilisateur', 'Catégorie', 'Article', 'État',
                'Statut', 'Description', 'Retrait requis', 'Adresse retrait', 'Créé le',
            ], ';');

            $query->chunk(200, function ($donations) use ($handle) {
                foreach ($donations as $donation) {
                    fputcsv($handle, [
                        $donation->id,
                        optional($donation->user)->name,
                        optional(optional($donation->waste)->category)->name,
                        $donation->item_name,
                        $donation->condition,
                        is_object($donation->status) ? $donation->status->value : $donation->status,
                        $donation->description,
                        $donation->pickup_required ? 'Oui' : 'Non',
                        $donation->pickup_address,
                        optional($donation->created_at)->format('Y-m-d H:i:s'),
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ======================= EXPORT PDF =======================
    public function exportPdf(Request $request)
    {
        $donations = $this->filteredQuery($request)->orderBy('id')->get();

        $pdf = Pdf::loadView($this->getViewPrefix() . 'donations.pdf', [
            'donations' => $donations,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('dons-' . date('Y-m-d-His') . '.pdf');
    }

    // ======================= IMPORT CSV =======================
    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|max:4096']);

        $file = $request->file('csv_file');
        if (strtolower($file->getClientOriginalExtension()) !== 'csv') {
            return back()->with('error', 'Le fichier doit être au format CSV.');
        }

        $rows = $this->readCsv($file->getRealPath());
        if (empty($rows)) {
            return back()->with('error', 'Le fichier CSV est vide ou illisible.');
        }

        $columns = $this->mapColumns($rows[0]);
        $hasHeader = count($columns) >= 2;
        $dataRows = $hasHeader ? array_slice($rows, 1) : $rows;

        if (! $hasHeader) {
            $columns = array_flip([
                'user_id', 'waste_category_id', 'item_name',
                'condition', 'description', 'status',
                'pickup_required', 'pickup_address',
            ]);
        }

        $imported = 0;
        $errors = [];

        foreach ($dataRows as $line => $row) {
            $attributes = $this->buildAttributes($row, $columns);
            $lineNumber = $line + ($hasHeader ? 2 : 1);

            if (empty($attributes['user_id'])) {
                $errors[] = "Ligne {$lineNumber} : user_id manquant.";
                continue;
            }
            if (! User::whereKey($attributes['user_id'])->exists()) {
                $errors[] = "Ligne {$lineNumber} : user_id {$attributes['user_id']} inexistant.";
                continue;
            }
            if (empty($attributes['waste_category_id'])) {
                $errors[] = "Ligne {$lineNumber} : waste_category_id manquant.";
                continue;
            }
            if (! WasteCategory::whereKey($attributes['waste_category_id'])->exists()) {
                $errors[] = "Ligne {$lineNumber} : waste_category_id inexistant.";
                continue;
            }
            if ($attributes['item_name'] === '') {
                $errors[] = "Ligne {$lineNumber} : item_name manquant.";
                continue;
            }

            try {
                $defaultCollectionPointId = CollectionPoint::value('id') ?? 1;

                $waste = Waste::create([
                    'type' => $attributes['item_name'],
                    'weight' => 0,
                    'status' => 'reusable',
                    'user_id' => $attributes['user_id'],
                    'waste_category_id' => $attributes['waste_category_id'],
                    'collection_point_id' => $defaultCollectionPointId,
                    'image_path' => null,
                    'description' => $attributes['description'],
                ]);

                Donation::create([
                    'user_id' => $attributes['user_id'],
                    'waste_id' => $waste->id,
                    'item_name' => $attributes['item_name'],
                    'condition' => $attributes['condition'],
                    'description' => $attributes['description'],
                    'status' => $attributes['status'],
                    'pickup_required' => $attributes['pickup_required'],
                    'pickup_address' => $attributes['pickup_address'],
                ]);

                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Ligne {$lineNumber} : " . $e->getMessage();
            }
        }

        $message = $imported . ' don(s) importé(s).';
        if (! empty($errors)) {
            $message .= ' ' . count($errors) . ' ligne(s) ignorée(s).';
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', array_slice($errors, 0, 20));
    }

    // ======================= HELPERS =======================
    protected function filteredQuery(Request $request): Builder
    {
        $query = Donation::with(['user', 'waste.category']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('pickup_address', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('waste.category', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if (request('item_search')) {
            $query->where('item_name', 'like', '%' . request('item_search') . '%');
        }

        if (request('user_search')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%' . request('user_search') . '%'));
        }

        if ($request->filled('waste_category_id')) {
            $query->whereHas('waste.category', fn ($q) => $q->where('id', $request->query('waste_category_id')));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->query('condition'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->query('date_to') . ' 23:59:59');
        }

        return $query;
    }

    protected function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) return [];

        $firstLine = fgets($handle);
        if ($firstLine === false) { fclose($handle); return []; }

        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        rewind($handle);

        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) === 1 && trim((string) $row[0]) === '') continue;
            $rows[] = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row);
        }

        fclose($handle);
        return $rows;
    }

    protected function mapColumns(array $header): array
    {
        $aliases = [
            'user_id' => ['user_id', 'user', 'utilisateur'],
            'waste_category_id' => ['waste_category_id', 'category', 'categorie', 'catégorie'],
            'item_name' => ['item_name', 'item', 'article', 'nom'],
            'condition' => ['condition', 'etat', 'état'],
            'description' => ['description', 'desc'],
            'status' => ['status', 'statut'],
            'pickup_required' => ['pickup_required', 'retrait', 'pickup'],
            'pickup_address' => ['pickup_address', 'adresse', 'adresse retrait'],
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

    protected function buildAttributes(array $row, array $columns): array
    {
        $get = function (string $attribute) use ($row, $columns) {
            if (! array_key_exists($attribute, $columns)) return null;
            return $row[$columns[$attribute]] ?? null;
        };

        $status = strtolower(trim((string) ($get('status') ?? 'available')));
        $allowed = array_map(fn ($s) => $s->value, DonationStatus::cases());
        if (! in_array($status, $allowed, true)) {
            $status = 'available';
        }

        $condition = strtolower(trim((string) ($get('condition') ?? 'used')));
        if (! in_array($condition, ['new', 'used', 'damaged'], true)) {
            $condition = 'used';
        }

        return [
            'user_id' => $this->parseId($get('user_id')),
            'waste_category_id' => $this->parseId($get('waste_category_id')),
            'item_name' => trim((string) ($get('item_name') ?? '')),
            'condition' => $condition,
            'description' => $this->nullIfEmpty($get('description')),
            'status' => $status,
            'pickup_required' => $this->parseBool($get('pickup_required')) ?? false,
            'pickup_address' => $this->nullIfEmpty($get('pickup_address')),
        ];
    }

    protected function parseId($value): ?int
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value)) return null;
        return (int) $value;
    }

    protected function parseBool($value): ?bool
    {
        $value = strtolower(trim((string) $value));
        if ($value === '') return null;
        if (in_array($value, ['1', 'yes', 'oui', 'true', 'vrai'], true)) return true;
        if (in_array($value, ['0', 'no', 'non', 'false', 'faux'], true)) return false;
        return null;
    }

    protected function nullIfEmpty($value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    public static function getWasteTypeName($wasteId)
    {
        return WasteCategory::find($wasteId)?->name ?? 'N/A';
    }
}