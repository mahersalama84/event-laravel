<?php

namespace App\Http\Controllers;

use App\Stats\CustomerStats;
use App\Stats\UserStats;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function stats(Request $request){
        $request->validate([
            'start' => ['nullable', Rule::when( 
                $request->start=='prevweek' ||$request->start=='prevmonth' ||$request->start=='prevyear' || $request->start=='thisweek' || $request->start=='thismonth' || $request->start=='thisyear', 
                'in:prevweek,prevmonth,prevyear,thisweek,thismonth,thisyear', 
                'date_format:Y-m-d' 
            ), ],
            'end' => 'nullable|string|date_format:Y-m-d'
        ]);
        $end = $request->end ?
                        Carbon::createFromFormat('Y-m-d',  $request->end)
                        :now()->subSecond();
                                
        $start = Carbon::now()->startOfWeek();
        if($request->start === 'prevweek'){
            $start = Carbon::now()->startOfWeek()->subWeek();
            $end = Carbon::now()->subWeek()->endOfWeek();
        }elseif($request->start === 'prevmonth'){
            $start = Carbon::now()->startOfMonth()->subMonth();
            $end = Carbon::now()->subMonth()->endOfMonth();
        }elseif($request->start === 'prevyear'){
            $start = Carbon::now()->startOfYear()->subYear();
            $end = Carbon::now()->subYear()->endOfYear();
        }elseif($request->start === 'thisweek')
            $start = Carbon::now()->startOfWeek();
        elseif($request->start === 'thismonth')
            $start = Carbon::now()->startOfMonth();
        elseif($request->start === 'thisyear')
            $start = Carbon::now()->startOfYear();  
        elseif($request->start)
            $start = Carbon::createFromFormat('Y-m-d',  $request->start);


        $stats = UserStats::query()
            ->start($start)
            ->end($end)
            ->groupByDay()
            ->get();
        $userStats = $stats->map(function($stat){
            return [
                'value' => $stat->value,
                'start' => $stat->start->format('Y-m-d'),
                'end' => $stat->end->format('Y-m-d'),
                'increments' => $stat->increments,
                'decrements' => $stat->decrements,
                'difference' => $stat->difference
            ];
        });

        $stats = CustomerStats::query()
            ->start($start)
            ->end($end)
            ->groupByDay()
            ->get();
        $customerStats = $stats->map(function($stat){
            return [
                'value' => $stat->value,
                'start' => $stat->start->format('Y-m-d'),
                'end' => $stat->end->format('Y-m-d'),
                'increments' => $stat->increments,
                'decrements' => $stat->decrements,
                'difference' => $stat->difference
            ];
        });        
        return Inertia::render('Tabs/Dashboard', [
            'CustomerStats'=>$customerStats,
            'UserStats'=>$userStats,
            'start'=> $start,
            'end'=> $end,
            'duration' =>$request->start && ($request->start=='prevweek' || $request->start=='prevmonth' || $request->start=='prevyear'|| $request->start=='thisweek'  || $request->start=='thismonth' || $request->start=='thisyear')?$request->start:'thisweek'
        ]);    
    }
}
