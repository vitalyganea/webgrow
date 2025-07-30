<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    protected $uploadPath = 'uploads';

    public function __construct()
    {
        // Ensure uploads directory exists
        if (!File::exists(public_path($this->uploadPath))) {
            File::makeDirectory(public_path($this->uploadPath), 0755, true);
        }
    }

    /**
     * Display all files in public folder
     */
    public function index()
    {
        $files = $this->getFiles();
        return view('admin.dashboard.file_manager.index', compact('files'));
    }

    /**
     * Upload new file
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:50240', // 50MB max
        ]);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->getClientOriginalExtension();

            // Create unique filename
            $fileName = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '.' . $extension;

            // Move file to uploads directory
            $uploadedFile->move(public_path($this->uploadPath), $fileName);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully!',
                'filename' => $fileName
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File upload failed!'
        ], 400);
    }

    /**
     * Delete file
     */
    public function delete(Request $request)
    {
        $filename = $request->input('filename');
        $filePath = public_path($this->uploadPath . '/' . $filename);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File not found!'
        ], 404);
    }

    /**
     * Get all files from uploads directory
     */
    private function getFiles()
    {
        $uploadDir = public_path($this->uploadPath);
        $files = [];

        if (File::exists($uploadDir)) {
            $fileList = File::files($uploadDir);

            foreach ($fileList as $file) {
                $filename = $file->getFilename();
                $filePath = $this->uploadPath . '/' . $filename;

                $files[] = [
                    'name' => $filename,
                    'path' => $filePath,
                    'url' => asset($filePath),
                    'size' => $this->formatBytes($file->getSize()),
                    'type' => $file->getExtension(),
                    'mime_type' => $this->getMimeType($file->getPathname()),
                    'modified' => date('M d, Y H:i', $file->getMTime()),
                    'is_image' => $this->isImage($file->getExtension())
                ];
            }

            // Sort by modification time (newest first)
            usort($files, function($a, $b) {
                return filemtime(public_path($b['path'])) - filemtime(public_path($a['path']));
            });
        }

        return $files;
    }

    /**
     * Format file size
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Get MIME type
     */
    private function getMimeType($filePath)
    {
        return mime_content_type($filePath) ?: 'application/octet-stream';
    }

    /**
     * Check if file is an image
     */
    private function isImage($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp'];
        return in_array(strtolower($extension), $imageExtensions);
    }
}
