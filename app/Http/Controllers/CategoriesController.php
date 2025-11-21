<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Models\Category;
use App\Models\Product;
use App\Traits\DataTableTrait;

class CategoriesController extends Controller
{
    use DataTableTrait;

    private $validationRules = [
        'name' => 'required|string|max:100',
        'description' => 'required|string|max:255',
        'status' => 'required|in:Active,inActive',
        'show_in_menu' => 'required|boolean',
        'parent_id' => 'nullable',
        'metadata' => 'nullable',
    ];

    public function index(Request $request)
    {
        $filters = json_decode(request('filters')) ?? [];
        $query = Category::with('parent');

        $searchColumns = ['name', 'description', 'status'];
        $categories = $this->dataTable($query, $searchColumns, $filters);

        return $this->successResponse($categories, 'List of Categories retrieved successfully', 200);
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) return $this->errorResponse(null, 'Category not found', 404);

        return $this->successResponse($category, 'Category retrieved successfully', 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validatedData = $validator->validated();

        // Create the category
        $category = Category::create($validatedData);

        return response()->json([
            'category' => $category,
            'message' => 'Category created successfully',
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) return $this->errorResponse(null, 'Category not found', 404);

        // Validate the status
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Active,inActive',  // Make sure the status matches
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        }

        // Update the category status
        $category->status = $request->status;
        $category->save();

        return $this->successResponse($category, 'Category status updated successfully');

    }


    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) return $this->errorResponse(null, 'Category not found', 404);

        $validator = Validator::make($request->all(), $this->validationRules);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validatedData = $validator->validated();

        // Update the category
        $category->update($validatedData);

        return $this->successResponse($category, 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) return $this->errorResponse(null, 'Category not found', 404);

        $category->delete();
        return $this->successResponse(null, 'Category deleted successfully', 200);
    }

    /**** Frontend APIs ****/
    public function getCategories()
    {
        // Fetch all categories
        $categories = Category::where('status', 'Active')->get(['id', 'name', 'show_in_menu', 'metadata']);

        // Fetch products for each category
        $categories->each(function ($category) {
            $category->products = Product::whereJsonContains('category_ids', $category->id)
                ->where('is_active', 1)
                ->get(['id', 'name', 'description', 'price', 'metadata']);
        });

        return $this->successResponse($categories, '', 200);
    }
}
