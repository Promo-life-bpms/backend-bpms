<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            'Pedido Confirmado',
            "En Orden de Compra",
            "Agendado en Ruta de Entrega (Limpio)",
            "En Bodega del Maquilador",
            "Agendado en Ruta de Entrega (Maquilado)",
            "En Almacen",
            "En Inspeccion de Calidad",
            "En Conteo de Productos",
            "Inventario Contabilizado",
            "Agendado en Ruta de Entrega (Cliente)",
            "En proceso de entrega",
            "Entregado a Cliente",
            "Pospuesto o Cancelado",
        ];

        foreach ($statuses as $status) {
            Status::create([
                'status' => $status,
                'slug' => Str::slug($status),
            ]);
        }
    }
}
