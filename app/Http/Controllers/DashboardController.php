<?php

namespace App\Http\Controllers;

use App\Models\ClubTable;
use App\Models\GameSession;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Today's Metrics
        $todaySessionsQuery = GameSession::whereDate('start_time', $today)
            ->where('status', '!=', 'cancelled');

        $totalSessions = (clone $todaySessionsQuery)->count();
        $totalRounds = (int) (clone $todaySessionsQuery)->sum('rounds');
        $totalRevenue = (float) (clone $todaySessionsQuery)->sum('total_price');
        $paidAmount = (float) (clone $todaySessionsQuery)->where('payment_status', 'paid')->sum('total_price');
        $unpaidAmount = (float) (clone $todaySessionsQuery)->where('payment_status', 'unpaid')->sum('total_price');

        // Tables & Active Sessions
        $tables = ClubTable::with(['currentSession.customer'])->orderBy('id')->get();
        $activeTablesCount = $tables->where('status', 'occupied')->count();

        // Any active unfinished sessions for quick recovery banner
        $activeSessions = GameSession::with(['customer', 'table'])
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->get();

        // Recent completed sessions today
        $recentSessions = GameSession::with(['customer', 'table', 'user'])
            ->where('status', 'completed')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        $currency = Setting::get('currency', 'Rs.');
        $pricePerRound = Setting::get('price_per_round', 500);

        return view('dashboard.index', compact(
            'totalSessions',
            'totalRounds',
            'totalRevenue',
            'paidAmount',
            'unpaidAmount',
            'activeTablesCount',
            'tables',
            'activeSessions',
            'recentSessions',
            'currency',
            'pricePerRound'
        ));
    }
}
