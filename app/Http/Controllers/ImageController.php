<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function uploadImage(Request $request): JsonResponse
    {
        try {
            if (!$request->hasFile('image')) {
                return response()->json(['error' => 'No image uploaded'], 400);
            }

            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();

            return response()->json([
                'message' => 'Image uploaded successfully',
                'url' => $uploadedFileUrl,
                'isSuccess' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Image upload failed',
                'message' => $e->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }
}

