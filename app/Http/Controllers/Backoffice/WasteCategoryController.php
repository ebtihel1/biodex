<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WasteCategory;

class WasteCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $query = WasteCategory::query();

        if ($search !== '') {
            $query->where(function ($filter) use ($search) {
                $filter->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('recycling_instructions', 'like', "%{$search}%");
            });
        }

        $categories = $query->latest()->paginate(10)->withQueryString();

        $summary = WasteCategory::query()
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN recycling_instructions IS NOT NULL AND recycling_instructions != '' THEN 1 ELSE 0 END) as with_instructions_count")
            ->first();

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