<?php


namespace App\Http\Controllers;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Product;  
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    // REMOVED: private static $products = [...];  // No longer needed

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

    public function index()
    {
        // NEW: Load DB products for search (replaces self::$products)
        $dbProducts = Product::available()->get()->keyBy('id')->toArray();  // id => full model array

        $query = Reservation::with('user')
            ->when(request('date_from'), function ($query) {
                return $query->where('created_at', '>=', request('date_from'));
            })
            ->when(request('date_to'), function ($query) {
                return $query->where('created_at', '<=', request('date_to'));
            })
            ->when(request('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when(request('product_search'), function ($query, $search) use ($dbProducts) {  // NEW: use $dbProducts
                $matchingProductIds = collect($dbProducts)
                    ->filter(function ($product, $id) use ($search) {
                        return str_contains(strtolower($product['name']), strtolower($search));
                    })
                    ->keys()
                    ->toArray();
                return $query->whereIn('product_id', $matchingProductIds);
            });

        $reservations = $query->paginate(4);  // Paginate with 4 items per page

        $viewPrefix = $this->getViewPrefix();
        $createRoute = $this->getCreateRoute();
        return view($viewPrefix . 'reservations.index', compact('reservations', 'createRoute'));
    }

    public function create()
    {
        // NEW: Load DB products (replaces self::$products; format matches static: id => ['name' => ..., 'price' => ...])
        $products = Product::available()
            ->get()
            ->mapWithKeys(function ($product) {
                return [$product->id => ['name' => $product->name, 'price' => $product->price]];
            })
            ->toArray();

        $viewPrefix = $this->getViewPrefix();
        $storeRoute = $this->getStoreRoute();
        $indexRoute = $this->getIndexRoute();

        // AI Recommendations
        $recommendations = [];
        if (Auth::check()) {
            $service = new RecommendationService();
            $recommendations = $service->suggestForUser(Auth::user());
        }

        return view($viewPrefix . 'reservations.create', compact('products', 'storeRoute', 'indexRoute', 'recommendations'));
    }

    public function store(Request $request)
    {
        // Base rules without user_id for front
        $rules = [
            'product_id' => ['required', Rule::exists('products', 'id')],  // CHANGED: DB exists check (FK safe)
            'quantity' => 'required|integer|min:1',
            'reserved_until' => 'required|date|after:now',
        ];

        // Add user_id rule only for back-end (admin) routes
        if (!$this->isFrontRoute()) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        // For front-end, auto-set user_id if not provided
        if ($this->isFrontRoute() && !isset($validated['user_id'])) {
            if (!Auth::check()) {
                return redirect()->back()->withErrors(['user' => 'Authentication required to create a reservation.']);
            }
            $validated['user_id'] = Auth::id();
        }

        $reservation = Reservation::create(array_merge($validated, [
            'status' => ReservationStatus::Pending,
        ]));

        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Reservation created successfully!');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('user');
        $viewPrefix = $this->getViewPrefix();
        $indexRoute = $this->getIndexRoute();
        $editRoute = $this->getEditRoute($reservation);
        $destroyRoute = $this->getDestroyRoute($reservation);
        return view($viewPrefix . 'reservations.show', compact('reservation', 'indexRoute', 'editRoute', 'destroyRoute'));
    }

    public function edit(Reservation $reservation)
    {
        $reservation->load('user');
        // NEW: Load DB products (replaces self::$products)
        $products = Product::available()
            ->get()
            ->mapWithKeys(function ($product) {
                return [$product->id => ['name' => $product->name, 'price' => $product->price]];
            })
            ->toArray();

        $viewPrefix = $this->getViewPrefix();
        $updateRoute = $this->getUpdateRoute($reservation);
        $showRoute = $this->getShowRoute($reservation);
        $indexRoute = $this->getIndexRoute();  // Add this line
        return view($viewPrefix . 'reservations.edit', compact('reservation', 'products', 'updateRoute', 'showRoute', 'indexRoute'));  // Add 'indexRoute' to compact
    }

    public function update(Request $request, Reservation $reservation)
    {
        // Base rules without status
        $rules = [
            'product_id' => ['required', Rule::exists('products', 'id')],  // CHANGED: DB exists check (FK safe)
            'quantity' => 'required|integer|min:1',
            'reserved_until' => 'required|date|after:now',
        ];

        // Add status rule only for back-end (admin) routes
        if (!$this->isFrontRoute()) {
            $rules['status'] = ['required', Rule::enum(ReservationStatus::class)];
        }

        $validated = $request->validate($rules);

        // For front-end, preserve existing status if not provided
        if ($this->isFrontRoute() && !isset($validated['status'])) {
            $validated['status'] = $reservation->status;
        }

        $reservation->update($validated);

        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Reservation updated!');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Reservation deleted!');
    }

    // UPDATED: Query DB for product name (replaces static lookup)
    public static function getProductName($productId)
    {
        $product = Product::find($productId);
        return $product ? $product->name : 'N/A';
    }
}