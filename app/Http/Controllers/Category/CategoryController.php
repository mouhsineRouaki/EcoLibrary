<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return response()->json([
                'message' => 'Failed: no categories found'
            ], 404);
        }

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $category = Category::create([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
        ]);

        if (! $category) {
            return response()->json([
                'message' => 'Failed: category creation failed'
            ], 400);
        }

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id){
        $category = Category::where('id', $id)->first();

        if (! $category) {
            return response()->json([
                'message' => 'Failed: category not found'
            ], 404);
        }

        return response()->json([
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::where('id', $id)->first();

        if (! $category) {
            return response()->json([
                'message' => 'Failed: category not found'
            ], 404);
        }

        $updated = $category->update([
            'name' =>$request->input('name'),
            'slug' =>$request->input('slug'),
            'description' =>$request->input('description'),
        ]);

        if (! $updated) {
            return response()->json([
                'message' => 'Failed: category update failed'
            ], 400);
        }

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::where('id', $id)->first();

        if (! $category) {
            return response()->json([
                'message' => 'Failed: category not found'
            ], 404);
        }

        if (! $category->delete()) {
            return response()->json([
                'message' => 'Failed: category deletion failed'
            ], 400);
        }

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
