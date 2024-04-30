<?php

namespace Database\Seeders;

use App\Models\Areas;
use App\Models\Company;
use App\Models\Department;
use App\Models\ManagerHasDepartment;
use App\Models\Role;
use App\Models\UserDetails;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSmallBox extends Seeder
{
    public function run()
    {
        $departments = [
            (object)[
                'id' => 1,
                'name_department' => 'ADMINISTRACIÓN',
            ],
            (object)[
                'id' => 2,
                'name_department' => 'OPERACIONES',
            ],
            (object)[
                'id' => 3,
                'name_department' => 'AUDITORÍA Y GESTIÓN DE CALIDAD',
            ],
            (object)[
                'id' => 4,
                'name_department' => 'DIRECCIÓN BH',
            ],
            (object)[
                'id' => 5,
                'name_department' => 'DIRECCIÓN PL',
            ],
            (object)[
                'id' => 6,
                'name_department' => 'IMPORTACIONES',
            ],
            (object)[
                'id' => 7,
                'name_department' => 'RECURSOS HUMANOS',
            ],
            (object)[
                'id' => 8,
                'name_department' => 'TECNOLOGÍA E INNOVACIÓN',
            ],
            (object)[
                'id' => 9,
                'name_department' => 'VENTAS BH',
            ],
            (object)[
                'id' => 10,
                'name_department' => 'VENTAS PL',
            ],
            (object)[
                'id' => 11,
                'name_department' => 'LOGÍSTICA',
            ],
            (object)[
                'id' => 12,
                'name_department' => 'COMPRAS NACIONALES',
            ],
        ];

        foreach ($departments as $department){

            DB::table('departments')->where('id', $department->id)->update([
                'id' => $department->id,
                'name_department' => $department->name_department,
            ]);
        }
    
        $areas = array(
            ///ADMINISTRACIÓN
            (object)[
                'name_area' => 'Manager administración',
                'id_department' => 1, // optional
            ],
            (object)[
                'name_area' => 'Administración',
                'id_department' => 1, // optional
            ],
            /////OPERACIONES
            (object)[
                'name_area' => 'Manager operaciones',
                'id_department' => 2, // optional
            ],
            (object)[
                'name_area' => 'Mesa de control',
                'id_department' => 2, // optional
            ],
            (object)[
                'name_area' => 'Almacén',
                'id_department' => 2, // optional
            ],
            (object)[
                'name_area' => 'Operador láser',
                'id_department' => 2, // optional
            ],
            (object)[
                'name_area' => 'Gestión de incidencias',
                'id_department' => 2, // optional
            ],
            ////AUDITORÍA Y GESTIÓN DE CALIDAD
            (object)[
                'name_area' => 'Manager de auditoría y gestión de calidad',
                'id_department' => 3, // optional
            ],
            (object)[
                'name_area' => 'Auditoria',
                'id_department' => 3, // optional
            ],
            (object)[
                'name_area' => 'Gestión de calidad',
                'id_department' => 3, // optional
            ],
            ////DIRECCIÓN BH
            (object)[
                'name_area' => 'Dirreción',
                'id_department' => 4, // optional
            ],
            (object)[
                'name_area' => 'Gestión de Proyectos',
                'id_department' => 4, // optional
            ],
            //DIRECCION PL
            (object)[
                'name_area' => 'Dirección',
                'id_department' => 5, // optional
            ],
            (object)[
                'name_area' => 'Marketing Promo Life',
                'id_department' => 5, // optional
            ],

            ////IMPORTACIONES
            (object)[
                'name_area' => 'Manager importaciones',
                'id_department' => 6, // optional
            ],
            (object)[
                'name_area' => 'Importacionaes',
                'id_department' => 6, // optional
            ],

            ///RH
            (object)[
                'name_area' => 'Manager Recursos Humanos',
                'id_department' => 7, // optional
            ],
            (object)[
                'name_area' => 'Recursos Humanos',
                'id_department' => 7, // optional
            ],
            ///Tecnología e innovación
            (object)[
                'name_area' => 'Manager Tecnología e Innovación',
                'id_department' => 8, // optional
            ],
            (object)[
                'name_area' => 'Tecnología e Innovación',
                'id_department' => 8, // optional
            ],
            (object)[
                'name_area' => 'Marketing',
                'id_department' => 8, // optional
            ],
            (object)[
                'name_area' => 'Sistemas',
                'id_department' => 8, // optional
            ],
            ////VENTAS BH
            (object)[
                'name_area' => 'Manager Daniel',
                'id_department' => 9, // optional
            ],
            (object)[
                'name_area' => 'Manager Jacobo',
                'id_department' => 9, // optional
            ],
            
            (object)[
                'name_area' => 'Manager Ventas BH Base',
                'id_department' => 9, // optional
            ],
            (object)[
                'name_area' => 'Ventas BH Base',
                'id_department' => 9, // optional
            ],
            (object)[
                'name_area' => 'Ventas Jacobo',
                'id_department' => 9, // optional
            ],
            (object)[
                'name_area' => 'Ventas Promo Zale',
                'id_department' => 9, // optional
            ],
            ///'VENTAS PL'
            (object)[
                'name_area' => 'Manager Ventas PL',
                'id_department' => 10, // optional
            ],
            (object)[
                'name_area' => 'Ventas PL',
                'id_department' => 10, // optional
            ],
            /////////////LOGÍSTICA//////////
            (object)[
                'name_area' => 'Manager Logística',
                'id_department' => 11, // optional
            ],
            (object)[
                'name_area' => 'Logística',
                'id_department' => 11, // optional
            ],
            //COMPRAS NACIONALES////
            (object)[
                'name_area' => 'Compras nacionales',
                'id_department' => 12, // optional
            ],
        );

        foreach ($areas as $area){
            Areas::create([
                'name_area' => $area->name_area,
                'id_department' => $area->id_department,
            ]);
        }
    
        /*$companies = array(
            (object)[
                'name' => 'Promo Zale',
                'description' => 'Promo Zale', // optional
            ],
            (object)[
                'name' => 'Trade Market 57',
                'description' => 'Trade Market 57', // optional
            ],
        );

        foreach ($companies as $company){
            Company::create([
                'name' => $company->name,
                'description' => $company->description,
            ]);
        }*/

        $roles = array(
            (object)[
                'name' => 'administrator',
                'display_name' => 'Administrador', // optional
                'description' => 'Administrator', // optional
            ],
            (object)[
                'name' => 'maquilador',
                'display_name' => 'Maquilador', // optional
                'description' => 'Maquilador', // optional
            ],
            (object)[
                'name' => 'almacen',
                'display_name' => 'Almacen', // optional
                'description' => 'Almacen', // optional
            ],
            (object)[
                'name' => 'chofer',
                'display_name' => 'Chofer', // optional
                'description' => 'Chofer', // optional
            ],
            (object)[
                'name' => 'control_calidad',
                'display_name' => 'Control Calidad', // optional
                'description' => 'Control Calidad', // optional
            ],
            (object)[
                'name' => 'compras',
                'display_name' => 'Compras', // optional
                'description' => 'Compras', // optional
            ],
            (object)[
                'name' => 'ventas',
                'display_name' => 'Ventas', // optional
                'description' => 'Ventas', // optional
            ],
            (object)[
                'name' => 'logistica-y-mesa-de-control',
                'display_name' => 'Logistica-y-mesa-de-control', // optional
                'description' => 'Logistica-y-mesa-de-control', // optional
            ],
            (object)[
                'name' => 'gerente',
                'display_name' => 'Gerente', // optional
                'description' => 'Gerente', // optional
            ],
            (object)[
                'name' => 'asistente_de_gerente',
                'display_name' => 'Asistente De Gerente', // optional
                'description' => 'Asistente De Gerente', // optional
            ],
            (object)[
                'name' => 'jefe_de_logistica',
                'display_name' => 'Jefe de Logistica', // optional
                'description' => 'Jefe de Logistica', // optional
            ],
            (object)[
                'name' => 'gerente-operaciones',
                'display_name' => 'Gerente de Operaciones', // optional
                'description' => 'Gerente de Operaciones', // optional
            ],
            (object)[
                'name' => 'manager',
                'display_name' => 'Manager', // optional
                'description' => 'Manager', // optional
            ],
            (object)[
                'name' => 'caja_chica',
                'display_name' => 'Caja chica', // optional
                'description' => 'Caja chica', // optional
            ],
            (object)[
                'name' => 'Adquisiciones',
                'display_name' => 'Adquisiciones', // optional
                'description' => 'Adquisiciones', // optional
            ],
            (object)[
                'name' => 'equipo_administracion',
                'display_name' => 'Equipo administración', // optional
                'description' => 'Equipo administración', // optional
            ],
            (object)[
                'name' => 'equipo_auditoria_gestion_calidad',
                'display_name' => 'Equipo Auditoría y gestión de calidad', // optional
                'description' => 'Equipo Auditoría y gestión de calidad', // optional
            ],
            (object)[
                'name' => 'quipo_compras_nacionales',
                'display_name' => 'Equipo compras nacionales', // optional
                'description' => 'Equipo compras nacionales', // optional
            ],
            (object)[
                'name' => 'equipo_direccion_bh',
                'display_name' => 'Equipo Dirección BH', // optional
                'description' => 'Equipo Dirección BH', // optional
            ],
            (object)[
                'name' => 'equipo_direccion_pl',
                'display_name' => 'Equipo Dirección PL', // optional
                'description' => 'Equipo Dirección PL', // optional
            ],
            (object)[
                'name' => 'equipo_importaciones',
                'display_name' => 'Equipo Importaciones', // optional
                'description' => 'Equipo Importaciones', // optional
            ],
            (object)[
                'name' => 'equipo_logistica',
                'display_name' => 'Equipo Logística', // optional
                'description' => 'Equipo Logística', // optional
            ],
            (object)[
                'name' => 'equipo_operaciones',
                'display_name' => 'Equipo Operaciones', // optional
                'description' => 'Equipo Operaciones', // optional
            ],
            (object)[
                'name' => 'equipo_rh',
                'display_name' => 'Equipo Recursos Humanos', // optional
                'description' => 'Equipo Recursos Humanos', // optional
            ],
            (object)[
                'name' => 'equipo_tecnología_innovacion',
                'display_name' => 'Equipo Tecnología e Innovación', // optional
                'description' => 'Equipo Tecnología e Innovación', // optional
            ],
            (object)[
                'name' => 'equipo_ventas_bh',
                'display_name' => 'Equipo Ventas BH', // optional
                'description' => 'Equipo Ventas BH', // optional
            ],
            (object)[
                'name' => 'equipo_ventas_pl',
                'display_name' => 'Equipo Ventas PL', // optional
                'description' => 'Equipo Ventas PL', // optional
            ],
            
        );

        foreach ($roles as $rol) {
            Role::create([
                'name' => $rol->name,
                'display_name' => $rol->display_name,
                'description' => $rol->description,
            ]);  
        }

        ///////////////AQUÍ ME QUEDE FIN////////////////////////
        ///33///
        $asigarroles = array(
            ////////////ROLES DE USUARIOS ADMINISTRACIÓN
            (object)[
                'role_id' => 1,
                'user_id' =>  57, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            (object)[
                'role_id' => 15,
                'user_id' => 58, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            (object)[
                'role_id' => 16,
                'user_id' => 59, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            (object)[
                'role_id' => 16,
                'user_id' => 61, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 16,
                'user_id' => 62, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 16,
                'user_id' => 127, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 14,
                'user_id' => '160', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 16,
                'user_id' => 184, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            ///////////////AUDITORÍA Y GESTIÓN DE CALIDAD
            (object)[
                'role_id' => 13,
                'user_id' => 90, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 17,
                'user_id' => 102, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 17,
                'user_id' => 125, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 17,
                'user_id' => 157, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 17,
                'user_id' => 230, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 17,
                'user_id' => 231, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            //////////////COMPRAS NACIONALES
            (object)[
                'role_id' => 18,
                'user_id' => 91, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 18,
                'user_id' => 95, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 18,
                'user_id' => 126, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            (object)[
                'role_id' => 18,
                'user_id' => 136, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 18,
                'user_id' => 137, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 18,
                'user_id' => 228, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 18,
                'user_id' => 229, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 18,
                'user_id' => 239, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 18,
                'user_id' => 247, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 18,
                'user_id' => 256, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            ////////////////////DIRECCIÓN BH
            (object)[
                'role_id' => 1,
                'user_id' => 51, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 19,
                'user_id' => 72, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 19,
                'user_id' => 233, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            /////////////////////DIRECCIÓN PL
            (object)[
                'role_id' => 1,
                'user_id' => 54, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 20,
                'user_id' => 79, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            ////////////////IMPORTACIONES
            (object)[
                'role_id' => 13,
                'user_id' => 63, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 21,
                'user_id' => 64, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 21,
                'user_id' => 121, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            /////////////////////LOGÍSITCA
            (object)[
                'role_id' => 22,
                'user_id' => 96, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 13,
                'user_id' => 99, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 22,
                'user_id' => 105, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 22,
                'user_id' => 106, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 22,
                'user_id' => 107, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 22,
                'user_id' => 113, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 22,
                'user_id' => 115, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 22,
                'user_id' => 167, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            //////////////OPERACIONES
            (object)[
                'role_id' => 13,
                'user_id' => 57, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 13,
                'user_id' => 90, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 92, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 93, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 97, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 98, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 108, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 109, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 110, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 111, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 145, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 148, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 153, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 156, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 224, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 23,
                'user_id' => 242, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            /////////////////RECURSOS HUMANOS
            (object)[
                'role_id' => 13,
                'user_id' => 55, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 24,
                'user_id' => 56, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 24,
                'user_id' => 112, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 24,
                'user_id' => 116, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 24,
                'user_id' => 117, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 24,
                'user_id' => 128, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 24,
                'user_id' => 159, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            ///////////TECNOLOGÍA E INNOVACIÓN
            (object)[
                'role_id' => 13,
                'user_id' => 66, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 138, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            (object)[
                'role_id' => 25,
                'user_id' => 141, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 188, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 244, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 118, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 68, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 120, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 123, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 129, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 130, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 132, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 135, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 139, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 140, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 158, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 241, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 248, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 261, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 262, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 264, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 25,
                'user_id' => 265, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            //////////////VENTAS BH
            (object)[
                'role_id' => 13,
                'user_id' => 52, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 13,
                'user_id' => 53, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 13,
                'user_id' => 73, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 69, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 70, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 71, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 74, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 75, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 76, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 77, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 78, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 86, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 100, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 101, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 103, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 122, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 124, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 134, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 143, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 149, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 164, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 166, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 222, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 232, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 243, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 246, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 249, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 250, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 257, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 258, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 26,
                'user_id' => 259, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            /////////////VENTAS PL
            (object)[
                'role_id' => 27,
                'user_id' => 80, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 81, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 82, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 85, // optional
                'user_type' => 'App\Models\User', // optional
            ],
            (object)[
                'role_id' => 27,
                'user_id' => 88, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 89, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 131, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 150, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 151, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 152, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 223, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 240, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 251, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 252, // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => 27,
                'user_id' => 255, // optional
                'user_type' => 'App\Models\User', // optional
            ],
        );
        foreach ($asigarroles as $asignarrol) {
            UserRole::create([
                'role_id' => $asignarrol->role_id,
                'user_id' => $asignarrol->user_id,
                'user_type' => $asignarrol->user_type,
            ]);  
        }

        /*$managers = array(
            (object)[
                'id' => 1,
                'id_department' => 1,
                'id_user' => 57,  ////
            ],
            (object)[
                'id' => 2,
                'id_department' => 2,
                'id_user' => 90,
            ],
            (object)[
                'id' => 3,
                'id_department' => 3,
                'id_user' => 90,
            ],
            (object)[
                'id' => 4,
                'id_department' => 4,
                'id_user' => 51,
            ],
            (object)[
                'id' => 5,
                'id_department' => 11,
                'id_user' => 54,
            ],
            (object)[
                'id' => 6,
                'id_department' => 5,
                'id_user' => 63,
            ],
            (object)[
                'id' => 7,
                'id_department' => 6,
                'id_user' => 55,
            ],
            (object)[
                'id' => 8,
                'id_department' => 7,
                'id_user' => 66,
            ],
            (object)[
                'id' => 9,
                'id_department' => 8,
                'id_user' => 73,
            ],
            (object)[
                'id' => 10,
                'id_department' => 9,
                'id_user' => 80,
            ],
            (object)[
                'id' => 11,
                'id_department' => 10,
                'id_user' => 90,
            ],
        );

        foreach ($managers as $manager) {
            DB::table('manager_has_departments')->where('id', $manager->id)->update([
                'id_department' => $manager->id_department,
                'id_user' => $manager->id_user,

            ]);
            ManagerHasDepartment::create([
                'id_department' => $manager->id_department,
                'id_user' => $manager->id_user,
            ]);  
        }*/

        ////PROMO ES ID = 1
        ///BH ES ID = 2
        $users = array(
            ///ADMINISTRACIÓN
            (object)[
                'id_user' => 57,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 1,
            ],
            (object)[
                'id_user' => 58,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 2,
            ],
            (object)[
                'id_user' => 59,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 2,
            ],
            (object)[
                'id_user' => 61,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 2,
            ],
            (object)[
                'id_user' => 62,
                'id_department' => 1,
                'id_company' => 1,
                'id_area' => 2,
            ],
            (object)[
                'id_user' => 127,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 2,
            ],
            (object)[
                'id_user' => 160,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 1,
            ],
            (object)[
                'id_user' => 184,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 2,
            ],
            ///// AUDITORÍA Y GESTIÓN DE CALIDAD ES ID = 3
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 90,
                'id_department' => 3,
                'id_company' => 2,
                'id_area' => 8,
            ],
            (object)[
                'id_user' => 102,
                'id_department' => 3,
                'id_company' => 1,
                'id_area' => 8,
            ],
            (object)[
                'id_user' => 125,
                'id_department' => 3,
                'id_company' => 1,
                'id_area' =>10 ,
            ],
            (object)[
                'id_user' => 157,
                'id_department' => 3,
                'id_company' => 2,
                'id_area' => 9,
            ],
            (object)[
                'id_user' => 230,
                'id_department' => 3,
                'id_company' => 1,
                'id_area' => 10,
            ],
            (object)[
                'id_user' => 231,
                'id_department' => 3,
                'id_company' => 1,
                'id_area' => 10,
            ],
            ////////////COMPRAS NACIONALES/////
            (object)[
                'id_user' => 91,
                'id_department' => 12,
                'id_company' => 5,
                'id_area' => 33,
            ],
            (object)[
                'id_user' => 95,
                'id_department' => 12,
                'id_company' => 5,
                'id_area' => 33,
            ],
            (object)[
                'id_user' => 126,
                'id_department' => 12,
                'id_company' => 5,
                'id_area' => 33,
            ],
            (object)[
                'id_user' => 136,
                'id_department' => 12,
                'id_company' => 5,
                'id_area' => 33,
            ],
            (object)[
                'id_user' => 137,
                'id_department' => 12,
                'id_company' => 5,
                'id_area' => 33,
            ],
            (object)[
                'id_user' => 228,
                'id_department' => 12,
                'id_company' => 5,
                'id_area' => 33,
            ],
            (object)[
                'id_user' => 229,
                'id_department' => 12,
                'id_company' => 5,
                'id_area' => 33,
            ],
            (object)[
                'id_user' => 239,
                'id_department' => 12,
                'id_company' => 5,
                'id_area' => 33,
            ],
            (object)[
                'id_user' => 247,
                'id_department' => 12,
                'id_company' => 5,
                'id_area' => 33,
            ],
            (object)[
                'id_user' => 256,
                'id_department' => 12,
                'id_company' => 5,
                'id_area' => 33,
            ],
            //////////DIRECCIÓN BH
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 51,
                'id_department' => 4,
                'id_company' => 2,
                'id_area' => 11,
            ],
            (object)[
                'id_user' => 72,
                'id_department' => 4,
                'id_company' => 2,
                'id_area' => 11,
            ],
            (object)[
                'id_user' => 233,
                'id_department' => 4,
                'id_company' => 2,
                'id_area' => 12,
            ],
            /////DIRECCIÓN PL////
            (object)[
                'id_user' => 54,
                'id_department' => 5,
                'id_company' => 1,
                'id_area' => 13,
            ],
            (object)[
                'id_user' => 79,
                'id_department' => 5,
                'id_company' => 1,
                'id_area' => 13,
            ],
            ///////IMPORTACIONES///////
            (object)[
                'id_user' => 63,
                'id_department' => 6,
                'id_company' => 5,
                'id_area' => 15,
            ],
            (object)[
                'id_user' => 64,
                'id_department' => 6,
                'id_company' => 5,
                'id_area' => 16,
            ],
            (object)[
                'id_user' => 121,
                'id_department' => 6,
                'id_company' => 5,
                'id_area' => 15,
            ],
            //////////LOGISTICA/////
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 96,
                'id_department' => 11,
                'id_company' => 1,
                'id_area' => 32,
            ],
            (object)[
                'id_user' => 99,
                'id_department' => 11,
                'id_company' => 2,
                'id_area' => 31,
            ],
            (object)[
                'id_user' => 105,
                'id_department' => 11,
                'id_company' => 2,
                'id_area' => 32,
            ],
            (object)[
                'id_user' => 106,
                'id_department' => 11,
                'id_company' => 2,
                'id_area' => 32,
            ],
            (object)[
                'id_user' => 107,
                'id_department' => 11,
                'id_company' => 2,
                'id_area' => 32,
            ],
            (object)[
                'id_user' => 113,
                'id_department' => 11,
                'id_company' => 5,
                'id_area' => 32,
            ],
            (object)[
                'id_user' => 115,
                'id_department' => 11,
                'id_company' => 2,
                'id_area' => 32,
            ],
            (object)[
                'id_user' => 167,
                'id_department' => 11,
                'id_company' => 1,
                'id_area' => 32,
            ],
            /////////OPERACIONES//
            (object)[
                'id_user' => 57,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 3,
            ],
            (object)[
                'id_user' => 90,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 3,
            ],
            (object)[
                'id_user' => 92,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 4,
            ],
            (object)[
                'id_user' => 93,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 4,
            ],
            (object)[
                'id_user' => 97,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 7,
            ],
            (object)[
                'id_user' => 98,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 5,
            ],
            (object)[
                'id_user' => 108,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 5,
            ],
            (object)[
                'id_user' => 109,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 5,
            ],
            (object)[
                'id_user' => 110,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 5,
            ],
            (object)[
                'id_user' => 111,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 5,
            ],
            (object)[
                'id_user' => 145,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 5,
            ],
            (object)[
                'id_user' => 148,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 6,
            ],
            (object)[
                'id_user' => 153,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 5,
            ],
            (object)[
                'id_user' => 156,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 5,
            ],
            (object)[
                'id_user' => 224,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 4,
            ],
            (object)[
                'id_user' => 242,
                'id_department' => 2,
                'id_company' => 5,
                'id_area' => 5,
            ],
            //////////RECURSOS HUMANOS = 7
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 55,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 17,
            ],
            (object)[
                'id_user' => 56,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 18,
            ],
            (object)[
                'id_user' => 112,
                'id_department' => 7,
                'id_company' => 1,
                'id_area' => 18,
            ],
            (object)[
                'id_user' => 116,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 18,
            ],
            (object)[
                'id_user' => 117,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 18,
            ],
            (object)[
                'id_user' => 128,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 18,
            ],
            (object)[
                'id_user' => 159,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 18,
            ],
            //////////TECNOLOGÍA E INNOVACIÓN = 8
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 66,
                'id_department' => 8,
                'id_company' => 2,
                'id_area' => 19,
            ],
            (object)[
                'id_user' => 138,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 21,
            ],
            (object)[
                'id_user' => 141,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 21,
            ],
            (object)[
                'id_user' => 188,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 21,
            ],
            (object)[
                'id_user' => 244,
                'id_department' => 8,
                'id_company' => 5,
                'id_area' => 21,
            ],
            (object)[
                'id_user' => 118,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 22,
            ], 
            (object)[
                'id_user' => 68,
                'id_department' => 8,
                'id_company' => 2,
                'id_area' => 20,
            ],
            (object)[
                'id_user' => 120,
                'id_department' => 8,
                'id_company' => 5,
                'id_area' => 20,
            ],
            (object)[
                'id_user' => 123,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 20,
            ],
            (object)[
                'id_user' => 129,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 20,
            ],
            (object)[
                'id_user' => 130,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 20,
            ],
            (object)[
                'id_user' => 132,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 20,
            ],
            (object)[
                'id_user' => 135,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 20,
            ],
            (object)[
                'id_user' => 139,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 20,
            ],
            (object)[
                'id_user' => 140,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 20,
            ],
            (object)[
                'id_user' => 158,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 20,
            ], 
            (object)[
                'id_user' => 241,
                'id_department' => 8,
                'id_company' => 5,
                'id_area' => 20,
            ],  
            (object)[
                'id_user' => 248,
                'id_department' => 8,
                'id_company' => 5,
                'id_area' => 20,
            ],   
            (object)[
                'id_user' => 261,
                'id_department' => 8,
                'id_company' => 5,
                'id_area' => 20,
            ],  
            (object)[
                'id_user' => 262,
                'id_department' => 8,
                'id_company' => 5,
                'id_area' => 20,
            ],   
            (object)[
                'id_user' => 264,
                'id_department' => 8,
                'id_company' => 5,
                'id_area' => 20,
            ],     
            (object)[
                'id_user' => 265,
                'id_department' => 8,
                'id_company' => 5,
                'id_area' => 20,
            ], 
            /////////////ME QUEDE EN TI
            ////
            ////
            /////

            ////////ALMACÉN = 2
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 127,
                'id_department' => 2,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 98,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 145,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 148,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 109,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 110,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 111,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 113,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 156,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 153,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            //////////MESA DE CONTROL = 2
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 92,
                'id_department' => 2,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 93,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            //////////CALIDAD = 2
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 97,
                'id_department' => 2,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 231,
                'id_department' => 2,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 108,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 125,
                'id_department' => 2,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 230,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            //////////COMPRAS = 2
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 136,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 229,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 91,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 95,
                'id_department' => 2,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 126,
                'id_department' => 2,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 228,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 137,
                'id_department' => 2,
                'id_company' => 1,
                'id_area' => 39,
            ],
            //////////IMPORTACIONES = 6
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 64,
                'id_department' => 6,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 63,
                'id_department' => 6,
                'id_company' => 2,
                'id_area' => 39,
            ],
            //////////VENTAS BH = 9
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 103,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 78,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 77,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 100,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 101,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 149,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 164,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 189,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 232,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 75,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 76,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 73,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 71,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 69,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 70,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 122,
                'id_department' => 9,
                'id_company' => 2,
                'id_area' => 39,
            ], 
            //////////VENTAS PMZ = 9
            ///PROMO ES ID = 1
            ///BH ES ID = 2 
            ////PROMO ZALE ES ID = 3
            (object)[
                'id_user' => 124,
                'id_department' => 9,
                'id_company' => 3,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 74,
                'id_department' => 9,
                'id_company' => 3,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 222,
                'id_department' => 9,
                'id_company' => 3,
                'id_area' => 39,
            ],
            //////////VENTAS PL = 10
            ///PROMO ES ID = 1
            ///BH ES ID = 2  
            (object)[
                'id_user' => 86,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 82,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ], 
            (object)[
                'id_user' => 81,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ], 
            (object)[
                'id_user' => 85,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ], 
            (object)[
                'id_user' => 80,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ], 
            (object)[
                'id_user' => 131,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ], 
            (object)[
                'id_user' => 142,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ], 
            (object)[
                'id_user' => 143,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ], 
            (object)[
                'id_user' => 88,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ], 
            (object)[
                'id_user' => 89,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 150,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 152,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ], 
            (object)[
                'id_user' => 151,
                'id_department' => 10,
                'id_company' => 1,
                'id_area' => 39,
            ],

            /////JEFES/////
            (object)[
                'id_user' => 51,
                'id_department' => 12,
                'id_company' => 1,
                'id_area' => 39,
            ],(object)[
                'id_user' => 52,
                'id_department' => 12,
                'id_company' => 1,
                'id_area' => 39,
            ],(object)[
                'id_user' => 53,
                'id_department' => 12,
                'id_company' => 1,
                'id_area' => 39,
            ],(object)[
                'id_user' => 54,
                'id_department' => 12,
                'id_company' => 1,
                'id_area' => 39,
            ],
        );

        foreach ($users as $user) {
            UserDetails::create([
                'id_user' => $user->id_user,
                'id_department' => $user->id_department,
                'id_company' => $user->id_company,
                'id_area' => $user->id_area
            ]);  
        }

    }
}
