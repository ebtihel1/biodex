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
    public function index()
    {
        $categories = WasteCategory::all();
        return view('wastecategory.list', compact('categories'));
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
                'min:5',
                'max:10', 
                'regex:/^[a-zA-Z\s\-]+$/',
                'unique:waste_categories,name'
            ],
            'description' => [
                'required', 
                'min:5',
                'max:10', 
                'regex:/^[a-zA-Z\s\-\.,!?()]+$/'
            ],
            'recycling_instructions' => [
                'nullable', 
                'min:5',
                'max:100',
                'regex:/^[a-zA-Z\s\-\.,!?()]*$/' 
            ],
        ], [
            'name.required' => 'Category name is required.',
            'name.min' => 'Name must be at least 5 characters.',
            'name.max' => 'Name must not exceed 10 characters.',
            'name.regex' => 'Name can only contain letters, spaces and hyphens. Numbers are not allowed.',
            'name.unique' => 'This category already exists.',
            
            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 5 characters.',
            'description.max' => 'Description must not exceed 10 characters.',
            'description.regex' => 'Description can only contain letters, spaces and the following special characters: - . , ! ? ( ). Numbers are not allowed.',
            
            'recycling_instructions.min' => 'Instructions must be at least 5 characters.',
            'recycling_instructions.max' => 'Instructions must not exceed 100 characters.',
            'recycling_instructions.regex' => 'Instructions can only contain letters, spaces and the following special characters: - . , ! ? ( ). Numbers are not allowed.',
        ]);

        // Additional validation to prevent empty strings and numbers
        $this->validateNoEmptyAndNoNumbers($request);

        WasteCategory::create($validated);

        return redirect()->route('waste_categories.index')->with('success', 'Category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = WasteCategory::findOrFail($id);
        return view('waste_categories.show', compact('category'));
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
                'min:5',
                'max:10', 
                'regex:/^[a-zA-Z\s\-]+$/',
                'unique:waste_categories,name,' . $id
            ],
            'description' => [
                'required', 
                'min:5',
                'max:10', 
                'regex:/^[a-zA-Z\s\-\.,!?()]+$/'
            ],
            'recycling_instructions' => [
                'nullable', // CORRECTION : Changer 'required' en 'nullable'
                'min:5',
                'max:100',
                'regex:/^[a-zA-Z\s\-\.,!?()]*$/' // CORRECTION : * au lieu de +
            ],
        ], [
            'name.required' => 'Category name is required.',
            'name.min' => 'Name must be at least 5 characters.',
            'name.max' => 'Name must not exceed 10 characters.',
            'name.regex' => 'Name can only contain letters, spaces and hyphens. Numbers are not allowed.',
            'name.unique' => 'This category already exists.',
            
            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 5 characters.',
            'description.max' => 'Description must not exceed 10 characters.',
            'description.regex' => 'Description can only contain letters, spaces and the following special characters: - . , ! ? ( ). Numbers are not allowed.',
            
            // CORRECTION : Retirer le message pour 'recycling_instructions.required'
            'recycling_instructions.min' => 'Instructions must be at least 5 characters.',
            'recycling_instructions.max' => 'Instructions must not exceed 100 characters.',
            'recycling_instructions.regex' => 'Instructions can only contain letters, spaces and the following special characters: - . , ! ? ( ). Numbers are not allowed.',
        ]);

        // Additional validation to prevent empty strings and numbers
        $this->validateNoEmptyAndNoNumbers($request);

        $category->update($validated);

        return redirect()->route('waste_categories.index')->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = WasteCategory::findOrFail($id);
        $category->delete();
        return redirect()->route('waste_categories.index')->with('success', 'Category deleted successfully');
    }

    /**
     * Additional validation to prevent empty strings and numbers in all fields
     */
    private function validateNoEmptyAndNoNumbers(Request $request)
    {
        $fields = [
            'name' => 'Name',
            'description' => 'Description', 
        ];
        
        foreach ($fields as $field => $fieldName) {
            // Check for empty strings (only spaces)
            if (trim($request->$field) === '') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    $field => "$fieldName cannot be empty or contain only spaces."
                ]);
            }
            
            // Check for any numbers in all fields
            if (preg_match('/\d/', $request->$field)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    $field => "$fieldName cannot contain numbers. Only letters and special characters are allowed."
                ]);
            }

            // Check length between 5 and max characters
            $length = strlen(trim($request->$field));
            
            // Define max length for each field
            $maxLengths = [
                'name' => 10,
                'description' => 10,
            ];
            
            if ($length < 5 || $length > $maxLengths[$field]) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    $field => "$fieldName must be between 5 and {$maxLengths[$field]} characters long."
                ]);
            }
        }

        // Validation spécifique pour recycling_instructions (seulement si non vide)
        if (!empty(trim($request->recycling_instructions))) {
            // Check for numbers in recycling_instructions
            if (preg_match('/\d/', $request->recycling_instructions)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'recycling_instructions' => "Recycling instructions cannot contain numbers. Only letters and special characters are allowed."
                ]);
            }

            // Check length for recycling_instructions
            $length = strlen(trim($request->recycling_instructions));
            if ($length < 5 || $length > 100) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'recycling_instructions' => "Recycling instructions must be between 5 and 100 characters long."
                ]);
            }
        }
    }
}