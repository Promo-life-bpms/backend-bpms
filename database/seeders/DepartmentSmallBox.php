<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\ManagerHasDepartment;
use App\Models\Role;
use App\Models\UserDetails;
use Illuminate\Database\Seeder;

class DepartmentSmallBox extends Seeder
{
    public function run()
    {
        $departments = [
            'ADMINISTRACIÓN',
            'ALMACÉN',
            'AUDITORÍA Y GESTIÓN DE CALIDAD',
            'CALIDAD',
            'COMPRAS',
            'DIRECCIÓN',
            'DISEÑO',
            'IMPORTACIONES',
            'LOGISTICA',
            'MANTENIMIENTO',
            'MESA DE CONTROL',
            'RECURSOS HUMANOS',
            'SISTEMAS',
            'TECNOLOGÍA E INNOVACIÓN',
            'VENTAS BH',
            'VENTAS PL',
            'VENTAS PMZ',
            'MARKETING',
        ];

        foreach ($departments as $department){
            Department::create([
                'name_department' => $department,
                'status' => 1,
            ]);
        }

        Role::create([
            'name' => 'equipo',
            'display_name' => 'Equipo', // optional
            'description' => 'Equipo de trabajo', // optional
        ]);


        $managers = array(
            (object)[
                'id_department' => 1,
                'id_user' => 57,
            ],
            (object)[
                'id_department' => 2,
                'id_user' => 110,
            ],
            (object)[
                'id_department' => 3,
                'id_user' => 90,
            ],
            (object)[
                'id_department' => 4,
                'id_user' => 97,
            ],
            (object)[
                'id_department' => 6,
                'id_user' => 135,
            ],
            (object)[
                'id_department' => 7,
                'id_user' => 66,
            ],
            (object)[
                'id_department' => 8,
                'id_user' => 64,
            ],
            (object)[
                'id_department' => 8,
                'id_user' => 63,
            ],
            (object)[
                'id_department' => 9,
                'id_user' => 99,
            ],
            (object)[
                'id_department' => 10,
                'id_user' => 55,
            ],
            (object)[
                'id_department' => 11,
                'id_user' => 90,
            ],
            (object)[
                'id_department' => 12,
                'id_user' => 55,
            ],
            (object)[
                'id_department' => 13,
                'id_user' => 66,
            ],
            (object)[
                'id_department' => 14,
                'id_user' => 66,
            ],
            (object)[
                'id_department' => 15,
                'id_user' => 73,
            ],
            (object)[
                'id_department' => 16,
                'id_user' => 80,
            ],
            (object)[
                'id_department' => 17,
                'id_user' => 53,
            ],
        );

        foreach ($managers as $manager) {
            ManagerHasDepartment::create([
                'id_department' => $manager->id_department,
                'id_user' => $manager->id_user,
            ]);  
        }

        ////PROMO ES ID = 1
        ///BH ES ID = 2
        $users = array(
            ///ADMINISTRACIÓN
            (object)[
                'id_user' => 57,
                'id_department' => 1,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 58,
                'id_department' => 1,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 61,
                'id_department' => 1,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 62,
                'id_department' => 1,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 184,
                'id_department' => 1,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 59,
                'id_department' => 1,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 160,
                'id_department' => 1,
                'id_company' => 2,
            ],
            /////////////////////

            ////////ALMACÉN = 2
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 127,
                'id_department' => 2,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 98,
                'id_department' => 2,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 145,
                'id_department' => 2,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 148,
                'id_department' => 2,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 109,
                'id_department' => 2,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 110,
                'id_department' => 2,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 111,
                'id_department' => 2,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 113,
                'id_department' => 2,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 156,
                'id_department' => 2,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 153,
                'id_department' => 2,
                'id_company' => 2,
            ],

            ///// AUDITORÍA Y GESTIÓN DE CALIDAD ES ID = 3
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 90,
                'id_department' => 3,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 157,
                'id_department' => 3,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 102,
                'id_department' => 3,
                'id_company' => 1,
            ],

