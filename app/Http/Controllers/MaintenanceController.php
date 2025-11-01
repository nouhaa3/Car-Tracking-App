<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function getTotalCost()
    {
        $total = DB::table('interventions')->sum('cout');
        
        return response()->json(['total' => $total ?? 0]);
    }

    public function getCostByMonth()
    {
        $costs = DB::table('interventions')
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(cout) as total')
            )
            ->where('date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        
        return response()->json($costs);
    }
}