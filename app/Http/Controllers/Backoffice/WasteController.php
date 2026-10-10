<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Waste;
use App\Models\CollectionPoint;
use App\Models\WasteCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;

class WasteController extends Controller
{
    /**
     * Colonnes autorisées pour le tri serveur.
     */
    protected array $sortable = [
        'id', 'type', 'weight', 'status', 'description', 'created_at',
    ];

    /**
     * Display a listing of the resource.
     */
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
            ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(weight), 0) as total_weight')
            ->selectRaw("SUM(CASE WHEN status = 'recyclable' THEN 1 ELSE 0 END) as recyclable_count")
            ->selectRaw("SUM(CASE WHEN status = 'reusable' THEN 1 ELSE 0 END) as reusable_count")
            ->first();

        $categories = WasteCategory::query()->orderBy('name')->get(['id', 'name']);

        $perPage = (int) $request->query('per_page', 12);
        if (! in_array($perPage, [5, 12, 25, 50, 100], true)) {
            $perPage = 12;
        }

        $wastes = $query->paginate($perPage)->withQueryString();

        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');
        $categoryId = $request->query('category');

        return view('waste.list', compact('wastes', 'categories', 'summary', 'search', 'status', 'categoryId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $collectionPoints = CollectionPoint::all();
        return view('waste.create', compact('collectionPoints'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-]+$/'
            ],
            'weight' => [
                'required',
                'numeric',
                'min:0.1',
                'max:1000',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'status' => [
                'required',
                'in:recyclable,reusable'
            ],
            'user_id' => [
                'required',
                'exists:users,id'
            ],
            'waste_category_id' => [
                'required',
                'exists:waste_categories,id'
            ],
            'collection_point_id' => [
                'required',
                'exists:collection_points,id'
            ],
            'image' => [
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048'
            ],
            'description' => [
                'required',
                'string',
                'min:5',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-\.,!?()]+$/'
            ],
        ], [
            'type.required' => 'Waste type is required.',
            'type.max' => 'Type must not exceed 100 characters.',
            'type.regex' => 'Type can only contain letters, spaces and hyphens.',

            'weight.required' => 'Weight is required.',
            'weight.numeric' => 'Weight must be a number.',
            'weight.min' => 'Weight must be at least 0.1 kg.',
            'weight.max' => 'Weight cannot exceed 1000 kg.',
            'weight.regex' => 'Weight must be a valid number with up to 2 decimal places.',

            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either recyclable or reusable.',

            'user_id.required' => 'User is required.',
            'user_id.exists' => 'Selected user does not exist.',

            'waste_category_id.required' => 'Waste category is required.',
            'waste_category_id.exists' => 'Selected waste category does not exist.',

            'collection_point_id.required' => 'Collection point is required.',
            'collection_point_id.exists' => 'Selected collection point does not exist.',

            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Image must be a JPEG, PNG, JPG, or GIF file.',
            'image.max' => 'Image size cannot exceed 2MB.',

            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 5 characters.',
            'description.max' => 'Description must not exceed 100 characters.',
            'description.regex' => 'Description can only contain letters, numbers, spaces and the following special characters: - . , ! ? ( )',
        ]);

        $this->validateImage($request);
        $this->validateWeightPrecision($request);

        $data = $validated;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('wastes', 'public');
            $data['image_path'] = $imagePath;
        }

        $data['collection_point_id'] = $request->input('collection_point_id');
        Waste::create($data);

        return redirect()->route('wastes.index')->with('success', 'Waste created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $waste = Waste::with(['category', 'user', 'collectionPoint'])->findOrFail($id);
        return view('waste.show', compact('waste'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $waste = Waste::findOrFail($id);
        $collectionPoints = CollectionPoint::all();
        return view('waste.edit', compact('waste', 'collectionPoints'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'type' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-]+$/'
            ],
            'weight' => [
                'required',
                'numeric',
                'min:0.1',
                'max:1000',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'status' => [
                'required',
                'in:recyclable,reusable'
            ],
            'user_id' => [
                'required',
                'exists:users,id'
            ],
            'waste_category_id' => [
                'required',
                'exists:waste_categories,id'
            ],
            'collection_point_id' => [
                'required',
                'exists:collection_points,id'
            ],
            'image' => [
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048'
            ],
            'description' => [
                'required',
                'string',
                'min:5',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-\.,!?()]+$/'
            ],
        ], [
            'type.required' => 'Waste type is required.',
            'type.max' => 'Type must not exceed 100 characters.',
            'type.regex' => 'Type can only contain letters, spaces and hyphens.',

            'weight.required' => 'Weight is required.',
            'weight.numeric' => 'Weight must be a number.',
            'weight.min' => 'Weight must be at least 0.1 kg.',
            'weight.max' => 'Weight cannot exceed 1000 kg.',
            'weight.regex' => 'Weight must be a valid number with up to 2 decimal places.',

            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either recyclable or reusable.',

            'user_id.required' => 'User is required.',
            'user_id.exists' => 'Selected user does not exist.',

            'waste_category_id.required' => 'Waste category is required.',
            'waste_category_id.exists' => 'Selected waste category does not exist.',

            'collection_point_id.required' => 'Collection point is required.',
            'collection_point_id.exists' => 'Selected collection point does not exist.',

            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Image must be a JPEG, PNG, JPG, or GIF file.',
            'image.max' => 'Image size cannot exceed 2MB.',

            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 5 characters.',
            'description.max' => 'Description must not exceed 100 characters.',
            'description.regex' => 'Description can only contain letters, numbers, spaces and the following special characters: - . , ! ? ( )',
        ]);

        $this->validateImage($request);
        $this->validateWeightPrecision($request);

        $waste = Waste::findOrFail($id);
        $data = $validated;

        if ($request->hasFile('image')) {
            if ($waste->image_path) {
                \Storage::disk('public')->delete($waste->image_path);
            }
            $imagePath = $request->file('image')->store('wastes', 'public');
            $data['image_path'] = $imagePath;
        }

        $waste->update($data);

        return redirect()->route('wastes.index')->with('success', 'Waste updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $waste = Waste::findOrFail($id);

        if ($waste->image_path) {
            \Storage::disk('public')->delete($waste->image_path);
        }

        $waste->delete();
        return redirect()->route('wastes.index')->with('success', 'Waste deleted successfully');
    }

    // =========================================================================
    // EXPORT CSV
    // =========================================================================
    public function exportCsv(Request $request)
    {
        $query = $this->filteredQuery($request)->with(['category', 'user', 'collectionPoint'])->orderBy('id');
        $filename = 'dechets-' . date('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Type', 'Poids (kg)', 'Statut', 'Description',
                'Catégorie', 'Utilisateur', 'Point de collecte', 'Créé le',
            ], ';');

            $query->chunk(200, function ($wastes) use ($handle) {
                foreach ($wastes as $waste) {
                    fputcsv($handle, [
                        $waste->id,
                        $waste->type,
                        $waste->weight,
                        $waste->status,
                        $waste->description,
                        optional($waste->category)->name,
                        optional($waste->user)->name,
                        optional($waste->collectionPoint)->name,
                        optional($waste->created_at)->format('Y-m-d H:i:s'),
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
        $wastes = $this->filteredQuery($request)
            ->with(['category', 'user', 'collectionPoint'])
            ->orderBy('id')
            ->get();

        $pdf = Pdf::loadView('waste.pdf', [
            'wastes' => $wastes,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('dechets-' . date('Y-m-d-His') . '.pdf');
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

        $columns = $this->mapColumns($rows[0]);
        $hasHeader = count($columns) >= 2;
        $dataRows = $hasHeader ? array_slice($rows, 1) : $rows;

        if (! $hasHeader) {
            $columns = array_flip([
                'type', 'weight', 'status', 'description',
                'waste_category_id', 'user_id', 'collection_point_id',
            ]);
        }

        $imported = 0;
        $errors = [];

        foreach ($dataRows as $line => $row) {
            $attributes = $this->buildAttributes($row, $columns);

            if ($attributes['type'] === '') {
                $errors[] = 'Ligne ' . ($line + ($hasHeader ? 2 : 1)) . ' : type manquant.';
                continue;
            }

            try {
                Waste::create($attributes);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = 'Ligne ' . ($line + ($hasHeader ? 2 : 1)) . ' : ' . $e->getMessage();
            }
        }

        $message = $imported . ' déchet(s) importé(s).';
        if (! empty($errors)) {
            $message .= ' ' . count($errors) . ' ligne(s) ignorée(s).';
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', array_slice($errors, 0, 20));
    }

    // =========================================================================
    // HELPERS PRIVÉS
    // =========================================================================

    /**
     * Construit la requête filtrée (recherche + statut + catégorie).
     */
    protected function filteredQuery(Request $request): Builder
    {
        $query = Waste::query()->with(['category', 'user', 'collectionPoint']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('collectionPoint', fn ($point) => $point->where('name', 'like', "%{$search}%"));
            });
        }

        if (in_array($request->query('status'), ['recyclable', 'reusable'], true)) {
            $query->where('status', $request->query('status'));
        }

        if (is_numeric($request->query('category'))) {
            $query->where('waste_category_id', (int) $request->query('category'));
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
            'type' => ['type', 'waste type', 'dechet', 'déchet', 'materiau', 'matériau'],
            'weight' => ['weight', 'poids', 'masse', 'kg'],
            'status' => ['status', 'statut', 'etat', 'état'],
            'description' => ['description', 'desc', 'details', 'détails'],
            'waste_category_id' => ['waste_category_id', 'category_id', 'categorie id', 'catégorie id', 'categorie', 'catégorie'],
            'user_id' => ['user_id', 'utilisateur id', 'user', 'utilisateur'],
            'collection_point_id' => ['collection_point_id', 'point de collecte', 'point collecte', 'collection point'],
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
        $statusMap = [
            'recyclable' => 'recyclable',
            'reusable' => 'reusable',
            'reutilisable' => 'reusable',
            'réutilisable' => 'reusable',
        ];
        $status = $statusMap[$status] ?? $status;
        if (! in_array($status, ['recyclable', 'reusable'], true)) {
            $status = 'recyclable';
        }

        return [
            'type' => trim((string) ($get('type') ?? '')),
            'weight' => $this->parseWeight($get('weight')),
            'status' => $status,
            'description' => trim((string) ($get('description') ?? '')),
            'waste_category_id' => $this->parseId($get('waste_category_id')),
            'user_id' => $this->parseId($get('user_id')),
            'collection_point_id' => $this->parseId($get('collection_point_id')),
        ];
    }

    protected function parseWeight($value): float
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value)) {
            return 0.0;
        }
        $number = (float) $value;
        return ($number >= 0.1 && $number <= 1000) ? $number : 0.0;
    }

    protected function parseId($value): ?int
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value)) {
            return null;
        }
        return (int) $value;
    }

    /**
     * Additional validation for image upload
     */
    private function validateImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            list($width, $height) = getimagesize($image->getPathname());
            if ($width > 4000 || $height > 4000) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'image' => 'Image dimensions cannot exceed 4000x4000 pixels.'
                ]);
            }

            $aspectRatio = $width / $height;
            if ($aspectRatio < 0.5 || $aspectRatio > 2) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'image' => 'Image aspect ratio must be between 0.5 and 2.'
                ]);
            }
        }
    }

    /**
     * Additional validation for weight precision
     */
    private function validateWeightPrecision(Request $request)
    {
        if ($request->filled('weight')) {
            $weight = $request->input('weight');

            if (preg_match('/\.\d{3,}/', $weight)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'weight' => 'Weight cannot have more than 2 decimal places.'
                ]);
            }

            if ($weight < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'weight' => 'Weight cannot be negative.'
                ]);
            }
        }
    }
}