            //////////CALIDAD = 4
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 97,
                'id_department' => 4,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 231,
                'id_department' => 4,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 108,
                'id_department' => 4,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 125,
                'id_department' => 4,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 230,
                'id_department' => 4,
                'id_company' => 2,
            ],

            //////////COMPRAS = 5
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 136,
                'id_department' => 5,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 229,
                'id_department' => 5,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 91,
                'id_department' => 5,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 95,
                'id_department' => 5,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 126,
                'id_department' => 5,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 228,
                'id_department' => 5,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 137,
                'id_department' => 5,
                'id_company' => 1,
            ],

            //////////DIRECCIÓN = 6
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 233,
                'id_department' => 6,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 79,
                'id_department' => 6,
                'id_company' => 1,
            ],
            //////////DISEÑO = 7
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 123,
                'id_department' => 7,
                'id_company' => 1,
            ],
            //////////IMPORTACIONES = 8
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 64,
                'id_department' => 8,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 63,
                'id_department' => 8,
                'id_company' => 2,
            ],
            //////////LOGISTICA = 9
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 105,
                'id_department' => 9,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 106,
                'id_department' => 9,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 167,
                'id_department' => 9,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 99,
                'id_department' => 9,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 96,
                'id_department' => 9,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 107,
                'id_department' => 9,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 115,
                'id_department' => 9,
                'id_company' => 2,
            ],
            //////////MANTENIMIENTO = 10
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 159,
                'id_department' => 10,
                'id_company' => 2,
            ],
            //////////MESA DE CONTROL = 11
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 92,
                'id_department' => 11,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 93,
                'id_department' => 11,
                'id_company' => 2,
            ],
            //////////RECURSOS HUMANOS = 12
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 55,
                'id_department' => 12,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 56,
                'id_department' => 12,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 112,
                'id_department' => 12,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 116,
                'id_department' => 12,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 117,
                'id_department' => 12,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 128,
                'id_department' => 12,
                'id_company' => 2,
            ],
            //////////SISTEMAS = 13
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 118,
                'id_department' => 13,
                'id_company' => 1,
            ],
            //////////TECNOLOGÍA E INNOVACIÓN = 14
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 139,
                'id_department' => 14,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 68,
                'id_department' => 14,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 140,
                'id_department' => 14,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 135,
                'id_department' => 14,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 129,
                'id_department' => 14,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 130,
                'id_department' => 14,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 132,
                'id_department' => 14,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 158,
                'id_department' => 14,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 66,
                'id_department' => 14,
                'id_company' => 2,
            ],
            //////////VENTAS BH = 15
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 103,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 78,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 77,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 100,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 101,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 149,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 164,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 189,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 232,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 75,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 76,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 73,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 71,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 69,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 70,
                'id_department' => 15,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 122,
                'id_department' => 15,
                'id_company' => 2,
            ], 
            //////////VENTAS PL = 16
            ///PROMO ES ID = 1
            ///BH ES ID = 2  
            (object)[
                'id_user' => 86,
                'id_department' => 16,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 82,
                'id_department' => 16,
                'id_company' => 1,
            ], 
            (object)[
                'id_user' => 81,
                'id_department' => 16,
                'id_company' => 1,
            ], 
            (object)[
                'id_user' => 85,
                'id_department' => 16,
                'id_company' => 1,
            ], 
            (object)[
                'id_user' => 80,
                'id_department' => 16,
                'id_company' => 1,
            ], 
            (object)[
                'id_user' => 131,
                'id_department' => 16,
                'id_company' => 1,
            ], 
            (object)[
                'id_user' => 142,
                'id_department' => 16,
                'id_company' => 1,
            ], 
            (object)[
                'id_user' => 143,
                'id_department' => 16,
                'id_company' => 1,
            ], 
            (object)[
                'id_user' => 88,
                'id_department' => 16,
                'id_company' => 1,
            ], 
            (object)[
                'id_user' => 89,
                'id_department' => 16,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 150,
                'id_department' => 16,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 152,
                'id_department' => 16,
                'id_company' => 1,
            ], 
            (object)[
                'id_user' => 151,
                'id_department' => 16,
                'id_company' => 1,
            ],
            //////////VENTAS PMZ = 17
            ///PROMO ES ID = 1
            ///BH ES ID = 2 
            ////PROMO ZALE ES ID = 3
            (object)[
                'id_user' => 124,
                'id_department' => 17,
                'id_company' => 3,
            ],
            (object)[
                'id_user' => 74,
                'id_department' => 17,
                'id_company' => 3,
            ],
            (object)[
                'id_user' => 222,
                'id_department' => 17,
                'id_company' => 3,
            ],

            ////JEFES
            (object)[
                'id_user' => 51,
                'id_department' => 6,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 52,
                'id_department' => 6,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 53,
                'id_department' => 6,
                'id_company' => 2,
            ],
            (object)[
                'id_user' => 54,
                'id_department' => 6,
                'id_company' => 1,
            ],

            //MARKETING = 18
            ///PROMO ES ID = 1
            ///BH ES ID = 2 
            ////PROMO ZALE ES ID = 3
            (object)[
                'id_user' => 188,
                'id_department' => 18,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 141,
                'id_department' => 18,
                'id_company' => 1,
            ],
            (object)[
                'id_user' => 138,
                'id_department' => 18,
                'id_company' => 1,
            ],
        );

        foreach ($users as $user) {
            UserDetails::create([
                'id_user' => $user->id_user,
                'id_department' => $user->id_department,
                'id_company' => $user->id_company
            ]);  
        }

    }
}
