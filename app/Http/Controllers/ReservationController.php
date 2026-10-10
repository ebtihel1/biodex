<?php

namespace App\Http\Controllers;

use App\Enums\ReservationStatus;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use App\Services\RecommendationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    /**
     * Colonnes autorisées pour le tri serveur.
     */
    protected array $sortable = [
        'id', 'user_id', 'product_id', 'quantity',
        'reserved_until', 'status', 'created_at',
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
        return $this->getPrefixedRoute('reservations.index');
    }

    private function getCreateRoute()
    {
        return $this->getPrefixedRoute('reservations.create');
    }

    private function getStoreRoute()
    {
        return $this->getPrefixedRoute('reservations.store');
    }

    private function getShowRoute($reservation = null)
    {
        $name = $this->getPrefixedRoute('reservations.show');
        return $reservation ? route($name, $reservation) : $name;
    }

    private function getEditRoute($reservation = null)
    {
        $name = $this->getPrefixedRoute('reservations.edit');
        return $reservation ? route($name, $reservation) : $name;
    }

    private function getUpdateRoute($reservation = null)
    {
        $name = $this->getPrefixedRoute('reservations.update');
        return $reservation ? route($name, $reservation) : $name;
    }

    private function getDestroyRoute($reservation = null)
    {
        $name = $this->getPrefixedRoute('reservations.destroy');
        return $reservation ? route($name, $reservation) : $name;
    }

    // Retourne les NOMS de route (pour usage dans route(...) côté vue)
    private function getExportCsvRoute()
    {
        return $this->getPrefixedRoute('reservations.export.csv');
    }

    private function getExportPdfRoute()
    {
        return $this->getPrefixedRoute('reservations.export.pdf');
    }

    private function getImportRoute()
    {
        return $this->getPrefixedRoute('reservations.import');
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
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count")
            ->selectRaw("SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_count")
            ->selectRaw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count")
            ->first();

        $products = Product::query()->orderBy('name')->get(['id', 'name', 'price']);
        $users = User::query()->orderBy('name')->get(['id', 'name']);

        $perPage = (int) $request->query('per_page', 10);
        if (! in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $reservations = $query->paginate($perPage)->withQueryString();

        $viewPrefix = $this->getViewPrefix();

        return view($viewPrefix . 'reservations.index', [
            'reservations' => $reservations,
            'summary' => $summary,
            'products' => $products,
            'users' => $users,
            'createRoute' => $this->getCreateRoute(),
            'exportCsvRoute' => $this->getExportCsvRoute(),
            'exportPdfRoute' => $this->getExportPdfRoute(),
            'importRoute' => $this->getImportRoute(),
        ]);
    }

    // ======================= CREATE =======================
    public function create()
    {
        $products = Product::available()
            ->get()
            ->mapWithKeys(function ($product) {
                return [$product->id => ['name' => $product->name, 'price' => $product->price]];
            })
            ->toArray();

        $viewPrefix = $this->getViewPrefix();
        $storeRoute = $this->getStoreRoute();
        $indexRoute = $this->getIndexRoute();

        $recommendations = [];
        if (Auth::check()) {
            $service = new RecommendationService();
            $recommendations = $service->suggestForUser(Auth::user());
        }

        return view($viewPrefix . 'reservations.create', compact('products', 'storeRoute', 'indexRoute', 'recommendations'));
    }

    // ======================= STORE =======================
    public function store(Request $request)
    {
        $rules = [
            'product_id' => ['required', Rule::exists('products', 'id')],
            'quantity' => 'required|integer|min:1',
            'reserved_until' => 'required|date|after:now',
        ];

        if (!$this->isFrontRoute()) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        if ($this->isFrontRoute() && !isset($validated['user_id'])) {
            if (!Auth::check()) {
                return redirect()->back()->withErrors(['user' => 'Authentication required to create a reservation.']);
            }
            $validated['user_id'] = Auth::id();
        }

        Reservation::create(array_merge($validated, [
            'status' => ReservationStatus::Pending,
        ]));

        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Reservation created successfully!');
    }

    // ======================= SHOW =======================
    public function show(Reservation $reservation)
    {
        $reservation->load('user');
        $viewPrefix = $this->getViewPrefix();
        $indexRoute = $this->getIndexRoute();
        $editRoute = $this->getEditRoute($reservation);
        $destroyRoute = $this->getDestroyRoute($reservation);
        return view($viewPrefix . 'reservations.show', compact('reservation', 'indexRoute', 'editRoute', 'destroyRoute'));
    }

    // ======================= EDIT =======================
    public function edit(Reservation $reservation)
    {
        $reservation->load('user');
        $products = Product::available()
            ->get()
            ->mapWithKeys(function ($product) {
                return [$product->id => ['name' => $product->name, 'price' => $product->price]];
            })
            ->toArray();

        $viewPrefix = $this->getViewPrefix();
        $updateRoute = $this->getUpdateRoute($reservation);
        $showRoute = $this->getShowRoute($reservation);
        $indexRoute = $this->getIndexRoute();
        return view($viewPrefix . 'reservations.edit', compact('reservation', 'products', 'updateRoute', 'showRoute', 'indexRoute'));
    }

    // ======================= UPDATE =======================
    public function update(Request $request, Reservation $reservation)
    {
        $rules = [
            'product_id' => ['required', Rule::exists('products', 'id')],
            'quantity' => 'required|integer|min:1',
            'reserved_until' => 'required|date|after:now',
        ];

        if (!$this->isFrontRoute()) {
            $rules['status'] = ['required', Rule::enum(ReservationStatus::class)];
        }

        $validated = $request->validate($rules);

        if ($this->isFrontRoute() && !isset($validated['status'])) {
            $validated['status'] = $reservation->status;
        }

        $reservation->update($validated);

        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Reservation updated!');
    }

    // ======================= DESTROY =======================
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Reservation deleted!');
    }

    // ======================= EXPORT CSV =======================
    public function exportCsv(Request $request)
    {
        $query = $this->filteredQuery($request)->orderBy('id');
        $filename = 'reservations-' . date('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Utilisateur', 'Produit', 'Quantité',
                'Statut', 'Réservé jusqu\'au', 'Créé le',
            ], ';');

            $query->chunk(200, function ($reservations) use ($handle) {
                foreach ($reservations as $reservation) {
                    fputcsv($handle, [
                        $reservation->id,
                        optional($reservation->user)->name,
                        self::getProductName($reservation->product_id),
                        $reservation->quantity,
                        is_object($reservation->status) ? $reservation->status->value : $reservation->status,
                        optional($reservation->reserved_until)->format('Y-m-d H:i:s'),
                        optional($reservation->created_at)->format('Y-m-d H:i:s'),
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ======================= EXPORT PDF =======================
    public function exportPdf(Request $request)
    {
        $reservations = $this->filteredQuery($request)->orderBy('id')->get();

        $pdf = Pdf::loadView($this->getViewPrefix() . 'reservations.pdf', [
            'reservations' => $reservations,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reservations-' . date('Y-m-d-His') . '.pdf');
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
                'user_id', 'product_id', 'quantity',
                'status', 'reserved_until',
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
            if (empty($attributes['product_id'])) {
                $errors[] = "Ligne {$lineNumber} : product_id manquant.";
                continue;
            }
            if (! Product::whereKey($attributes['product_id'])->exists()) {
                $errors[] = "Ligne {$lineNumber} : product_id {$attributes['product_id']} inexistant.";
                continue;
            }
            if ($attributes['quantity'] < 1) {
                $errors[] = "Ligne {$lineNumber} : quantité invalide.";
                continue;
            }
            if (empty($attributes['reserved_until'])) {
                $errors[] = "Ligne {$lineNumber} : reserved_until manquant.";
                continue;
            }

            try {
                Reservation::create($attributes);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Ligne {$lineNumber} : " . $e->getMessage();
            }
        }

        $message = $imported . ' réservation(s) importée(s).';
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
        $query = Reservation::query()->with(['user']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        if (request('user_search')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%' . request('user_search') . '%'));
        }

        if (request('product_search')) {
            $search = request('product_search');
            $query->whereHas('product', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('user')) {
            $query->where('user_id', (int) $request->query('user'));
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
            'product_id' => ['product_id', 'product', 'produit'],
            'quantity' => ['quantity', 'quantite', 'quantité', 'qte', 'qty'],
            'status' => ['status', 'statut'],
            'reserved_until' => ['reserved_until', 'reserved until', 'reserve jusqua', 'réservé jusqu\'au', 'expire', 'date expiration'],
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

        $status = strtolower(trim((string) ($get('status') ?? 'pending')));
        $allowed = array_map(fn ($s) => $s->value, ReservationStatus::cases());
        if (! in_array($status, $allowed, true)) {
            $status = 'pending';
        }

        return [
            'user_id' => $this->parseId($get('user_id')),
            'product_id' => $this->parseId($get('product_id')),
            'quantity' => max(1, (int) ($this->parseId($get('quantity')) ?? 1)),
            'status' => $status,
            'reserved_until' => $this->parseDate($get('reserved_until')),
        ];
    }

    protected function parseId($value): ?int
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value)) return null;
        return (int) $value;
    }

    protected function parseDate($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') return null;
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    // ======================= STATIC HELPER =======================
    public static function getProductName($productId)
    {
        $product = Product::find($productId);
        return $product ? $product->name : 'N/A';
    }
}