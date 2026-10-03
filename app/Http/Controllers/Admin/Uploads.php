<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Uploads extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $uploads = Upload::all();

        if($uploads->isEmpty()){
            return response()->json([
                'message' => 'no Uploads have been found, upload one in admin dashboard'
            ], 404);    
        }

        return response()->json([
            'uploads' => $uploads
        ]);
    }
   
    /**
     * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'filename' => 'required|string|max:255|min:3|unique:uploads,filename',
            'description' => 'nullable|max:255'
        ]);
        
        $image = $request->file('image');
        $safeName = Str::slug($validation['filename']) . '-' . time() . '.' . $image->getClientOriginalExtension();
        $image_uploaded_path = $image->storeAs('product_images', $safeName, 'local');
        
        $uploadedImageResponse = array(
            "image_name" => basename($image_uploaded_path),
            "image_url" => Storage::disk('public')->url($image_uploaded_path),
            "mime" => $image->getClientMimeType()
        );

        $validation['filename'] = $safeName;
            
        $upload = Upload::create($validation);

        return response()->json([
            'message' => "Uploaded image {$safeName} added successfully!",
            'data' => $uploadedImageResponse
        ], 201);
    }

    /**
     * Get Image by link
     */
    public function getImage(String $id)
    {
        $upload = Upload::find($id);
        if(!$upload){
            return response()->json([
                'message' => 'Image not found'
            ], 404);    
        }

        if (!Storage::disk('local')->exists('product_images/' . $upload->filename)) {
            abort(404);
        }

        return Storage::disk('local')->response('product_images/' . $upload->filename);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $upload = Upload::find($id);
        if(!$upload){
            return response()->json([
                'message' => 'Image not found'
            ], 404);    
        }

        if (!Storage::disk('local')->exists('product_images/' . $upload->filename)) {
            return response()->json([
                'message' => 'File not found'
            ], 404);   
        }

        return response()->json([
            'id'          => $upload->id,
            'product_id'  => $upload->product_id,
            'filename'    => $upload->filename,
            'description' => $upload->description,
            'size'        => Storage::disk('local')->size('product_images/'. $upload->filename),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'filename' => 'required|string|max:255|min:3|unique:uploads,filename',
            'description' => 'nullable|max:255'
        ]);

        $upload = Upload::find($id);
        if(!$upload){
            return response()->json([
                'message' => 'Image not found'
            ], 404);    
        }

        if ($request->hasFile('image')) {
            if ($upload->filename && Storage::disk('local')->exists('product_images/'. $upload->filename)) {
                Storage::disk('local')->delete('product_images/'. $upload->filename);
            }

            $image = $request->file('image');
            $safeName = Str::slug($validation['filename']) . '-' . time() . '.' . $image->getClientOriginalExtension();
            
            $newPath = $image->storeAs('product_images', $safeName, 'local');

            $validation['filename'] = $safeName;
        }

        $upload->update($validation);

        return response()->json([
            'message' => 'update upload success',
            'product_id'  => $upload->product_id,
            'filename'    => $upload->filename,
            'description' => $upload->description,
            'image_url'   => Storage::disk('local')->url('product_images/'. $upload->filename), 
            'size'        => Storage::disk('local')->size('product_images/'. $upload->filename), 
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $upload = Upload::find($id);
        if(!$upload){
            return response()->json([
                'message' => 'Image not found'
            ], 404);    
        }

        if ($upload->path && Storage::disk('local')->exists('product_images/'. $upload->filename)) {
            Storage::disk('local')->delete('product_images/'. $upload->filename);
        }

        $upload->delete();

        return response()->json([
            'message' => "upload {$upload->filename} is deleted successfully!"
        ]);
    }
}
