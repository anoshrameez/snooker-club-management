<?php

namespace App\Http\Controllers;

use App\Models\ClubTable;
use App\Models\GameSession;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::today()->toDateString());
        $toDate = $request->input('to_date', Carbon::today()->toDateString());

        $query = GameSession::whereDate('start_time', '>=', $fromDate)
            ->whereDate('start_time', '<=', $toDate)
            ->where('status', '!=', 'cancelled');

        $totalSessions = (clone $query)->count();
        $totalRounds = (int) (clone $query)->sum('rounds');
        $totalRevenue = (float) (clone $query)->sum('total_price');
        $paidAmount = (float) (clone $query)->where('payment_status', 'paid')->sum('total_price');
        $unpaidAmount = (float) (clone $query)->where('payment_status', 'unpaid')->sum('total_price');

        // Table usage breakdown
        $tableUsage = ClubTable::leftJoin('game_sessions', function ($join) use ($fromDate, $toDate) {
            $join->on('tables.id', '=', 'game_sessions.table_id')
                ->whereDate('game_sessions.start_time', '>=', $fromDate)
                ->whereDate('game_sessions.start_time', '<=', $toDate)
                ->where('game_sessions.status', '!=', 'cancelled');
        })
        ->select(
            'tables.id',
            'tables.name',
            'tables.type',
            DB::raw('COUNT(game_sessions.id) as sessions_count'),
            DB::raw('COALESCE(SUM(game_sessions.rounds), 0) as total_rounds'),
            DB::raw('COALESCE(SUM(game_sessions.total_price), 0) as total_revenue')
        )
        ->groupBy('tables.id', 'tables.name', 'tables.type')
        ->orderBy('tables.id')
        ->get();

        // Payment method breakdown
        $paymentMethods = GameSession::whereDate('start_time', '>=', $fromDate)
            ->whereDate('start_time', '<=', $toDate)
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_price) as amount'))
            ->groupBy('payment_method')
            ->get();

        $currency = Setting::get('currency', 'Rs.');
        $clubName = Setting::get('club_name', 'CueMaster Snooker Club');

        return view('reports.index', compact(
            'fromDate',
            'toDate',
            'totalSessions',
            'totalRounds',
            'totalRevenue',
            'paidAmount',
            'unpaidAmount',
            'tableUsage',
            'paymentMethods',
            'currency',
            'clubName'
        ));
    }
}
