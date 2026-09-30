<?php

namespace App\Http\Controllers;

use App\Enums\DonationStatus;
use App\Models\Donation;
use App\Models\User;
use App\Models\WasteCategory;
use App\Services\SentimentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DonationController extends Controller
{
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

    public function index()
    {
        $query = Donation::with(['user', 'waste.category'])  // Eager load waste and its category for display/filtering
            ->when(request('date_from'), function ($query) {
                return $query->where('created_at', '>=', request('date_from'));
            })
            ->when(request('date_to'), function ($query) {
                return $query->where('created_at', '<=', request('date_to'));
            })
            ->when(request('item_search'), function ($query, $search) {
                return $query->where('item_name', 'like', '%' . $search . '%');
            })
            ->when(request('user_search'), function ($query, $search) {
                return $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when(request('waste_category_id'), function ($query, $waste_category_id) {  // Filter by category ID via waste
                return $query->whereHas('waste.category', function($q) use ($waste_category_id) {
                    $q->where('id', $waste_category_id);
                });
            })
            ->when(request('condition'), function ($query, $condition) {
                return $query->where('condition', $condition);
            })
            ->when(request('status'), function ($query, $status) {
                return $query->where('status', $status);
            });

        $donations = $query->paginate(4);  // Paginate with 4 items per page

        // AI Sentiment Analysis for batch display
        $service = new SentimentService();
        $sentiments = $service->getSentimentsForDonations($donations->items());  // Only current page

        // Load from DB instead of static
        $wasteCategories = WasteCategory::pluck('name', 'id')->toArray();  // For filters/display

        $viewPrefix = $this->getViewPrefix();
        $createRoute = $this->getCreateRoute();
        return view($viewPrefix . 'donations.index', compact('donations', 'createRoute', 'wasteCategories', 'sentiments'));
    }

    public function create()
    {
        // Load wastes/categories from DB for dropdown
        $wasteCategories = WasteCategory::all(['id', 'name']);  // For selection
        $users = User::all();
        $viewPrefix = $this->getViewPrefix();
        $storeRoute = $this->getStoreRoute();
        $indexRoute = $this->getIndexRoute();
        return view($viewPrefix . 'donations.create', compact('wasteCategories', 'users', 'storeRoute', 'indexRoute'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'waste_category_id' => 'required|exists:waste_categories,id',  // Changed to category for simplicity; adjust if needed
            'item_name' => 'required|string|max:255',
            'condition' => 'required|string|in:new,used,damaged',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'pickup_required' => 'nullable|boolean',
            'pickup_address' => 'required_if:pickup_required,true|string|nullable|max:255',
        ]);

        // Create a new Waste entry linked to the category (since donation needs waste_id)
        $waste = \App\Models\Waste::create([
            'type' => $validated['item_name'],  // Or derive from category
            'weight' => 0,  // Default; update via form if needed
            'status' => 'reusable',
            'user_id' => $validated['user_id'],
            'waste_category_id' => $validated['waste_category_id'],
            'collection_point_id' => null,  // Optional
            'image_path' => null,
            'description' => $validated['description'],
        ]);

        // Map to waste_id for donation
        $validated['waste_id'] = $waste->id;
        unset($validated['waste_category_id']);  // Remove temp field

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('donations', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $donation = Donation::create($validated + ['status' => DonationStatus::Available]);

        // AI Sentiment Analysis
        $service = new SentimentService();
        $sentiment = $service->analyzeSentiment($validated['description'] ?? '');

        // Optional: Log or update model (e.g., add 'sentiment' column via migration)
        // $donation->update(['sentiment' => $sentiment]);

        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Donation created successfully! Sentiment: ' . ucfirst($sentiment));
    }

    public function analyzeSentiment(Request $request)
    {
        $request->validate(['description' => 'required|string']);
        $service = new SentimentService();
        $sentiment = $service->analyzeSentiment($request->description);
        return response()->json(['sentiment' => $sentiment]);
    }

    public function show(Donation $donation)
    {
        $donation->load(['user', 'waste.category']);  // Load related waste and category
        $viewPrefix = $this->getViewPrefix();
        $indexRoute = $this->getIndexRoute();
        $editRoute = $this->getEditRoute($donation);
        $destroyRoute = $this->getDestroyRoute($donation);
        return view($viewPrefix . 'donations.show', compact('donation', 'indexRoute', 'editRoute', 'destroyRoute'));
    }

    public function edit(Donation $donation)
    {
        $donation->load(['user', 'waste.category']);
        // Load wastes/categories from DB for dropdown
        $wasteCategories = WasteCategory::all(['id', 'name']);
        $users = User::all();
        $viewPrefix = $this->getViewPrefix();
        $updateRoute = $this->getUpdateRoute($donation);
        $showRoute = $this->getShowRoute($donation);
        $indexRoute = $this->getIndexRoute();
        return view($viewPrefix . 'donations.edit', compact('donation', 'wasteCategories', 'users', 'updateRoute', 'showRoute', 'indexRoute'));
    }

    public function update(Request $request, Donation $donation)
    {
        // Base rules without status
        $rules = [
            'user_id' => 'required|exists:users,id',
            'waste_category_id' => 'required|exists:waste_categories,id',  // Temp for update; will map to waste
            'item_name' => 'required|string|max:255',
            'condition' => 'required|string|in:new,used,damaged',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'pickup_required' => 'nullable|boolean',
            'pickup_address' => 'required_if:pickup_required,true|string|nullable|max:255',
        ];

        // Add status rule only for back-end (admin) routes
        if (!$this->isFrontRoute()) {
            $rules['status'] = ['required', Rule::enum(DonationStatus::class)];
        }

        $validated = $request->validate($rules);

        // For front-end, preserve existing status if not provided
        if ($this->isFrontRoute() && !isset($validated['status'])) {
            $validated['status'] = $donation->status;
        }

        // Update or create waste if category changed
        if (isset($validated['waste_category_id']) && $validated['waste_category_id'] != $donation->waste->waste_category_id) {
            // Update existing waste or create new
            $donation->waste->update(['waste_category_id' => $validated['waste_category_id']]);
        }
        unset($validated['waste_category_id']);  // Clean up

        if ($request->hasFile('images')) {
            // Optionally delete old images if needed
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

        // AI Sentiment Analysis on update
        $service = new SentimentService();
        $sentiment = $service->analyzeSentiment($validated['description'] ?? '');

        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Donation updated! Sentiment: ' . ucfirst($sentiment));
    }

    public function destroy(Donation $donation)
    {
        // Optionally delete images and related waste if needed
        if ($donation->images && is_array($donation->images)) {
            foreach ($donation->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }
        // Optionally delete associated waste
        // $donation->waste->delete();  // Uncomment if desired
        $donation->delete();
        $indexRoute = $this->getIndexRoute();
        return redirect()->route($indexRoute)->with('success', 'Donation deleted!');
    }

    public static function getWasteTypeName($wasteId)
    {
        // Updated to use DB
        return WasteCategory::find($wasteId)?->name ?? 'N/A';
    }
}
