<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\Company;
use App\Models\Spent;
use Illuminate\Database\Seeder;

class SmallBox extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
            "DESARROLLO",
            "RH",
            "LOGISTICA",
            "COMPRAS",
            "CALIDAD",
            "LEGAL",
            "GABRADO LASER",
            "NOMINA BANCO",
            "NOMINA CASH"
        ];

        foreach ($centers as $center) {
            Center::create([
                'name' => $center,
                'description' => null
            ]);
        }

        
        $spents = array(  
            (object)[
                'concept' => "GASTO DE CAJA CHICA",
                'center_id' => 1 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "EMPLEADOS EVENTUALES",
                'center_id' => 1 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "COSTO" ,
            ],
            (object)[
                'concept' => "PASAJES MENSAJERO/CHOFER",
                'center_id' => 1 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "COSTO" ,
            ],
            (object)[
                'concept' => "IMPORTACIONES PAQUETERUA A LA OFIC",
                'center_id' => 1 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "COSTO" ,
            ],

            (object)[
                'concept' => "RENTA",
                'center_id' => 2 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "NOMINA Y SEGURO SOCIAL",
                'center_id' => 2 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "CANCUN",
                'center_id' => 2 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "SEGUROS Y SEGURIDAD",
                'center_id' => 2 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],

            (object)[
                'concept' => "IMPUESTOS (ISR Y 3.5% s/NOMINA)",
                'center_id' => 3 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "CONTADORES",
                'center_id' => 3 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "FISCALIA MAX",
                'center_id' => 3 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],

            (object)[
                'concept' => "COMISION BANCARIA",
                'center_id' => 4 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "FINANCIAMIENTO - INTERES",
                'center_id' => 4 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "TC AMEX ANUALIDAD",
                'center_id' => 4 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "COMISION BANCARIA",
                'center_id' => 4 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],

            (object)[
                'concept' => "LUZ Y AGUA",
                'center_id' => 5 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "TELEFONO + INTERNET",
                'center_id' => 5 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "ENLACE TPE",
                'center_id' => 5 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "AT&T",
                'center_id' => 5 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "ALARMA ADT",
                'center_id' => 5 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],

            (object)[
                'concept' => "MUESTRAS NACIONALES",
                'center_id' => 6 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "COSTO" ,
            ],
            (object)[
                'concept' => "MUESTRAS CHINA o USA",
                'center_id' => 6 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "COSTO" ,
            ],
            (object)[
                'concept' => "MUESTRAS ONLINE AMAZON",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "COSTO" ,
            ],
            (object)[
                'concept' => "CATALOGOS PROMO OPCION",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "VIATICOS NACIONALES",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "VIATICOS USA - CHINA - MEX",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "EXPO CHINA o LAS VEGAS",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "EXPOS (PUBLICITAS, BIMBO, CANCUN, COCA COLA, ABASTUR, JUMEX)",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "AMPPRO",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "TARJETA DE PRESENTACION",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "REGALO A CLIENTES",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "COMIDA CON CLIENTES, PROVEEDORES O JEFES DE AREA",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "PORTALES CLIENTES - GPO SALINAS, PILGRIMS, BBVA, POSADAS, ADM, LALA, LOREAL, COCA, NUBE PYME, PEPSI, PALACIO, FULLER, TAJIN",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "ZOOM, FB, AMAZON, TRELLO , LINKEDIN",
                'center_id' => 6 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "BH CR Y CENTROAMERICA",
                'center_id' => 6 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "AUDITORIA CLIENTES ILUSIÓN, LALA, GEEP, ALSEA, BIMBO, AVON, COCA, LOREAL, GLAXO",
                'center_id' => 6 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],

            (object)[
                'concept' => "E-COMMERCE o MARKETING",
                'center_id' => 7 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "MEDIOS DIGITALES",
                'center_id' => 7 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],

            (object)[
                'concept' => "VIDEO INTRANET, CAMARAS, AUDIO, ILUMINACIÓN, CABLES",
                'center_id' => 8 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],

            (object)[
                'concept' => "ETIQUETAS",
                'center_id' => 9 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "COSTO" ,
            ],
            (object)[
                'concept' => "CINTAS - PLAYO - POLIBURBUJA",
                'center_id' => 9 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "COSTO" ,
            ],
            (object)[
                'concept' => "CAJAS DE CARTON",
                'center_id' => 9 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "COSTO" ,
            ],
            (object)[
                'concept' => "TARIMAS, PATIN, BASCULAS, FLEJE",
                'center_id' => 9 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "BOLSAS",
                'center_id' => 9 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "COSTO" ,
            ],

            (object)[
                'concept' => "PAPELERIA - ART. OFICINA",
                'center_id' => 10 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "DESPENSA - ART. LIMPIEZA",
                'center_id' => 10 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "FUMIGACIÓN / SANITIZACIÓN",
                'center_id' => 10 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "PROTECCIÓN CIVIL. EXTINTORES",
                'center_id' => 10 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],

            (object)[
                'concept' => "SISTEMA ODOO, BI",
                'center_id' => 11 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "PAGINA DE INTERNET, HOSTING",
                'center_id' => 11 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "DOMINIO (PAGINA WEB - CORREO)",
                'center_id' => 11 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "CARTUCHOS / TONER IMPRESORAS",
                'center_id' => 11 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "EQUIPO DE COMPUTO",
                'center_id' => 11 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "LICENCIAS EQUIPO DE COMPUTO",
                'center_id' => 11 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "SAE LICENCIAS",
                'center_id' => 11 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "FOLIOS P/ FACTURA ELECTR.",
                'center_id' => 11 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],

            (object)[
                'concept' => "HERRAMIENTAS DESARROLLO WEB",
                'center_id' => 12 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],

            (object)[
                'concept' => "FIESTA FIN DE AÑO (10° ANIVERSARIO PL, RECONOCIMIENTOS, DESPENSAS) ",
                'center_id' => 13 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "PSICOMETRICOS",
                'center_id' => 13 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "RECLUTAMIENTO",
                'center_id' => 13 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "CAPACITACIÓN NELSON, KELLOGS UNIVERSITY, IPADE, ARIE y LEY, ETC.",
                'center_id' => 13 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "INVERSION" ,
            ],
            (object)[
                'concept' => "UNIFORMES",
                'center_id' => 13 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "CUMPLEAÑOS",
                'center_id' => 13 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "SALÓN RECREATIVO (PB)",
                'center_id' => 13 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "NOM 035 - EXAMEN MEDICO",
                'center_id' => 13 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],

            (object)[
                'concept' => "TAG",
                'center_id' => 14 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "UBER",
                'center_id' => 14 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "IVOY",
                'center_id' => 14 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "GASOLINA",
                'center_id' => 14 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "COMPRA CAMIONETA 2021",
                'center_id' => 14 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "TRAMITES VEHICULARES (VERIFICACION, PERMISO DE CARGA, ETC.)",
                'center_id' => 14 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "RENTA CAMIONETA",
                'center_id' => 14 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "MANTENIMIENTO CAMIONETAS",
                'center_id' => 14 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "PAQUETERIA (PAQUETE EXPRESS, DHL, FEDEX, ETC.)",
                'center_id' => 14 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "COSTO" ,
            ],
            (object)[
                'concept' => "FLETE",
                'center_id' => 14 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "COSTO" ,
            ],
            (object)[
                'concept' => "GPS CAMIONETA",
                'center_id' => 14 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "COSTO" ,
            ],
            (object)[
                'concept' => "GPS CAMIONETAS",
                'center_id' => 14 ,
                'outgo_type' => "VARIABLE" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "APOYO BODEGA O PERMISOS",
                'center_id' => 15 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "RETRABAJOS NO CARGADOS AL PEDIDO O AL PROVEEDOR",
                'center_id' => 16 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "GASTOS LEGALES - Abogados - Notaría -Registro de Marca, Camara Comercio",
                'center_id' => 17 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],
            (object)[
                'concept' => "DEPTO DE GRABADO LASER MANTENIMIENTO",
                'center_id' => 18 ,
                'outgo_type' => "FIJO" ,
                'expense_type' => "GASTO" ,
            ],

        );

        foreach ($spents as $spent) {
            Spent::create([
                'concept' => $spent->concept,
                'center_id' => $spent->center_id,
                'outgo_type' => $spent->outgo_type,
                'expense_type' => $spent->expense_type,
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

        
    }
}
