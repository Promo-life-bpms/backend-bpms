<?php

namespace App\Http\Controllers;

use App\Models\OrderPurchase;
use App\Models\Role;
use App\Models\TaggerProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TaggerProviderController extends Controller
{
    /**
     * Sincroniza los usuarios con las órdenes de compra.
     *
     * Obtiene las órdenes de compra que contengan "OT" en el código de orden y que no tengan asignado un usuario etiquetador.
     * Recorre las órdenes de compra y busca el proveedor en la tabla "tagger_providers".
     * Si el proveedor existe, crea un nuevo usuario en la tabla "users" o lo busca si ya existe.
     * Asigna el rol de usuario y guarda el ID del usuario en la orden de compra.
     */
    public function syncUserToOrderPurchase()
    {
        // Obtener las ordenes de compra que tengan OT en el code_order
        $orderPurchases = OrderPurchase::where('code_order', 'like', '%OT%')->whereNull('tagger_user_id')->get();
        // Recorrer las ordenes de compra
        foreach ($orderPurchases as $orderPurchase) {
            // Obtener el nombre del proveedor
            $nameProvider = $orderPurchase->provider_name;
            // Buscar el proveedor en la tabla tagger_providers
            $taggerProvider = TaggerProvider::where('name_provider', $nameProvider)->first();
            // Si el proveedor existe
            if ($taggerProvider) {
                // Obtener el nombre del usuario
                $nameUser = $taggerProvider->name_user;
                // Obtener el email del usuario
                $email = $taggerProvider->email;

                //Crear un nuevo usuario en la tabla users o buscarlo si ya existe
                $user = User::firstOrCreate([
                    'email' => $email,
                ], [
                    'name' => $nameUser,
                    'password' => Hash::make('12345678'),
                ]);

                $role = Role::find(2);
                $user->attachRole($role);

                $orderPurchase->tagger_user_id = $user->id;
                $orderPurchase->save();
            }
        }
    }
}
