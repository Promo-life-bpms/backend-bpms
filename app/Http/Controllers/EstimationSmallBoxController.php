<?php

namespace App\Http\Controllers;

use App\Models\EstimationSmallBox;
use App\Models\PaymentMethodInformation;
use App\Models\RefundOfMoney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ResponseInterface;

class EstimationSmallBoxController extends Controller
{
    public function index()
    {
        ///OBTGENEMOS LA INFORMACION PARA LA TABLA///
        $Information = DB::table('estimation_small_box')
        ->select('estimation_small_box.total', 'estimation_small_box.id_user', 'users.name',
                  DB::raw('DATE_FORMAT(estimation_small_box.created_at, "%d-%m-%Y") as created_date'))
        ->join('users', 'estimation_small_box.id_user', '=', 'users.id')
        ->get()
        ->toArray();

        ///OBTENEMOS EL PRIMER DÍA DEL MES Y EL ÚLTIMO///        
        $primerDiaDelMes = Carbon::now()->startOfMonth();
        $ultimoDiaDelMes = Carbon::now()->endOfMonth();

        // Verificar si la fecha actual está dentro del mes
        if (Carbon::now()->between($primerDiaDelMes, $ultimoDiaDelMes)) {
            // Si estamos en el mes actual, realizar la suma
            //presupuestomensual == MonthlyBudget
            $MonthlyBudget = DB::table('estimation_small_box')
                ->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
                ->sum('total');
        }

        ///CONDICIONES PARA PODER SUMAR EL CAMPO "total"///
        //gastosmentuales == monthlyexpenses
        $MonthlyExpenses = DB::table('purchase_requests')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
                                                        ->where(function ($query) {
                                                            $query->where(function ($subquery) {
                                                                $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                                                            })->orWhere(function ($subquery) {
                                                                $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                                                            })->orWhere(function ($subquery) {
                                                                $subquery->where('purchase_status_id', '=', 3)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                                                            });
                                                        })->sum('total');
        
        ///presupuestodisponible == AvailableBudget

        $devolution = DB::table('purchase_requests')->whereBetween('created_at',[$primerDiaDelMes,$ultimoDiaDelMes])
                                                ->where(function($query){
                                                    $query->where(function ($subquery){
                                                        $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                                                    });
                                                })->sum('total');

        $AvailableBudget =number_format($MonthlyBudget - $MonthlyExpenses, 2, '.', '' );

        $restaDelCajaReturn = DB::table('refund_of_money')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
                                                        ->sum('total_returned');
        
        // Restar total_returned al AvailableBudget si hay valores
        if ($restaDelCajaReturn) {
            $AvailableBudget -= $restaDelCajaReturn;
        }

        //REGRESAR EL DINERO DE LA DEVOLUCIÓN SIEMPRE Y CUANDO SEA EN EFECTIVO//
        if($devolution){
            $AvailableBudget += $devolution;
        }

        return response()->json(['Information' => $Information, 'MonthlyExpenses' => $MonthlyExpenses, 'AvailableBudget' => $AvailableBudget]);   
    }


    //////////////////////////////////////////////////HISTORIAL DE LA DEVOLUCIÓN DEL DINERO/////////////////////////////////////////////////////////////////////////////// 
    public function historyOfTheReturnOfMoney()
    {
        $history = DB::table('refund_of_money')->select('total_returned', 'total_spent', 'period', 'was_returned_to', 'id_user', 'file', 'created_at')->get();
        
        foreach ($history as $informtaion) {
            $informtaion->created_at = date('d-m-Y', strtotime($informtaion->created_at));

            // Retrieve the user name using the id_user
            $user = DB::table('users')->where('id', $informtaion->id_user)->select('name')->first();
            $informtaion->id_user = $user ? $user->name : null;
        }

        return response()->json(['history' => $history]);
    }

