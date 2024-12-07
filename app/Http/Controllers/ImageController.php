<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public static function update($userId, $type, Request $request)
    {
        $user = User::findOrFail($userId);
        $file = $request->file('image');
        

        if (!$file) {
            return redirect()->back()->with('error', 'No file uploaded');
        }

        $path = $file->store('images/profile', 'public');

        $image = $user->image;

        if ($image) {
            Storage::disk('public')->delete($image->path);
            $image->path = $path;
            $image->save();
        } else {
            $user->image()->create(['path' => $path]);
        }

        return redirect()->back()->with('success', 'Profile picture updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public static function delete($imageId)
    {
        $image = Image::findOrFail($imageId);

        Storage::disk('public')->delete($image->path);

        $image->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    }
}
