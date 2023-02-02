<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            ], response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $imagenes = $request->file('files');
        $namesImagenes = [];
        foreach ($imagenes as $imagen) {
            $nombreImagen = time() . ' ' . str_replace(',', ' ', $imagen->getClientOriginalName());
            $imagen->move(public_path('storage/images/'), $nombreImagen);
            array_push($namesImagenes, 'storage/images/' . $nombreImagen);
        }
        return response()->json(['images' => $namesImagenes]);
    }

    public function deleteImage(Request $request)
    {
        $imagen = $request->get('file');
        if (File::exists($imagen)) {
            File::delete($imagen);
        }
        return response(['mensaje' => 'Imagen Eliminada', 'imagen' => $imagen], 200);
    }
}
