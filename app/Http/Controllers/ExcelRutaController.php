<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelRutaController extends Controller
{
    //
    public function export()
    {
        // Crea un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Obtiene tu conjunto de datos de la base de datos o de donde sea necesario
        $data = [
            ['Nombre', 'Correo'],
            ['Ejemplo 1', 'ejemplo1@email.com'],
            ['Ejemplo 2', 'ejemplo2@email.com'],
            // ... más datos
        ];

        // Selecciona la hoja activa
        $sheet = $spreadsheet->getActiveSheet();

        // Llena la hoja con tus datos
        $sheet->fromArray($data, NULL, 'A1');

        // Crea un objeto de escritura (Writer) y exporta a formato Xlsx
        $writer = new Xlsx($spreadsheet);

        // Guarda el archivo en el sistema de archivos o devuelve una respuesta de descarga
        $filename = 'archivo_excel.xlsx';
        $writer->save($filename);

        // Puedes devolver una respuesta aquí si lo necesitas
        return response()->download($filename)->deleteFileAfterSend();
    }
}
