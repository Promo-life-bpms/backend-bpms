<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SmallBoxReport extends Controller
{
    public function smallBoxReport()
    {
        //Personal de alta
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
        $sheet->getStyle('A1:G51')->applyFromArray($styleBorders);
        $sheet->getStyle('A53:G56')->applyFromArray($styleBorders);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(34);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(36);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(4);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(8);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(4);
        $spreadsheet->getActiveSheet()->getRowDimension('52')->setRowHeight(5);
        $sheet->getStyle('A2')->applyFromArray($styleTitle);
        $sheet->getStyle('B4:G4')->applyFromArray($styleRows);
        $sheet->getStyle('B6:G6')->applyFromArray($styleRows);
        $sheet->getStyle('B8:G8')->applyFromArray($styleRows);
        $sheet->getStyle('B10:G10')->applyFromArray($styleRows);
        $sheet->getStyle('B12:G12')->applyFromArray($styleRows);
        $sheet->getStyle('B14:G14')->applyFromArray($styleRows);
        $sheet->getStyle('B16')->applyFromArray($styleRows);
        $sheet->getStyle('F16:G16')->applyFromArray($styleRows);
        $sheet->getStyle('B19:G19')->applyFromArray($styleRows);
        $sheet->getStyle('B21:G21')->applyFromArray($styleRows);
        $sheet->getStyle('B23:G23')->applyFromArray($styleRows);
        $sheet->getStyle('B25:G25')->applyFromArray($styleRows);
        $sheet->getStyle('B27:G27')->applyFromArray($styleRows);
        $sheet->getStyle('B29:G29')->applyFromArray($styleRows);
        $sheet->getStyle('B31:G31')->applyFromArray($styleRows);
        $sheet->getStyle('B33:G33')->applyFromArray($styleRows);
        $sheet->getStyle('B35:G35')->applyFromArray($styleRows);
        $sheet->getStyle('B37:G37')->applyFromArray($styleRows);

        $sheet->getStyle('B39:D39')->applyFromArray($styleBeneficiary);
        $sheet->getStyle('E39')->applyFromArray($styleBeneficiary);
        $sheet->getStyle('F39')->applyFromArray($styleBeneficiary);
        $sheet->getStyle('G39:F39')->applyFromArray($styleBeneficiary);

        $sheet->getStyle('B40:D40')->applyFromArray($styleBeneficiary);
        $sheet->getStyle('E40')->applyFromArray($styleBeneficiary);
        $sheet->getStyle('F40')->applyFromArray($styleBeneficiary);
        $sheet->getStyle('G40:F40')->applyFromArray($styleBeneficiary);

        $sheet->getStyle('B41:D41')->applyFromArray($styleBeneficiary);
        $sheet->getStyle('E41')->applyFromArray($styleBeneficiary);
        $sheet->getStyle('F41')->applyFromArray($styleBeneficiary);
        $sheet->getStyle('G41:F41')->applyFromArray($styleBeneficiary);

        $sheet->getStyle('B43:G43')->applyFromArray($styleRows);
        $sheet->getStyle('B45:G45')->applyFromArray($styleRows);
        $sheet->getStyle('B47:G47')->applyFromArray($styleRows);
        $sheet->getStyle('B48:G48')->applyFromArray($styleRows);
        $sheet->getStyle('B49:G49')->applyFromArray($styleRows);
        $sheet->getStyle('B50:G50')->applyFromArray($styleRows);
        $sheet->getStyle('A53:G53')->applyFromArray($styleExclusive);

        $sheet->getStyle('E54:G54')->applyFromArray($styleExclusiveBody);
        $sheet->getStyle('E55:G55')->applyFromArray($styleExclusiveBody);
        $sheet->getStyle('C54:D55')->applyFromArray($styleExclusiveBody);
        $sheet->getStyle('A56')->applyFromArray($styleExclusiveBody);
        $sheet->getStyle('B54:B55')->applyFromArray($styleExclusiveBody);
        $sheet->getStyle('B56')->applyFromArray($styleExclusiveBody);
        $sheet->getStyle('C56:G56')->applyFromArray($styleExclusiveBody);

        $sheet->setCellValue('A2', 'REPORTE ');
        $sheet->setCellValue('A4', 'EMPRESA:');
        $sheet->setCellValue('C54', 'SD');
        $sheet->setCellValue('C55', 'SBC');
        $sheet->setCellValue('A56', 'OBSERVACIONES');
        $sheet->setCellValue('B56', 'NUM DE EMPLEADO EN NOMINA');
        $sheet->setCellValue('D56', 'SALARIO');
        $sheet->setCellValue('B53','EXCLUSIVO RECURSOS HUMANOS');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . 'REPORTE PERSONAL ' . ' '  . '.xls');
        header('Cache-Control: max-age=0');
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
    }
}
