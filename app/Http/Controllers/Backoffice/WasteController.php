<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Waste; 
use App\Models\CollectionPoint;
use App\Models\WasteCategory;

class WasteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');
        $categoryId = $request->input('category');

        $query = Waste::query()->with(['category', 'user', 'collectionPoint']);

        if ($search !== '') {
            $query->where(function ($filter) use ($search) {
                $filter->where('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('collectionPoint', fn ($point) => $point->where('name', 'like', "%{$search}%"));
            });
        }

        if (in_array($status, ['recyclable', 'reusable'], true)) {
            $query->where('status', $status);
        } else {
            $status = '';
        }

        if (is_numeric($categoryId)) {
            $query->where('waste_category_id', (int) $categoryId);
        } else {
            $categoryId = '';
        }

        $wastes = $query->latest()->paginate(12)->withQueryString();
        $categories = WasteCategory::query()->orderBy('name')->get(['id', 'name']);
        $summary = Waste::query()
            ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(weight), 0) as total_weight')
            ->selectRaw("SUM(CASE WHEN status = 'recyclable' THEN 1 ELSE 0 END) as recyclable_count")
            ->selectRaw("SUM(CASE WHEN status = 'reusable' THEN 1 ELSE 0 END) as reusable_count")
            ->first();

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
            'type.max' => 'Type must not exceed 10 characters.',
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
            'description.max' => 'Description must not exceed 10 characters.',
            'description.regex' => 'Description can only contain letters, numbers, spaces and the following special characters: - . , ! ? ( )',
        ]);

        // Additional validation for image upload
        $this->validateImage($request);
        
        // Additional validation for weight precision
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
        $waste = Waste::findOrFail($id);
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
                'max:10',
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
                'max:10',
                'regex:/^[a-zA-Z0-9\s\-\.,!?()]+$/'
            ],
        ], [
            'type.required' => 'Waste type is required.',
            'type.max' => 'Type must not exceed 10 characters.',
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
            'description.max' => 'Description must not exceed 10 characters.',
            'description.regex' => 'Description can only contain letters, numbers, spaces and the following special characters: - . , ! ? ( )',
        ]);

        // Additional validation for image upload
        $this->validateImage($request);
        
        // Additional validation for weight precision
        $this->validateWeightPrecision($request);

        $waste = Waste::findOrFail($id);
        $data = $validated;

        if ($request->hasFile('image')) {
            // Delete old image if exists
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
        
        // Delete associated image
        if ($waste->image_path) {
            \Storage::disk('public')->delete($waste->image_path);
        }
        
        $waste->delete();   
        return redirect()->route('wastes.index')->with('success', 'Waste deleted successfully');
    }

    /**
     * Additional validation for image upload
     */
    private function validateImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            // Check image dimensions
            list($width, $height) = getimagesize($image->getPathname());
            if ($width > 4000 || $height > 4000) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'image' => 'Image dimensions cannot exceed 4000x4000 pixels.'
                ]);
            }
            
            // Check aspect ratio
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
            
            // Check if weight has more than 2 decimal places
            if (preg_match('/\.\d{3,}/', $weight)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'weight' => 'Weight cannot have more than 2 decimal places.'
                ]);
            }
            
            // Check for negative values
            if ($weight < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'weight' => 'Weight cannot be negative.'
                ]);
            }
        }
    }

    /**
     * Additional validation to prevent empty strings
     */
    private function validateNoEmptyStrings(Request $request)
    {
        $textFields = ['type', 'description'];
        
        foreach ($textFields as $field) {
            if ($request->filled($field) && trim($request->$field) === '') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    $field => "This field cannot be empty or contain only spaces."
                ]);
            }
        }
    }
}