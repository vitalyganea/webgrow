<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\SeoTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,gif,webp|max:2048', // Max 2MB
        ]);

        // Store the file in the 'public/uploads' directory
        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads', $filename, 'public');

        // Generate the URL for the uploaded image
        $url = Storage::url($path);

        // Return the URL in the format TinyMCE expects
        return response()->json(['location' => $url]);
    }
}
