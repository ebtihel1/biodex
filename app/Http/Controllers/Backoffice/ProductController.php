<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WasteCategory;
use App\Models\RecyclingProcess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Colonnes autorisées pour le tri serveur.
     */
    protected array $sortable = [
        'id', 'name', 'price', 'stock_quantity', 'is_available',
        'waste_category_id', 'recycling_process_id', 'created_at',
    ];

    /**
     * Display a listing of the products.
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
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available_count")
            ->selectRaw("SUM(CASE WHEN is_available = 0 THEN 1 ELSE 0 END) as unavailable_count")
            ->selectRaw('COALESCE(SUM(stock_quantity), 0) as total_stock')
            ->selectRaw('COALESCE(SUM(price * stock_quantity), 0) as total_value')
            ->first();

        $categories = WasteCategory::query()->orderBy('name')->get(['id', 'name']);
        $recyclingProcesses = RecyclingProcess::query()
            ->where('status', 'completed')
            ->with('waste')
            ->orderBy('id')
            ->get(['id', 'method', 'waste_id']);

        $perPage = (int) $request->query('per_page', 12);
        if (! in_array($perPage, [5, 12, 25, 50, 100], true)) {
            $perPage = 12;
        }

        $products = $query->paginate($perPage)->withQueryString();

        return view('back.products.index', compact(
            'products',
            'summary',
            'categories',
            'recyclingProcesses'
        ));
    }

    public function create()
    {
        $categories = WasteCategory::all();
        $recyclingProcesses = RecyclingProcess::where('status', 'completed')
            ->with('waste')
            ->get();

        return view('back.products.create', compact('categories', 'recyclingProcesses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'waste_category_id' => 'required|exists:waste_categories,id',
            'recycling_process_id' => 'nullable|exists:recycling_processes,id',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|string',
            'is_available' => 'boolean',
        ]);

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('products', 'public');
        }

        if (isset($validated['specifications'])) {
            $validated['specifications'] = json_decode($validated['specifications'], true)
                ?? ['description' => $validated['specifications']];
        }

        $validated['is_available'] = $validated['stock_quantity'] > 0;

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show($id)
    {
        $product = Product::with(['category', 'recyclingProcess', 'recyclingProcess.waste', 'orders'])
            ->findOrFail($id);

        return view('back.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = WasteCategory::all();
        $recyclingProcesses = RecyclingProcess::where('status', 'completed')
            ->with('waste')
            ->get();

        return view('back.products.edit', compact('product', 'categories', 'recyclingProcesses'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'waste_category_id' => 'required|exists:waste_categories,id',
            'recycling_process_id' => 'nullable|exists:recycling_processes,id',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|string',
            'is_available' => 'boolean',
        ]);

        if ($request->hasFile('image_path')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image_path')->store('products', 'public');
        }

        if (isset($validated['specifications'])) {
            $validated['specifications'] = json_decode($validated['specifications'], true)
                ?? ['description' => $validated['specifications']];
        }

        $validated['is_available'] = $validated['stock_quantity'] > 0;

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->orders()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Cannot delete this product because orders are associated with it.');
        }

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function toggleAvailability($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_available' => !$product->is_available]);

        $status = $product->is_available ? 'available' : 'unavailable';
        return redirect()->route('products.index')
            ->with('success', "Product marked as {$status}.");
    }

    // =========================================================================
    // EXPORT CSV
    // =========================================================================
    public function exportCsv(Request $request)
    {
        $query = $this->filteredQuery($request)->orderBy('id');
        $filename = 'produits-' . date('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Nom', 'Description', 'Prix', 'Stock',
                'Catégorie', 'Processus de recyclage', 'Disponible',
                'Spécifications', 'Image', 'Créé le',
            ], ';');

            $query->chunk(200, function ($products) use ($handle) {
                foreach ($products as $product) {
                    fputcsv($handle, [
                        $product->id,
                        $product->name,
                        $product->description,
                        $product->price,
                        $product->stock_quantity,
                        optional($product->category)->name,
                        optional($product->recyclingProcess)->method,
                        $product->is_available ? 'Oui' : 'Non',
                        $this->encodeList($product->specifications),
                        $product->image_path,
                        optional($product->created_at)->format('Y-m-d H:i:s'),
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
        $products = $this->filteredQuery($request)->orderBy('id')->get();

        $pdf = Pdf::loadView('back.products.pdf', [
            'products' => $products,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('produits-' . date('Y-m-d-His') . '.pdf');
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
                'name', 'description', 'price', 'stock_quantity',
                'waste_category_id', 'recycling_process_id',
                'specifications', 'is_available',
            ]);
        }

        $imported = 0;
        $errors = [];

        foreach ($dataRows as $line => $row) {
            $attributes = $this->buildAttributes($row, $columns);
            $lineNumber = $line + ($hasHeader ? 2 : 1);

            if ($attributes['name'] === '') {
                $errors[] = "Ligne {$lineNumber} : nom manquant.";
                continue;
            }

            if ($attributes['description'] === '') {
                $errors[] = "Ligne {$lineNumber} : description manquante.";
                continue;
            }

            if (empty($attributes['waste_category_id'])) {
                $errors[] = "Ligne {$lineNumber} : waste_category_id manquant.";
                continue;
            }

            if (! WasteCategory::whereKey($attributes['waste_category_id'])->exists()) {
                $errors[] = "Ligne {$lineNumber} : waste_category_id {$attributes['waste_category_id']} inexistant.";
                continue;
            }

            if ($attributes['recycling_process_id'] !== null
                && ! RecyclingProcess::whereKey($attributes['recycling_process_id'])->exists()) {
                $errors[] = "Ligne {$lineNumber} : recycling_process_id {$attributes['recycling_process_id']} inexistant.";
                continue;
            }

            try {
                Product::create($attributes);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Ligne {$lineNumber} : " . $e->getMessage();
            }
        }

        $message = $imported . ' produit(s) importé(s).';
        if (! empty($errors)) {
            $message .= ' ' . count($errors) . ' ligne(s) ignorée(s).';
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', array_slice($errors, 0, 20));
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    protected function filteredQuery(Request $request): Builder
    {
        $query = Product::query()->with(['category', 'recyclingProcess', 'recyclingProcess.waste']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('recyclingProcess', fn ($rp) => $rp->where('method', 'like', "%{$search}%"));
            });
        }

        if (in_array($request->query('available'), ['0', '1'], true)) {
            $query->where('is_available', (int) $request->query('available'));
        }

        if (is_numeric($request->query('category'))) {
            $query->where('waste_category_id', (int) $request->query('category'));
        }

        if (is_numeric($request->query('recycling_process'))) {
            $query->where('recycling_process_id', (int) $request->query('recycling_process'));
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
            'name' => ['name', 'nom', 'title', 'libelle', 'produit'],
            'description' => ['description', 'desc', 'details', 'détails'],
            'price' => ['price', 'prix', 'tarif', 'montant'],
            'stock_quantity' => ['stock_quantity', 'stock', 'quantite', 'quantité', 'qte'],
            'waste_category_id' => ['waste_category_id', 'category_id', 'categorie id', 'catégorie id', 'categorie', 'catégorie'],
            'recycling_process_id' => ['recycling_process_id', 'process_id', 'processus', 'recycling process'],
            'specifications' => ['specifications', 'specs', 'caracteristiques', 'caractéristiques'],
            'is_available' => ['is_available', 'available', 'disponible', 'dispo'],
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

        $stockQuantity = $this->parseInt($get('stock_quantity')) ?? 0;
        $isAvailable = $this->parseBool($get('is_available')) ?? ($stockQuantity > 0);

        return [
            'name' => trim((string) ($get('name') ?? '')),
            'description' => trim((string) ($get('description') ?? '')),
            'price' => $this->parseDecimal($get('price')) ?? 0.0,
            'stock_quantity' => $stockQuantity,
            'waste_category_id' => $this->parseId($get('waste_category_id')),
            'recycling_process_id' => $this->parseId($get('recycling_process_id')),
            'specifications' => $this->parseList($get('specifications')),
            'is_available' => (bool) $isAvailable,
            'image_path' => null,
        ];
    }

    protected function parseInt($value): ?int
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value)) return null;
        return (int) $value;
    }

    protected function parseId($value): ?int
    {
        return $this->parseInt($value);
    }

    protected function parseDecimal($value): ?float
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value)) return null;
        $n = (float) $value;
        return $n < 0 ? null : $n;
    }

    protected function parseBool($value): ?bool
    {
        $value = strtolower(trim((string) $value));
        if ($value === '') return null;

        if (in_array($value, ['1', 'yes', 'oui', 'true', 'vrai', 'disponible', 'available'], true)) {
            return true;
        }
        if (in_array($value, ['0', 'no', 'non', 'false', 'faux', 'indisponible', 'unavailable'], true)) {
            return false;
        }
        return null;
    }

    protected function parseList($value): ?array
    {
        $value = trim((string) $value);
        if ($value === '') return null;

        $decoded = json_decode($value, true);
        if (is_array($decoded)) return $decoded;

        return ['description' => $value];
    }

    protected function encodeList($value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        return (string) $value;
    }
}