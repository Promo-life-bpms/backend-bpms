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
                                                            });
                                                        })->sum('total');
        
        ///presupuestodisponible == AvailableBudget
        $AvailableBudget =number_format($MonthlyBudget - $MonthlyExpenses, 2, '.', '' );

        return response()->json(['Information' => $Information, 'MonthlyExpenses' => $MonthlyExpenses, 'AvailableBudget' => $AvailableBudget]);   
    }


    public function ExpenseHistory()
    {
        $MonthlyExpense = [];

        $MonthlyExpenseHistory = DB::table('purchase_requests')->where(function ($query) {
            $query->where(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
            })->orWhere(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
            });
        })->select('id', 'total')->get()->toArray();
    
        foreach ($MonthlyExpenseHistory as $history) {
            $paymentInfo = DB::table('paymentmethodinformation')->where('id', $history->id)->first(['id_pursache_request','id_user', 'created_at']);
            dd($paymentInfo);
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
            return response()->json(['message' => 'exito', 'status' => 200], 200);
        }
        else
        return response()->json(['message' => 'error', 'status' => 400], 400);
    }

    public function BudgetReturn(Request $request)
    {
        $user = auth()->user();

        $this->validate($request,[
            'total_returned' => 'required',
            'total_spent' => 'required', 
        ]);

        ///OBTENEMOS EL PRIMER DÍA DEL MES Y EL ÚLTIMO///        
        $primerDiaDelMes = Carbon::now()->startOfMonth();
        $ultimoDiaDelMes = Carbon::now()->endOfMonth();

       $mes = Carbon::now()->format('F');

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
                                                            });
                                                        })->sum('total');

        ///presupuestodisponible == AvailableBudget
        $AvailableBudget =($MonthlyBudget - $MonthlyExpenses);
        if($AvailableBudget == $request->total_returned){
            RefundOfMoney::create([
                'total_returned' => $request->total_returned,
                'total_spent' => $MonthlyExpenses,
                'period' => $mes,
                'id_user' => $user->id
            ]);

            return response()->json(['message' => 'Se devolvio el dinero']);
        }
        else{
            return response()->json(['message' => 'Debes insertar la cantidad sobrante exacta']);
        }
    }
}
