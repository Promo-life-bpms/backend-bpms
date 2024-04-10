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
        /*$departments = [
            'ADMINISTRACIÓN', 
            'OPERACIONES',
            'AUDITORÍA Y GESTIÓN DE CALIDAD',
            'DIRECCIÓN BH',
            'DIRECCIÓN PL',
            'IMPORTACIONES', 
            'RECURSOS HUMANOS', 
            'TECNOLOGÍA E INNOVACIÓN', 
            'VENTAS BH', 
            'VENTAS PL',
            'PRODUCCIÓN',
            'SIN DEFINIR',
        ];*/

        /*foreach ($departments as $department){
            Department::create([
                'name_department' => $department,
                'status' => 1,
            ]);
        }*/
    
        /*$areas = array(
            ///ADMINISTRACIÓN
            (object)[
                'name_area' => 'Tesoreria',
                'id_department' => 1, // optional
            ],
            (object)[
                'name_area' => 'Cuentas por cobrar',
                'id_department' => 1, // optional
            ],(object)[
                'name_area' => 'Facturación',
                'id_department' => 1, // optional
            ],(object)[
                'name_area' => 'Gerencia',
                'id_department' => 1, // optional
            ],
            /////OPERACIONES
            (object)[
                'name_area' => 'Logistica',
                'id_department' => 2, // optional
            ],
            (object)[
                'name_area' => 'Mesa de control',
                'id_department' => 2, // optional
            ],
            (object)[
                'name_area' => 'Almacen',
                'id_department' => 2, // optional
            ],
            (object)[
                'name_area' => 'Calidad',
                'id_department' => 2, // optional
            ],
            (object)[
                'name_area' => 'Compras',
                'id_department' => 2, // optional
            ],
            ////AUDITORÍA Y GESTIÓN DE CALIDAD
            (object)[
                'name_area' => 'Gerencia',
                'id_department' => 3, // optional
            ],
            (object)[
                'name_area' => 'Auditoria',
                'id_department' => 3, // optional
            ],
            ////DIRECCIÓN
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
                'name_area' => 'Importaciones',
                'id_department' => 6, // optional
            ],
            (object)[
                'name_area' => 'Gerencia',
                'id_department' => 6, // optional
            ],

            ///RH
            (object)[
                'name_area' => 'Mantenimiento',
                'id_department' => 7, // optional
            ],
            (object)[
                'name_area' => 'RH',
                'id_department' => 7, // optional
            ],
            (object)[
                'name_area' => 'Gerencia',
                'id_department' => 7, // optional
            ],
            (object)[
                'name_area' => 'Recepción',
                'id_department' => 7, // optional
            ],
            (object)[
                'name_area' => 'Limpieza',
                'id_department' => 7, // optional
            ],

            ///Tecnología e innovación
            (object)[
                'name_area' => 'Sistemas',
                'id_department' => 8, // optional
            ],
            (object)[
                'name_area' => 'Desarrollo',
                'id_department' => 8, // optional
            ],
            (object)[
                'name_area' => 'Marketing BH',
                'id_department' => 8, // optional
            ],
            (object)[
                'name_area' => 'Gerencia',
                'id_department' => 8, // optional
            ],
            (object)[
                'name_area' => 'Diseño',
                'id_department' => 8, // optional
            ],

            ////VENTAS BH
            (object)[
                'name_area' => 'Gerencia',
                'id_department' => 9, // optional
            ],
            (object)[
                'name_area' => 'Gerencia Jacobo',
                'id_department' => 9, // optional
            ],
            (object)[
                'name_area' => 'Gerencia Promo Zale',
                'id_department' => 9, // optional
            ],
            (object)[
                'name_area' => 'Ventas Cancún/Monterry',
                'id_department' => 9, // optional
            ],
            (object)[
                'name_area' => 'Ventas Jacobo',
                'id_department' => 9, // optional
            ],
            (object)[
                'name_area' => 'Ventas México',
                'id_department' => 9, // optional
            ],
            (object)[
                'name_area' => 'Ventas Promo Zale',
                'id_department' => 9, // optional
            ],
            (object)[
                'name_area' => 'Ventas USA',
                'id_department' => 9, // optional
            ],

            ///'VENTAS PL'
            (object)[
                'name_area' => 'Gerencia',
                'id_department' => 10, // optional
            ],
            (object)[
                'name_area' => 'Ventas',
                'id_department' => 10, // optional
            ],

            //PRODUCCIÓN
            (object)[
                'name_area' => 'Impresión',
                'id_department' => 11, // optional
            ],
            //PRODUCCIÓN
            (object)[
                'name_area' => 'SIN DEFINIR',
                'id_department' => 12, // optional
            ],
        );*/

        /*foreach ($areas as $area){
            Areas::create([
                'name_area' => $area->name_area,
                'id_department' => $area->id_department,
            ]);
        }*/
    
        /*$companies = array(
            (object)[
                'name' => 'Promo Zale',
                'description' => 'Promo Zale', // optional
            ],
            (object)[
                'name' => 'Trade Market 57',
                'description' => 'Trade Market 57', // optional
            ],
        );*/

        /*foreach ($companies as $company){
            Company::create([
                'name' => $company->name,
                'description' => $company->description,
            ]);
        }*/

        /*$roles = array(
            (object)[
                'name' => 'equipo_administración',
                'display_name' => 'Equipo administración', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_almacén',
                'display_name' => 'Equipo almacén', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_auditoría_gestión_calidad',
                'display_name' => 'Equipo auditoría y gestión de calidad', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_calidad',
                'display_name' => 'Equipo calidad', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_compras',
                'display_name' => 'Equipo compras', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_dirección',
                'display_name' => 'Equipo dirección', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_diseño',
                'display_name' => 'Equipo diseño', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_importaciones',
                'display_name' => 'Equipo importaciones', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_logistica',
                'display_name' => 'Equipo logistica', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_mantenimiento',
                'display_name' => 'Equipo mantenimiento', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_mesa_control',
                'display_name' => 'Equipo mesa de control', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_recursos_humanos',
                'display_name' => 'Equipo recursos humanos', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_sistemas',
                'display_name' => 'Equipo sistemas', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_tecnología_e_innovación',
                'display_name' => 'Equipo tecnología e innovación', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_ventas_BH',
                'display_name' => 'Equipo ventas BH', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_ventas_PL',
                'display_name' => 'Equipo ventas PL', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_ventas_PMZ',
                'display_name' => 'Equipo ventas PMZ', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'equipo_marketing',
                'display_name' => 'Equipo marketing', // optional
                'description' => 'Equipo de trabajo', // optional
            ],
            (object)[
                'name' => 'caja_chica',
                'display_name' => 'Caja chica', // optional
                'description' => 'Caja chica', 
            ],
            (object)[
                'name' => 'sin_definir',
                'display_name' => 'Sin definir', // optional
                'description' => 'El usuario aún no tiene un rol', 
            ],
            (object)[
                'name' => 'equipo_tm_57',
                'display_name' => 'Trade Market 57', // optional
                'description' => 'Trade Market 57', 
            ],
        );*/

        /*foreach ($roles as $rol) {
            Role::create([
                'name' => $rol->name,
                'display_name' => $rol->display_name,
                'description' => $rol->description,
            ]);  
        }*/

        ///33///
        /*$asigarroles = array(
            (object)[
                'role_id' => '33',
                'user_id' => '55', // optional
                'user_type' => 'App\Models\User', // optional
            ],
            (object)[
                'role_id' => '33',
                'user_id' => '56', // optional
                'user_type' => 'App\Models\User', // optional
            ],
            (object)[
                'role_id' => '33',
                'user_id' => '58', // optional
                'user_type' => 'App\Models\User', // optional
            ],
            (object)[
                'role_id' => '33',
                'user_id' => '59', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '60', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '61', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '62', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '65', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '68', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '72', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '112', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '114', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '116', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '117', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '118', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '119', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '120', // optional
                'user_type' => 'App\Models\User', // optional
            ],
            (object)[
                'role_id' => '33',
                'user_id' => '121', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '123', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '125', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '126', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '127', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '128', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '129', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '132', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '138', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '139', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '140', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '141', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '157', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '159', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '170', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '180', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '181', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '184', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '188', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '232', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '234', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '239', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '240', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '241', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '242', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '243', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '244', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '245', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '246', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '247', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '248', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '249', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '250', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '251', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '252', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '253', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '254', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '255', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '256', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '257', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '258', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '259', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '260', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '261', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '262', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '263', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '264', // optional
                'user_type' => 'App\Models\User', // optional
            ],(object)[
                'role_id' => '33',
                'user_id' => '265', // optional
                'user_type' => 'App\Models\User', // optional
            ],
        );*/
        /*foreach ($asigarroles as $asignarrol) {
            UserRole::create([
                'role_id' => $asignarrol->role_id,
                'user_id' => $asignarrol->user_id,
                'user_type' => $asignarrol->user_type,
            ]);  
        }*/
        
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
        );*/

        /*foreach ($managers as $manager) {
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
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 58,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 61,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 62,
                'id_department' => 1,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 184,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 59,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 160,
                'id_department' => 1,
                'id_company' => 2,
                'id_area' => 39,
            ],
            /////////////////////

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
             //////////LOGISTICA = 2
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 105,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 106,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 167,
                'id_department' => 2,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 99,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 96,
                'id_department' => 2,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 107,
                'id_department' => 2,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 115,
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
            ///// AUDITORÍA Y GESTIÓN DE CALIDAD ES ID = 3
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 90,
                'id_department' => 3,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 157,
                'id_department' => 3,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 102,
                'id_department' => 3,
                'id_company' => 1,
                'id_area' => 39,
            ],
            //////////DIRECCIÓN = 4
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 233,
                'id_department' => 4,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 79,
                'id_department' => 4,
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
            //////////RECURSOS HUMANOS = 7
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 55,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 56,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 112,
                'id_department' => 7,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 116,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 117,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 128,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 39,
            ],
            //////////MANTENIMIENTO = 7
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 159,
                'id_department' => 7,
                'id_company' => 2,
                'id_area' => 39,
            ],
            //////////TECNOLOGÍA E INNOVACIÓN = 8
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 139,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 68,
                'id_department' => 8,
                'id_company' => 2,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 140,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 135,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 129,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 130,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 132,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 158,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 66,
                'id_department' => 8,
                'id_company' => 2,
                'id_area' => 39,
            ],
            //////////SISTEMAS = 8
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 118,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 39,
            ],
            //MARKETING = 8
            ///PROMO ES ID = 1
            ///BH ES ID = 2 
            ////PROMO ZALE ES ID = 3
            (object)[
                'id_user' => 188,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 141,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 39,
            ],
            (object)[
                'id_user' => 138,
                'id_department' => 8,
                'id_company' => 1,
                'id_area' => 39,
            ],
            //////////DISEÑO = 8
            ///PROMO ES ID = 1
            ///BH ES ID = 2
            (object)[
                'id_user' => 123,
                'id_department' => 8,
                'id_company' => 1,
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
