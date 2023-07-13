<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\Company;
use App\Models\PaymentMethod;
use App\Models\PurchaseRequest;
use App\Models\PurchaseStatus;
use App\Models\Role;
use App\Models\Spent;
use App\Models\UserCenter;
use Illuminate\Database\Seeder;

class SmallBoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Role::create([
            'name' => 'manager',
            'display_name' => 'Manager', // optional
            'description' => 'Jefe directo', // optional
        ]);

        $centers = [
            'ESTRUCTURAL',
            "ADMINISTRACIÓN",
            "FISCAL",
            "TESORERIA",
            "SERVICIOS OFI",
            "VENTAS",
            "MARKETING",
            "COMUNICACION",
            "ALMACEN",
            "SERV GENERALES",
            "SISTEMAS",
            "DESARROLLO",
            "RH",
            "LOGISTICA",
            "COMPRAS",
            "CALIDAD",
            "LEGAL",
            "GABRADO LASER",
            "NOMINA BANCO",
            "NOMINA CASH",
        ];

        foreach ($centers as $center) {
            Center::create([
                'name' => $center,
                'description' => null,
                'status' =>1
            ]);
        }

        
        $spents = array(
            (object)[
                'concept' => "GASTO DE CAJA CHICA",
                'center_id' => 1,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "EMPLEADOS EVENTUALES",
                'center_id' => 1,
                'outgo_type' => "FIJO",
                'expense_type' => "COSTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "PASAJES MENSAJERO/CHOFER",
                'center_id' => 1,
                'outgo_type' => "FIJO",
                'expense_type' => "COSTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "IMPORTACIONES PAQUETERUA A LA OFIC",
                'center_id' => 1,
                'outgo_type' => "FIJO",
                'expense_type' => "COSTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "RENTA",
                'center_id' => 2,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "NOMINA Y SEGURO SOCIAL",
                'center_id' => 2,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "CANCUN",
                'center_id' => 2,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "SEGUROS Y SEGURIDAD",
                'center_id' => 2,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "IMPUESTOS (ISR Y 3.5% s/NOMINA)",
                'center_id' => 3,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "CONTADORES",
                'center_id' => 3,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "FISCALIA MAX",
                'center_id' => 3,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "COMISION BANCARIA",
                'center_id' => 4,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "FINANCIAMIENTO - INTERES",
                'center_id' => 4,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "TC AMEX ANUALIDAD",
                'center_id' => 4,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "LUZ Y AGUA",
                'center_id' => 5,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "TELEFONO + INTERNET",
                'center_id' => 5,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "ENLACE TPE",
                'center_id' => 5,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "AT&T",
                'center_id' => 5,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "ALARMA ADT",
                'center_id' => 5,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "MUESTRAS NACIONALES",
                'center_id' => 6,
                'outgo_type' => "FIJO",
                'expense_type' => "COSTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "MUESTRAS CHINA o USA",
                'center_id' => 6,
                'outgo_type' => "FIJO",
                'expense_type' => "COSTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "MUESTRAS ONLINE AMAZON",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "COSTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "CATALOGOS PROMO OPCION",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "VIATICOS NACIONALES",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "VIATICOS USA - CHINA - MEX",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "EXPO CHINA o LAS VEGAS",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "EXPOS (PUBLICITAS, BIMBO, CANCUN, COCA COLA, ABASTUR, JUMEX)",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "AMPPRO",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "TARJETA DE PRESENTACION",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "REGALO A CLIENTES",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "COMIDA CON CLIENTES, PROVEEDORES O JEFES DE AREA",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "PORTALES CLIENTES - GPO SALINAS, PILGRIMS, BBVA, POSADAS, ADM, LALA, LOREAL, COCA, NUBE PYME, PEPSI, PALACIO, FULLER, TAJIN",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "ZOOM, FB, AMAZON, TRELLO , LINKEDIN",
                'center_id' => 6,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "BH CR Y CENTROAMERICA",
                'center_id' => 6,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "AUDITORIA CLIENTES ILUSIÓN, LALA, GEEP, ALSEA, BIMBO, AVON, COCA, LOREAL, GLAXO",
                'center_id' => 6,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "E-COMMERCE o MARKETING",
                'center_id' => 7,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "MEDIOS DIGITALES",
                'center_id' => 7,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "VIDEO INTRANET, CAMARAS, AUDIO, ILUMINACIÓN, CABLES",
                'center_id' => 8,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "ETIQUETAS",
                'center_id' => 9,
                'outgo_type' => "VARIABLE",
                'expense_type' => "COSTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "CINTAS - PLAYO - POLIBURBUJA",
                'center_id' => 9,
                'outgo_type' => "VARIABLE",
                'expense_type' => "COSTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "CAJAS DE CARTON",
                'center_id' => 9,
                'outgo_type' => "VARIABLE",
                'expense_type' => "COSTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "TARIMAS, PATIN, BASCULAS, FLEJE",
                'center_id' => 9,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "BOLSAS",
                'center_id' => 9,
                'outgo_type' => "VARIABLE",
                'expense_type' => "COSTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "PAPELERIA - ART. OFICINA",
                'center_id' => 10,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "DESPENSA - ART. LIMPIEZA",
                'center_id' => 10,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "FUMIGACIÓN / SANITIZACIÓN",
                'center_id' => 10,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "PROTECCIÓN CIVIL. EXTINTORES",
                'center_id' => 10,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "SISTEMA ODOO, BI",
                'center_id' => 11,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "PAGINA DE INTERNET, HOSTING",
                'center_id' => 11,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "DOMINIO (PAGINA WEB - CORREO)",
                'center_id' => 11,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "CARTUCHOS / TONER IMPRESORAS",
                'center_id' => 11,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "EQUIPO DE COMPUTO",
                'center_id' => 11,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "LICENCIAS EQUIPO DE COMPUTO",
                'center_id' => 11,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "SAE LICENCIAS",
                'center_id' => 11,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "FOLIOS P/ FACTURA ELECTR.",
                'center_id' => 11,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "HERRAMIENTAS DESARROLLO WEB",
                'center_id' => 12,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "FIESTA FIN DE AÑO (10° ANIVERSARIO PL, RECONOCIMIENTOS, DESPENSAS)",
                'center_id' => 13,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "PSICOMETRICOS",
                'center_id' => 13,
                'outgo_type' => "VARIABLE",
                'expense_type' => "GASTO",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "RECLUTAMIENTO",
                'center_id' => 13,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "CAPACITACIÓN NELSON, KELLOGS UNIVERSITY, IPADE, ARIE y LEY, ETC.",
                'center_id' => 13,
                'outgo_type' => "VARIABLE",
                'expense_type' => "INVERSION",
                'product_type' => "SERVICIO",
            ],
            (object)[
                'concept' => "UNIFORMES",
                'center_id' => 13,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "CUMPLEAÑOS",
                'center_id' => 13,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
            (object)[
                'concept' => "SALÓN RECREATIVO (PB)",
                'center_id' => 13,
                'outgo_type' => "FIJO",
                'expense_type' => "GASTO",
                'product_type' => "PRODUCTO",
            ],
        );
        

        foreach ($spents as $spent) {
            Spent::create([
                'concept' => $spent->concept,
                'center_id' => $spent->center_id,
                'outgo_type' => $spent->outgo_type,
                'expense_type' => $spent->expense_type,
                'product_type' => $spent->product_type,
                'status' =>1
            ]);  
        } 

        $companies = [
            'Promo life',
            "BH Trademarket",
        ];

        foreach ($companies as $company) {
            Company::create([
                'name' => $company,
                'description' => $company,
            ]);  
        } 


        PaymentMethod::create([
            'name' => 'EFECTIVO',
            'description' => 'Pagon en efectivo',
        ]);
        
        PaymentMethod::create([
            'name' => 'AME EXPRESS',
            'description' => 'Pago con American Express',
        ]);  

        PaymentMethod::create([
            'name' => 'TRANSFERENCIA',
            'description' => 'Pago con transferencia',
        ]); 

        PaymentMethod::create([
            'name' => 'SIN METODO DE PAGO',
            'description' => 'Sin metodo de pago definido',
        ]);
        

        //Seeders temporales

        UserCenter::create([
            'user_id' => 1,
            'center_id' => 1,
        ]);
        UserCenter::create([
            'user_id' => 1,
            'center_id' => 2,
        ]);
        UserCenter::create([
            'user_id' => 1,
            'center_id' => 3,
        ]);
        UserCenter::create([
            'user_id' => 1,
            'center_id' => 4,
        ]);
        UserCenter::create([
            'user_id' => 2,
            'center_id' => 1,
        ]);
        UserCenter::create([
            'user_id' => 3,
            'center_id' => 1,
        ]);
        UserCenter::create([
            'user_id' => 4,
            'center_id' => 6,
        ]);
        UserCenter::create([
            'user_id' => 4,
            'center_id' => 7,
        ]);

        
        PurchaseStatus::create([
            'name' => 'En proceso',
            'table_name' => 'pedido-en-proceso',
        ]);

        PurchaseStatus::create([
            'name' => 'Compra',
            'table_name' => 'pedido-en-compra',
        ]);

        PurchaseStatus::create([
            'name' => 'Entregado',
            'table_name' => 'pedido-entregado',
        ]);

        PurchaseStatus::create([
            'name' => 'Recibido',
            'table_name' => 'pedido-recibido',
        ]);

    }
}
