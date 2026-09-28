<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class Categories extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categorie::all();

        if($categories->isEmpty()){
            return response()->json([
                'message' => 'no categories have been found, create one in admin dashboard'
            ], 404);    
        }

        return response()->json([
            'categories' => $categories
        ]);
    }
   
    /**
     * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'category' => 'required|max:255|min:1|unique:categories,category'
        ]);

        $category = Categorie::create([
            'category' => $validation['category'],
        ]);

        return response()->json([
            'message' => "category {$validation['category']} Added Successfully!"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categories = Categorie::find($id);

        if(!$categories){
            return response()->json([
                'message' => 'Category not found'
            ], 404);    
        }

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = $request->validate([
            'category' => 'required|max:255|min:1|unique:categories,category'
        ]);
        
        $category = Categorie::find($id);
        if(!$category){
            return response()->json([
                'message' => "no category with {$id} id"
            ]);
        }
        
        $category->update([
            'category' => $validation['category']
        ]);

        return response()->json([
            'message' => 'update category success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Categorie::find($id);
        if(!$category){
            return response()->json([
                'message' => "no category with {$id} id"
            ]);
        }

        $category->delete();
        return response()->json([
            'message' => "Category {$category->category} is deleted successfully!"
        ]);
    }
}
