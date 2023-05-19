<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SmallBoxReport extends Controller
{
    public function smallBoxReport($purchases, $filter_data)
    {
      
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $styleBorders = [
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                'color' => ['argb' => 'FF000000'],
                ],
        ],
        'font' => [
            'name' => 'Arial',
            'size' => 12
        ]
        ];

        $styleExclusive = [
        'borders' => [
                'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                'color' => ['argb' => 'FF000000'],
                ],
            ],
        'font' => [
            'name' => 'Arial',
            'size' => 12,
            'bold'  => true,
            ],
        'fill' => [ 
            'fillType' => Fill::FILL_SOLID,
            'startColor' => array('argb' => 'FFD9D9D9')
            ],
        ];

        $styleExclusiveBody = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'font' => [
                'name' => 'Arial',
                'size' => 12,
                'bold'  => true,
            ],
        ];

        $styleRows = [
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $styleTitle = [
            'font' => [
                'bold'  => true,
                'name' => 'Arial',
                'size' => 12,    
            ]
        ];

        $styleBeneficiary = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'font' => [
                'name' => 'Arial',
                'size' => 12
            ]
        ];
    
        //Ajuste de espacio de las columnas
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
     
        //Asignacion de estilos a las celdas
        $sheet->getStyle('A1:G1')->applyFromArray($styleBorders);
        $sheet->getStyle('A2:G2')->applyFromArray($styleBorders);

        $sheet->setCellValue('A1', 'REPORTE EMPRESA : '. strtoupper($filter_data[0]->company));
        $sheet->setCellValue('A2', 'PERIODO : '. $filter_data[0]->start . ' AL ' . $filter_data[0]->end);;

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . 'REPORTE PERSONAL ' . ' '  . '.xls');
        header('Cache-Control: max-age=0');
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
    }
}
