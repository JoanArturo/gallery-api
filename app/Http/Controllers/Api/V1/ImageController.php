<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\SaveImageRequest;
use App\Http\Resources\V1\ImageResource;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        $images = Image::latest()->get();

        return ImageResource::collection($images);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveImageRequest $request): JsonResponse
    {
        // Crear directorio 'images'
        if (Storage::directoryMissing('images'))
            Storage::createDirectory('images');

        // Guardar archivo en el directorio 'images'
        $file = $request->file('image')->store('images');

        // Guardar registro en la BD
        $image = new Image($request->validated());
        $image->path = $file;
        $image->save();

        return response()->json([
            'message' => __('Image saved successfully.'),
            'data'    => new ImageResource($image)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): ImageResource
    {
        $image = Image::findOrFail($id);

        return new ImageResource($image);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $image = Image::findOrFail($id);

        // Eliminar imagen del servidor
        if (Storage::fileExists($image->path))
            Storage::delete($image->path);

        // Eliminar registro de la BD
        $image->delete();

        return response()->json([
            'message' => __('Image successfully deleted.')
        ], Response::HTTP_OK);
    }
}
