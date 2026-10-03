<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class Products extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('stock', '>', 0)->with('category', 'upload')->get();

        if($products->isEmpty()){
            return response()->json([
                'message' => 'no Products have been found, create one in admin dashboard'
            ], 404);
        }

        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Display a listing of the resource. list all products whatever the stock
     */
    public function allProducts()
    {
        $products = Product::with('category', 'upload')->get();

        if($products->isEmpty()){
            return response()->json([
                'message' => 'no Products have been found, create one in admin dashboard'
            ], 404);
        }

        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'  => 'required|integer|exists:categories,id',
            'name'         => 'required|string|min:1|max:255',
            'description'  => 'nullable|string|max:1024',
            'image_id'     => 'nullable|integer',
            'price'        => 'required|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0|max:100',
            'discount_end' => 'nullable|date',
            'stock'        => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => "Product {$product->name} Added Successfully!"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('category')->find($id);

        if(!$product){
            return response()->json([
                'message' => 'Product not found'
            ], 404);    
        }

        return response()->json([
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = $request->validate([
            'category_id'  => 'nullable|integer|exists:categories,id',
            'name'         => 'nullable|string|min:1|max:255',
            'description'  => 'nullable|string|max:1024',
            'image_id'     => 'nullable|integer',
            'price'        => 'nullable|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0|max:100',
            'discount_end' => 'nullable|date',
            'stock'        => 'nullable|integer|min:0',
            'sold'         => 'nullable|integer|min:0' 
        ]);
        
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'message' => "no product with {$id} id"
            ]);
        }
        
        $product->update($validation);

        return response()->json([
            'message' => "update product {$product->name} success"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'message' => "no product with {$id} id"
            ]);
        }

        $product->delete();

        return response()->json([
            'message' => "Category {$product->name} is deleted successfully!"
        ]);
    }
}
