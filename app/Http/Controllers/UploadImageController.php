<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Clase controladora para subir y eliminar imágenes.
 */
class UploadImageController extends Controller
{
    /**
     * Sube una o varias imágenes al servidor.
     *
     * @param Request $request La solicitud HTTP con las imágenes a subir.
     * @return \Illuminate\Http\JsonResponse La respuesta JSON con los nombres de las imágenes subidas.
     */
    public function uploadImage(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'files' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                "msg" => 'No se registró correctamente la información',
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

    /**
     * Elimina una imagen del servidor.
     *
     * @param Request $request La solicitud HTTP con la imagen a eliminar.
     * @return \Illuminate\Http\Response La respuesta HTTP con el mensaje de eliminación.
     */
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
