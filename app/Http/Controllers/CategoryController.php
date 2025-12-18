<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::select('name', 'slug')->get();
if ($categories->isEmpty()) {
            return response()->json(['message' => 'No categories found'], 404);
        }
        return response()->json($categories);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return response()->json($category, 201);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
           $validated = $request->validate([
            'slug' => 'required|string|max:255',
        ]);
        $category = Category::where('slug', Str::slug($validated['slug']))->firstOrFail();
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);

    }
}
