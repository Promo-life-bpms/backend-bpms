<?php

namespace Database\Seeders;

use App\Models\Spent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpentsSmallBox extends Seeder
{
    public function run()
    {
        $spents = array(
            (object)[
                'id' => 19,
                'concept' => "AGUA DE GARRAFÓN",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'id' => 9,
                'concept' => "ARTÍCULOS DE LIMPIEZA",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'id' => 8,
                'concept' => "ARTÍCULOS DE OFICINA - PAPELERÍA",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'id' => 67,
                'concept' => "AUDITORÍA CLIENTES",
                'center_id' => 6,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 74,
                'concept' => "BÁSCULAS",
                'center_id' => 9,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'id' => 124,
                'concept' => "CÁMARA DE COMERCIO",
                'center_id' => 2,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 14,
                'concept' => "CAPACITACIONES",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 50,
                'concept' => "CLEARCOM",
                'center_id' => 5,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 12,
                'concept' => "COMIDA CON CLIENTES",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'id' => 95,
                'concept' => "COMPRA DE EQUIPO DE TRANSPORTE",
                'center_id' => 14,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'id' => 111,
                'concept' => "CURSOS PROTECCIÓN CIVIL, ALERTA SISMICA, RECARGA EXTINTORES",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 87,
                'concept' => "DESPENSA FIN DE AÑO",
                'center_id' => 13,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'id' => 28,
                'concept' => "DESPENSA GENERAL",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],  
            (object)[
                'id' => 24,
                'concept' => "EGRESOS DIRECCIÓN BH TRADE MARKET",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ], 
            (object)[
                'id' => 113,
                'concept' => "EGRESOS DIRECCIÓN PROMOLIFE",
                'center_id' => 21,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ], 
            (object)[
                'id' => 21,
                'concept' => "EMPLEADOS EVENTUALES",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ], 
            (object)[
                'id' => 22,
                'concept' => "EMPLEADOS EVENTUALES - MAQUILA",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ], 
            (object)[
                'id' => 30,
                'concept' => "EXPOSICIONES NACIONALES",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'id' => 48,
                'concept' => "FINANCIAMIENTO- INTERESES",
                'center_id' => 4,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 46,
                'concept' => "FISCALISTA MAX",
                'center_id' => 3,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 16,
                'concept' => "FLETE",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 102,
                'concept' => "GASTOS LEGALES - ABOGADOS - NOTARÍA -REGISTRO DE MARCA",
                'center_id' => 17,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 6,
                'concept' => "MANTENIMIENTO CAMIONETAS",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 11,
                'concept' => "MANTENIMIENTO OFICINAS",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 55,
                'concept' => "MUESTRAS CHINA O USA - CASA DE BOLSA",
                'center_id' => 6,
                'outgo_type' => "FIJO",
                'expense_type' => "COSTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'id' => 98,
                'concept' => "PAQUETERIA  NACIONAL",
                'center_id' => 14,
                'outgo_type' => "FIJO",
                'expense_type' => "COSTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 39,
                'concept' => "PAQUETERÍA IMPORTACIONES",
                'center_id' => 1,
                'outgo_type' => "FIJO",
                'expense_type' => "COSTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'id' => 27,
                'concept' => "PASAJES MENSAJERO-CHOFER",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 64,
                'concept' => "PORTALES CLIENTES",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            //////////AQUI SE DEBE ELIMIAR EL DE ID 63////////////
            (object)[
                'id' => 110,
                'concept' => "REGALO A CLIENTES",
                'center_id' => 21,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 40,
                'concept' => "RENTA Y MANTENIMIENTO EDIFICIO",
                'center_id' => 2,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 31,
                'concept' => "SERVICIO MÉDICO",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 31,
                'concept' => "SERVICIO MÉDICO",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 49,
                'concept' => "TC AMEX ANUALIDAD",
                'center_id' => 4,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 96,
                'concept' => "TRÁMITES VEHICULARES",
                'center_id' => 14,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 10,
                'concept' => "UBER - DIDI",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 59,
                'concept' => "VIÁTICOS USA",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 65,
                'concept' => "ZOOM, FB y AMAZON, TRELLO o LINKEDIN,GIRA",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 82,
                'concept' => "EQUIPO DE COMPUTO",
                'center_id' => 11,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSIÓN",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'id' => 83,
                'concept' => "LICENCIAS EQUIPO DE COMPUTO",
                'center_id' => 11,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSIÓN",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'id' => 122,
                'concept' => "PROTECCIÓN CIVIL",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
        );

        foreach ($spents as $spent) {
            $spent = DB::table('spents')->where('id', $spent->id)->update([
                'concept' => $spent->concept,
                'center_id' => $spent->center_id,
                'outgo_type' => $spent->outgo_type,
                'expense_type' => $spent->expense_type,
                'product_type' => $spent->product_type,
                'status' => $spent->status,
            ]);
        }
        //////////////AQUI DEBO ACTUALIZAR//////////////////
        $Newspents = array(
            /* (object)[
                'concept' => "BOTELLAS DE AGUA",
                'center_id' => 21,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1,
            ],
            (object)[
                'concept' => "COMPRA Y MANTENIMIENTO DE PATIN",
                'center_id' => 9,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1,
            ],
            (object)[
                'concept' => "EVENTOS DE RECONOCIMIENTO",
                'center_id' => 13,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'concept' => "FLEJE",
                'center_id' => 9,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'concept' => "LUZ",
                'center_id' => 21,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'concept' => "TARIMAS",
                'center_id' => 9,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
                'status' => 1
            ],
            (object)[
                'concept' => "TELEFONO + INTERNET IZZI",
                'center_id' => 5,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'concept' => "TOTAL PLAY",
                'center_id' => 5,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'concept' => "VIÁTIVOS CHINA",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],
            (object)[
                'concept' => "BURO DE CRÉDITO",
                'center_id' => 2,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ], */
            (object)[
                'concept' => "ESTUDIO PRECIOS DE TRANSFERENCIA ",
                'center_id' => 2,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
                'status' => 1
            ],

        );

        foreach ($Newspents as $newspent) {
            Spent::create([
                'concept' => $newspent->concept,
                'center_id' => $newspent->center_id,
                'outgo_type' => $newspent->outgo_type,
                'expense_type' => $newspent->expense_type,
                'product_type' => $newspent->product_type,
                'status' => $newspent->status
            ]);  
        }
    }
}
