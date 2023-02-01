<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        $imagenes = $request->allFiles('files');
        $namesImagenes = [];
        foreach ($imagenes as $imagen) {
            $nombreImagen = time() . ' ' . str_replace(',', ' ', $imagen->getClientOriginalName());
            $imagen->move(public_path('storage/images/'), $nombreImagen);
            array_push($namesImagenes, $nombreImagen);
        }
        return response()->json(['links' => $namesImagenes]);
    }

    public function deleteImage(Request $request)
    {
        # code...
    }
}
