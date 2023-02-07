<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class UploadImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'files' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                "msg" => 'No se registro correctamente la informacion',
                "errorValidacion" => $validation->getMessageBag()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $imagenes = $request->file('files');
        $namesImagenes = [];
        foreach ($imagenes as $imagen) {
            $n = str_replace(' ', '%20', $imagen->getClientOriginalName());
            $nombreImagen = time() . ' ' . str_replace(',', ' ', $n);
            $imagen->move(public_path('storage/images/'), $nombreImagen);
            array_push($namesImagenes, 'storage/images/' . $nombreImagen);
        }
        return response()->json(['images' => $namesImagenes]);
    }

    public function deleteImage(Request $request)
    {
        $imagen = $request->image;
        if (File::exists($imagen)) {
            File::delete($imagen);
            return response(['mensaje' => 'Imagen Eliminada', 'imagen' => $imagen], 200);
        }
        return response(['mensaje' => 'No se pudo eliminar la imagen', 'imagen' => $imagen], 200);
    }
}
