<?php

namespace App\Http\Controllers;

use App\Models\ProductRoute;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductRouteController extends Controller
{
    public function productsToSchedule()
    {
        $sales = Sale::with('currentStatus')->whereIn('status_id', [2, 13])->paginate(10);
        return response()->json(['pedidos' => $sales], 200);
    }
}
