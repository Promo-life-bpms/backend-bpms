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