    ////////////////////////////////////////////////////////HISTORIAL DE LA DEVOLUCION DE UN PRODUCTO///////////////////////////////////////////////////////////////////////
    public function DevolutionHistory()
    {
        $DevolutionProduct = DB::table('history_devolution')->select('total_return', 'status', 'id_user', 'created_at', 'id_purchase')->get();

        foreach ($DevolutionProduct as $information){
            $information->created_at = date('d-m-Y', strtotime($information->created_at));
            // Retrieve the user name using the id_user
            $user = DB::table('users')->where('id', $information->id_user)->select('name')->first();
           $information->id_user = $user ? $user->name : null;
        }
        return response()->json(['DevolutionProduct' => $DevolutionProduct]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function ExpenseHistory()
    {
        $MonthlyExpense = [];

        $MonthlyExpenseHistory = DB::table('purchase_requests')->where(function ($query) {
            $query->where(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
            })->orWhere(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
            })->orWhere(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 3)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
            });
        })->select('id', 'total')->get()->toArray();

        foreach ($MonthlyExpenseHistory as $history) {
            $paymentInfo = DB::table('paymentmethodinformation')->where('id', $history->id)->first(['id_user', 'created_at']);
            if ($paymentInfo) {
                $userInfo = DB::table('users')->where('id', $paymentInfo->id_user)->select('name')->first();
                if ($userInfo) {
                    $MonthlyExpense[] = [
                        'id' => $history->id,
                        'total' => $history->total,
                        'created_at' => date('d-m-Y', strtotime($paymentInfo->created_at)),
                        'id_user' => $paymentInfo->id_user,
                        'user_name' => $userInfo->name,
                    ];
                }
            }
        }
        
        return response()->json(['MonthlyExpense' => $MonthlyExpense]);
    }

    public function create(Request $request)
    {
        $user= auth()->user();

        $this->validate($request,[
            'total' => 'required'
        ]);

        $presupuesto = EstimationSmallBox::create([
            'total' => $request->total,
            'id_user' => $user->id,
        ]);

        if($presupuesto){
            return response()->json(['message' => 'Se agregó con éxito el presupuesto', 'status' => 200], 200);
        }
        else
        return response()->json(['message' => 'Error. No se agregó el presupuesto', 'status' => 400], 400);
    }

    public function BudgetReturn(Request $request)
    {
        $user = auth()->user();

        $this->validate($request,[
            'total_returned' => 'required',
            'was_returned_to' => 'required', 
            'file' => 'required'
        ]);

        $mes = Carbon::now()->format('F');
        ///OBTENEMOS EL PRIMER DÍA DEL MES Y EL ÚLTIMO///        
        $primerDiaDelMes = Carbon::now()->startOfMonth();
        $ultimoDiaDelMes = Carbon::now()->endOfMonth();

        ///CONDICIONES PARA PODER SUMAR EL CAMPO "total"///
        //gastosmentuales == monthlyexpenses
        $MonthlyExpenses = DB::table('purchase_requests')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
                                                        ->where(function ($query) {
                                                            $query->where(function ($subquery) {
                                                                $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                                                            })->orWhere(function ($subquery) {
                                                                $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                                                            });
                                                        })->sum('total');

        $path = '';
        if ($request->hasFile('file')) {
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->clientExtension();
            $fileNameToStore = time(). $filename . '.' . $extension;
            $path= $request->file('file')->move('storage/smallbox/files/', $fileNameToStore);
        }

        $returnmoney =RefundOfMoney::create([
            'total_returned' => $request->total_returned,
            'total_spent' => $MonthlyExpenses,
            'period' => $mes,
            'was_returned_to' => $request->was_returned_to,
            'file' => $path,
            'id_user' => $user->id
        ]);    

        if($returnmoney){
            return response()->json(['message' => 'Se regresó el dinero', 'status' => 200], 200);
        }
        else{
            return response()->json(['message' => 'Error,', 'status' => 400], 400);
        }
    }
}
