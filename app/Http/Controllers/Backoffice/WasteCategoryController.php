<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WasteCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;

class WasteCategoryController extends Controller
{
    /**
     * Colonnes autorisées pour le tri serveur.
     */
    protected array $sortable = [
        'id', 'name', 'description', 'recycling_instructions', 'created_at',
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

        $summary = WasteCategory::query()
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN recycling_instructions IS NOT NULL AND recycling_instructions != '' THEN 1 ELSE 0 END) as with_instructions_count")
            ->first();

        $perPage = (int) $request->query('per_page', 10);
        if (! in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $categories = $query->paginate($perPage)->withQueryString();
        $search = trim((string) $request->query('search', ''));

        return view('wastecategory.list', compact('categories', 'summary', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('wastecategory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:waste_categories,name',
                'regex:/^[a-zA-Z0-9\s\-]+$/'
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'recycling_instructions' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'name.required' => 'Category name is required.',
            'name.max' => 'Name must not exceed 100 characters.',
            'name.unique' => 'This category name already exists.',
            'name.regex' => 'Name can only contain letters, numbers, spaces, and hyphens.',
            'description.max' => 'Description must not exceed 500 characters.',
            'recycling_instructions.max' => 'Instructions must not exceed 1000 characters.',
        ]);

        $this->validateNoEmptyStrings($request);

        WasteCategory::create($validated);

        return redirect()->route('waste_categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = WasteCategory::findOrFail($id);
        return view('wastecategory.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = WasteCategory::findOrFail($id);
        return view('wastecategory.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = WasteCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:waste_categories,name,' . $id,
                'regex:/^[a-zA-Z0-9\s\-]+$/'
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'recycling_instructions' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'name.required' => 'Category name is required.',
            'name.max' => 'Name must not exceed 100 characters.',
            'name.unique' => 'This category name already exists.',
            'name.regex' => 'Name can only contain letters, numbers, spaces, and hyphens.',
            'description.max' => 'Description must not exceed 500 characters.',
            'recycling_instructions.max' => 'Instructions must not exceed 1000 characters.',
        ]);

        $this->validateNoEmptyStrings($request);

        $category->update($validated);

        return redirect()->route('waste_categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = WasteCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('waste_categories.index')->with('success', 'Category deleted successfully.');
    }

    // =========================================================================
    // EXPORT CSV
    // =========================================================================
    public function exportCsv(Request $request)
    {
        $query = $this->filteredQuery($request)->orderBy('id');
        $filename = 'categories-dechets-' . date('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 pour une ouverture correcte dans Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Nom', 'Description', 'Instructions de recyclage', 'Créé le',
            ], ';');

            $query->chunk(200, function ($categories) use ($handle) {
                foreach ($categories as $category) {
                    fputcsv($handle, [
                        $category->id,
                        $category->name,
                        $category->description,
                        $category->recycling_instructions,
                        optional($category->created_at)->format('Y-m-d H:i:s'),
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
        $categories = $this->filteredQuery($request)->orderBy('id')->get();

        $pdf = Pdf::loadView('wastecategory.pdf', [
            'categories' => $categories,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('categories-dechets-' . date('Y-m-d-His') . '.pdf');
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
                'name', 'description', 'recycling_instructions',
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

            try {
                WasteCategory::create($attributes);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = 'Ligne ' . ($line + ($hasHeader ? 2 : 1)) . ' : ' . $e->getMessage();
            }
        }

        $message = $imported . ' catégorie(s) importée(s).';
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
     * Construit la requête filtrée (recherche).
     */
    protected function filteredQuery(Request $request): Builder
    {
        $query = WasteCategory::query();

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('recycling_instructions', 'like', "%{$search}%");
            });
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
            'name' => ['name', 'nom', 'title', 'libelle', 'categorie', 'catégorie'],
            'description' => ['description', 'desc', 'details', 'détails'],
            'recycling_instructions' => ['recycling instructions', 'instructions', 'recyclage', 'consignes'],
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

        return [
            'name' => trim((string) ($get('name') ?? '')),
            'description' => $this->nullIfEmpty($get('description')),
            'recycling_instructions' => $this->nullIfEmpty($get('recycling_instructions')),
        ];
    }

    protected function nullIfEmpty($value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    /**
     * Additional validation to prevent empty strings containing only spaces.
     */
    private function validateNoEmptyStrings(Request $request)
    {
        $textFields = ['name', 'description', 'recycling_instructions'];

        foreach ($textFields as $field) {
            if ($request->filled($field) && trim($request->$field) === '') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    $field => "This field cannot contain only spaces."
                ]);
            }
        }
    }
}