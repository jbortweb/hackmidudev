<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'type' => 'required|in:projects,users',
        ]);

        $type = $request->type;
        $file = $request->file('image');
        
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        $path = $file->storeAs(
            'img/' . $type,
            $filename,
            'public'
        );

        return response()->json([
            'url' => '/storage/' . $path,
            'filename' => $filename,
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = str_replace('/storage/', '', $request->path);
        
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return response()->json(['message' => 'Imagen eliminada']);
        }

        return response()->json(['message' => 'Imagen no encontrada'], 404);
    }
}
