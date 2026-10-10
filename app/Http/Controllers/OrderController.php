<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\RecommendationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Colonnes autorisées pour le tri serveur.
     */
    protected array $sortable = [
        'id', 'user_id', 'product_id', 'quantity',
        'total_amount', 'status', 'order_date', 'created_at',
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
        return $this->getPrefixedRoute('orders.index');
    }

    private function getCreateRoute()
    {
        return $this->getPrefixedRoute('orders.create');
    }

    private function getStoreRoute()
    {
        return $this->getPrefixedRoute('orders.store');
    }

    private function getShowRoute($order = null)
    {
        $name = $this->getPrefixedRoute('orders.show');
        return $order ? route($name, $order) : $name;
    }

    private function getEditRoute($order = null)
    {
        $name = $this->getPrefixedRoute('orders.edit');
        return $order ? route($name, $order) : $name;
    }

    private function getUpdateRoute($order = null)
    {
        $name = $this->getPrefixedRoute('orders.update');
        return $order ? route($name, $order) : $name;
    }

    private function getDestroyRoute($order = null)
    {
        $name = $this->getPrefixedRoute('orders.destroy');
        return $order ? route($name, $order) : $name;
    }

    private function getExportCsvRoute()
    {
        return $this->getPrefixedRoute('orders.export.csv');
    }

    private function getExportPdfRoute()
    {
        return $this->getPrefixedRoute('orders.export.pdf');
    }

    private function getImportRoute()
    {
        return $this->getPrefixedRoute('orders.import');
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
            ->selectRaw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_count")
            ->selectRaw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count")
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();

        $products = Product::query()->orderBy('name')->get(['id', 'name']);
        $users = User::query()->orderBy('name')->get(['id', 'name']);

        $perPage = (int) $request->query('per_page', 10);
        if (! in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $orders = $query->paginate($perPage)->withQueryString();

        $viewPrefix = $this->getViewPrefix();

        return view($viewPrefix . 'orders.index', [
            'orders' => $orders,
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
        $products = Product::available()->get(['id', 'name', 'price']);
        $viewPrefix = $this->getViewPrefix();
        $storeRoute = $this->getStoreRoute();
        $indexRoute = $this->getIndexRoute();

        $recommendations = [];
        if (Auth::check()) {
            $service = new RecommendationService();
            $recommendations = $service->suggestForUser(Auth::user());
        }

        return view($viewPrefix . 'orders.create', compact('products', 'storeRoute', 'indexRoute', 'recommendations'));
    }

    // ======================= STORE =======================
    public function store(Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cash,card,transfer',
        ];

        if (!$this->isFrontRoute()) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        if ($this->isFrontRoute() && !isset($validated['user_id'])) {
            if (!Auth::check()) {
                return redirect()->back()->withErrors(['user' => 'Authentication required to create an order.']);
            }
            $validated['user_id'] = Auth::id();
        }

        $product = Product::findOrFail($validated['product_id']);
        $validated['total_amount'] = $validated['quantity'] * $product->price;

        Order::create(array_merge($validated, [
            'status' => OrderStatus::Pending,
            'order_date' => now(),
        ]));

        $product->decrementStock($validated['quantity']);

        return redirect()->route($this->getIndexRoute())
            ->with('success', 'Order created successfully!');
    }

    // ======================= SHOW =======================
    public function show(Order $order)
    {
        $order->load(['user', 'product']);
        $viewPrefix = $this->getViewPrefix();
        $indexRoute = $this->getIndexRoute();
        $editRoute = $this->getEditRoute($order);
        $destroyRoute = $this->getDestroyRoute($order);
        return view($viewPrefix . 'orders.show', compact('order', 'indexRoute', 'editRoute', 'destroyRoute'));
    }

    // ======================= EDIT =======================
    public function edit(Order $order)
    {
        $order->load(['user', 'product']);
        $products = Product::available()->get(['id', 'name', 'price']);
        $viewPrefix = $this->getViewPrefix();
        $updateRoute = $this->getUpdateRoute($order);
        $showRoute = $this->getShowRoute($order);
        $indexRoute = $this->getIndexRoute();
        return view($viewPrefix . 'orders.edit', compact('order', 'products', 'updateRoute', 'showRoute', 'indexRoute'));
    }

    // ======================= UPDATE =======================
    public function update(Request $request, Order $order)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cash,card,transfer',
            'tracking_number' => 'nullable|string|max:100',
        ];

        if (!$this->isFrontRoute()) {
            $rules['status'] = ['required', Rule::enum(OrderStatus::class)];
        }

        $validated = $request->validate($rules);

        if ($this->isFrontRoute() && !isset($validated['status'])) {
            $validated['status'] = $order->status;
        }

        $product = Product::findOrFail($validated['product_id']);
        $validated['total_amount'] = $validated['quantity'] * $product->price;

        if ($validated['quantity'] != $order->quantity) {
            $diff = $validated['quantity'] - $order->quantity;
            if ($diff > 0) {
                $product->decrementStock($diff);
            } else {
                $product->increment('stock_quantity', abs($diff));
            }
        }

        $order->update($validated);

        return redirect()->route($this->getIndexRoute())->with('success', 'Order updated!');
    }

    // ======================= DESTROY =======================
    public function destroy(Order $order)
    {
        if ($order->product) {
            $order->product->increment('stock_quantity', $order->quantity);
        }
        $order->delete();
        return redirect()->route($this->getIndexRoute())->with('success', 'Order deleted!');
    }

    // ======================= EXPORT CSV =======================
    public function exportCsv(Request $request)
    {
        $query = $this->filteredQuery($request)->orderBy('id');
        $filename = 'commandes-' . date('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Utilisateur', 'Produit', 'Quantité',
                'Montant total', 'Statut', 'Adresse de livraison',
                'Mode de paiement', 'Numéro de suivi', 'Date',
            ], ';');

            $query->chunk(200, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    fputcsv($handle, [
                        $order->id,
                        optional($order->user)->name,
                        optional($order->product)->name,
                        $order->quantity,
                        $order->total_amount,
                        is_object($order->status) ? $order->status->value : $order->status,
                        $order->shipping_address,
                        $order->payment_method,
                        $order->tracking_number,
                        optional($order->order_date)->format('Y-m-d H:i:s'),
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ======================= EXPORT PDF =======================
    public function exportPdf(Request $request)
    {
        $orders = $this->filteredQuery($request)->orderBy('id')->get();

        $pdf = Pdf::loadView($this->getViewPrefix() . 'orders.pdf', [
            'orders' => $orders,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('commandes-' . date('Y-m-d-His') . '.pdf');
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
                'user_id', 'product_id', 'quantity', 'shipping_address',
                'payment_method', 'status', 'tracking_number',
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

            try {
                $product = Product::find($attributes['product_id']);
                $attributes['total_amount'] = $attributes['quantity'] * $product->price;
                $attributes['order_date'] = now();

                Order::create($attributes);
                $product->decrementStock($attributes['quantity']);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Ligne {$lineNumber} : " . $e->getMessage();
            }
        }

        $message = $imported . ' commande(s) importée(s).';
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
        $query = Order::query()->with(['user', 'product']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('shipping_address', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('product_search')) {
            $ps = $request->query('product_search');
            $query->whereHas('product', fn ($q) => $q->where('name', 'like', "%{$ps}%"));
        }

        if ($request->filled('user')) {
            $query->where('user_id', (int) $request->query('user'));
        }

        if ($request->filled('date_from')) {
            $query->where('order_date', '>=', $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('order_date', '<=', $request->query('date_to') . ' 23:59:59');
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
            'shipping_address' => ['shipping address', 'shipping_address', 'adresse', 'adresse livraison'],
            'payment_method' => ['payment method', 'payment_method', 'paiement', 'mode paiement'],
            'status' => ['status', 'statut', 'etat', 'état'],
            'tracking_number' => ['tracking number', 'tracking_number', 'suivi', 'numero suivi'],
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
        $allowed = array_map(fn ($s) => $s->value, OrderStatus::cases());
        if (! in_array($status, $allowed, true)) {
            $status = 'pending';
        }

        return [
            'user_id' => $this->parseId($get('user_id')),
            'product_id' => $this->parseId($get('product_id')),
            'quantity' => max(1, (int) ($this->parseId($get('quantity')) ?? 1)),
            'shipping_address' => trim((string) ($get('shipping_address') ?? '')),
            'payment_method' => in_array($get('payment_method'), ['cash', 'card', 'transfer'], true)
                ? $get('payment_method') : 'cash',
            'status' => $status,
            'tracking_number' => $this->nullIfEmpty($get('tracking_number')),
        ];
    }

    protected function parseId($value): ?int
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value)) return null;
        return (int) $value;
    }

    protected function nullIfEmpty($value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    public static function getProductName($productId)
    {
        return Product::find($productId)?->name ?? 'N/A';
    }
}