<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            $n =  $imagen->getClientOriginalName();
            $nombreImagen = time() . ' ' . Str::slug($n) . '.' . $imagen->getClientOriginalExtension();
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
