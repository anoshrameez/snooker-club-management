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

        // Concurrency & Integrity Guarantee: Reconcile table statuses with active sessions
        // Any active session strictly locks its table as occupied until explicitly finished
        $activeSessions = GameSession::with(['customer', 'table'])
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->get();

        $activeTableIds = [];
        foreach ($activeSessions as $actSess) {
            if ($actSess->table_id) {
                $activeTableIds[] = $actSess->table_id;
                ClubTable::where('id', $actSess->table_id)->update([
                    'status' => 'occupied',
                    'current_session_id' => $actSess->id,
                ]);
            }
        }

        // Clean up orphaned occupied tables if no active session exists
        ClubTable::where('status', 'occupied')
            ->whereNotIn('id', $activeTableIds)
            ->update([
                'status' => 'available',
                'current_session_id' => null,
            ]);

        // Tables & Active Sessions
        $tables = ClubTable::with(['currentSession.customer'])->orderBy('id')->get();
        $activeTablesCount = $tables->where('status', 'occupied')->count();

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
