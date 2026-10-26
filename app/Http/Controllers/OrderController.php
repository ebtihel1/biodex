<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Cart;
use App\Services\RecommendationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
            ->selectRaw("SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_count")
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
    public function create(Request $request)
    {
        if ($this->isFrontRoute()) {
            // For front, get user's cart
            $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();

            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('front.cart.index')
                    ->with('error', 'Your cart is empty. Please add items before checkout.');
            }

            $viewPrefix = $this->getViewPrefix();
            $storeRoute = $this->getStoreRoute();
            $indexRoute = $this->getIndexRoute();

            return view($viewPrefix . 'orders.create', compact(
                'cart',
                'storeRoute',
                'indexRoute'
            ));
        } else {
            // For back, show product selection (admin creating orders)
            $products = Product::available()->get(['id', 'name', 'price']);
            $users = User::all(['id', 'name']);

            $viewPrefix = $this->getViewPrefix();
            $storeRoute = $this->getStoreRoute();
            $indexRoute = $this->getIndexRoute();

            return view($viewPrefix . 'orders.create', compact(
                'products',
                'users',
                'storeRoute',
                'indexRoute'
            ));
        }
    }

    // ======================= STORE =======================
    public function store(Request $request)
    {
        $rules = [
            'shipping_address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cash',
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

        // Get user's cart
        $cart = Cart::with('items.product')->where('user_id', $validated['user_id'])->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->back()->withErrors(['cart' => 'Your cart is empty.']);
        }

        // Validate stock for all items
        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->stock_quantity) {
                return redirect()->back()->withErrors([
                    'stock' => "Not enough stock for {$item->product->name}. Available: {$item->product->stock_quantity}, Requested: {$item->quantity}"
                ]);
            }
        }

        // Create order with items in a transaction
        DB::transaction(function () use ($validated, $cart) {
            $order = Order::create([
                'user_id' => $validated['user_id'],
                'total_amount' => $cart->total,
                'status' => OrderStatus::Confirmed,
                'order_date' => now(),
                'shipping_address' => $validated['shipping_address'],
                'payment_method' => $validated['payment_method'],
                'tracking_number' => null,
            ]);

            // Create order items and decrement stock
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]);

                $item->product->decrementStock($item->quantity);
            }

            // Clear the cart
            $cart->items()->delete();
        });

        // Invalider le cache des recommandations après une nouvelle commande
        $recommendationService = new RecommendationService();
        $recommendationService->invalidateCache();

        return redirect()->route($this->getIndexRoute())
            ->with('success', 'Order created successfully!');
    }

    // ======================= SHOW =======================
    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        $viewPrefix = $this->getViewPrefix();
        $indexRoute = $this->getIndexRoute();
        $editRoute = $this->getEditRoute($order);
        $destroyRoute = $this->getDestroyRoute($order);
        return view($viewPrefix . 'orders.show', compact('order', 'indexRoute', 'editRoute', 'destroyRoute'));
    }

    // ======================= EDIT =======================
    public function edit(Order $order)
    {
        $order->load(['user', 'items.product']);
        $viewPrefix = $this->getViewPrefix();
        $updateRoute = $this->getUpdateRoute($order);
        $showRoute = $this->getShowRoute($order);
        $indexRoute = $this->getIndexRoute();
        return view($viewPrefix . 'orders.edit', compact('order', 'updateRoute', 'showRoute', 'indexRoute'));
    }

    // ======================= UPDATE =======================
    public function update(Request $request, Order $order)
    {
        $rules = [
            'shipping_address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cash',
            'tracking_number' => 'nullable|string|max:100',
        ];

        if (!$this->isFrontRoute()) {
            $rules['status'] = ['required', Rule::enum(OrderStatus::class)];
        }

        $validated = $request->validate($rules);

        if ($this->isFrontRoute() && !isset($validated['status'])) {
            $validated['status'] = $order->status;
        }

        // Normalize status values (enum vs string) so comparisons are reliable
        $oldStatusValue = $order->status instanceof OrderStatus ? $order->status->value : $order->status;
        $newStatusValue = $validated['status'] instanceof OrderStatus
            ? $validated['status']->value
            : $validated['status'];

        $becomingCancelled = $oldStatusValue !== OrderStatus::Cancelled->value
            && $newStatusValue === OrderStatus::Cancelled->value;

        $leavingCancelled = $oldStatusValue === OrderStatus::Cancelled->value
            && $newStatusValue !== OrderStatus::Cancelled->value;

        DB::transaction(function () use ($order, $becomingCancelled, $leavingCancelled, $validated) {
            // Restore stock when the order becomes cancelled
            if ($becomingCancelled) {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            // Re-decrement stock when leaving cancelled (reactivation)
            if ($leavingCancelled) {
                $failures = [];
                foreach ($order->items as $item) {
                    if (! $item->product) {
                        continue;
                    }
                    if ($item->quantity > $item->product->stock_quantity) {
                        $failures[] = "{$item->product->name} (need {$item->quantity}, have {$item->product->stock_quantity})";
                    }
                }

                if (! empty($failures)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'status' => 'Cannot reactivate cancelled order — insufficient stock: ' . implode('; ', $failures),
                    ]);
                }

                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->decrementStock($item->quantity);
                    }
                }
            }

            $order->update($validated);
        });

        return redirect()->route($this->getIndexRoute())->with('success', 'Order updated!');
    }

    // ======================= DESTROY =======================
    public function destroy(Order $order)
    {
        DB::transaction(function () use ($order) {
            // Stock was already restored when the order was cancelled
            // (in update() or cancel()). Do NOT restore it a second time.
            $wasCancelled = $order->status === OrderStatus::Cancelled
                || $order->status === OrderStatus::Cancelled->value;

            if (! $wasCancelled) {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            $order->delete();
        });

        return redirect()->route($this->getIndexRoute())->with('success', 'Order deleted!');
    }

    // ======================= CANCEL (for customers) =======================
    public function cancel(Order $order)
    {
        // Only allow cancellation for own orders and only if pending
        if ($this->isFrontRoute()) {
            if ($order->user_id !== Auth::id()) {
                return redirect()->back()->with('error', 'Unauthorized action.');
            }
        }

        if ($order->status !== OrderStatus::Pending->value && $order->status !== OrderStatus::Confirmed->value) {
            return redirect()->back()->with('error', 'Only pending or confirmed orders can be cancelled.');
        }

        DB::transaction(function () use ($order) {
            // Restore stock for all items
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }
            $order->update(['status' => OrderStatus::Cancelled]);
        });

        return redirect()->route($this->getIndexRoute())->with('success', 'Order cancelled successfully!');
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
        $query = Order::query()->with(['user', 'items.product']);

        if ($this->isFrontRoute()) {
            $query->where('user_id', Auth::id() ?? 0);
        }

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('shipping_address', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('items.product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('product_search')) {
            $ps = $request->query('product_search');
            $query->whereHas('items.product', fn ($q) => $q->where('name', 'like', "%{$ps}%"));
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