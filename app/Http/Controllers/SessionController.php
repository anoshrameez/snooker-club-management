<?php

namespace App\Http\Controllers;

use App\Models\ClubTable;
use App\Models\Customer;
use App\Models\GameSession;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    /**
     * Display session history with filters.
     */
    public function index(Request $request)
    {
        $query = GameSession::with(['customer', 'table', 'user'])
            ->orderBy('id', 'desc');

        // Filter: Search (Customer Name, Phone, Code)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('session_code', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Filter: Table
        if ($request->filled('table_id')) {
            $query->where('table_id', $request->table_id);
        }

        // Filter: Payment Status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter: Session Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        $sessions = $query->paginate(15)->withQueryString();
        $tables = ClubTable::orderBy('id')->get();
        $currency = Setting::get('currency', 'Rs.');

        return view('sessions.index', compact('sessions', 'tables', 'currency'));
    }

    /**
     * Show the form to start a new session.
     */
    public function create(Request $request)
    {
        $availableTables = ClubTable::where('status', 'available')
            ->whereNull('current_session_id')
            ->orderBy('id')
            ->get();

        $selectedTableId = $request->query('table_id');
        $defaultPrice = (float) Setting::get('price_per_round', 500);
        $currency = Setting::get('currency', 'Rs.');
        $recentCustomers = Customer::latest()->limit(8)->get();

        return view('sessions.create', compact(
            'availableTables',
            'selectedTableId',
            'defaultPrice',
            'currency',
            'recentCustomers'
        ));
    }

    /**
     * Start and persist a new session.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'table_id' => ['required', 'exists:tables,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        return DB::transaction(function () use ($validated) {
            // Business Rule: Check table availability atomically
            $table = ClubTable::lockForUpdate()->findOrFail($validated['table_id']);

            if (!$table->isAvailable()) {
                return back()->withInput()->withErrors([
                    'table_id' => "{$table->name} is already occupied or in maintenance. Please select an available table.",
                ]);
            }

            // Customer lookup or create
            $customerName = trim($validated['customer_name']);
            $customerPhone = !empty($validated['customer_phone']) ? trim($validated['customer_phone']) : null;

            $customer = Customer::where('name', $customerName)
                ->when($customerPhone, fn($q) => $q->where('phone', $customerPhone))
                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'name' => $customerName,
                    'phone' => $customerPhone,
                ]);
            } elseif ($customerPhone && empty($customer->phone)) {
                $customer->update(['phone' => $customerPhone]);
            }

            // Lock price from system settings for historical accuracy
            $lockedPrice = (float) Setting::get('price_per_round', 500);
            $startTime = Carbon::now();

            // Generate clean human-readable session code e.g. SESS-1025
            $nextId = (GameSession::max('id') ?? 0) + 1;
            $sessionCode = 'SESS-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Create GameSession
            $session = GameSession::create([
                'session_code' => $sessionCode,
                'customer_id' => $customer->id,
                'table_id' => $table->id,
                'user_id' => Auth::id(),
                'price_per_round' => $lockedPrice,
                'rounds' => 1,
                'total_price' => $lockedPrice,
                'start_time' => $startTime,
                'end_time' => null,
                'duration_seconds' => 0,
                'payment_status' => 'unpaid',
                'status' => 'active',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Mark table occupied & link active session
            $table->update([
                'status' => 'occupied',
                'current_session_id' => $session->id,
            ]);

            return redirect()->route('sessions.show', $session->id)
                ->with('success', "Session started for {$customer->name} on {$table->name}!");
        });
    }

    /**
     * Display active session operator screen OR completed session receipt.
     */
    public function show($id)
    {
        $session = GameSession::with(['customer', 'table', 'user', 'payments.user'])->findOrFail($id);
        $currency = Setting::get('currency', 'Rs.');
        $clubName = Setting::get('club_name', 'Break Point Snooker Club');
        $clubPhone = Setting::get('club_phone', '');

        // If completed or cancelled, show receipt/details view
        if (!$session->isActive()) {
            return view('sessions.receipt', compact('session', 'currency', 'clubName', 'clubPhone'));
        }

        // Active session interactive screen
        return view('sessions.active', compact('session', 'currency', 'clubName', 'clubPhone'));
    }

    /**
     * AJAX: Update Round quantity (Shopify quantity style).
     */
    public function updateRounds(Request $request, $id)
    {
        $session = GameSession::findOrFail($id);

        if (!$session->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update rounds for a completed or cancelled session.',
            ], 422);
        }

        $validated = $request->validate([
            'rounds' => ['required', 'integer', 'min:1'],
        ]);

        $rounds = (int) $validated['rounds'];
        $totalPrice = $rounds * $session->price_per_round;

        $session->update([
            'rounds' => $rounds,
            'total_price' => $totalPrice,
        ]);

        $currency = Setting::get('currency', 'Rs.');

        return response()->json([
            'success' => true,
            'rounds' => $session->rounds,
            'price_per_round' => (float) $session->price_per_round,
            'total_price' => (float) $session->total_price,
            'formatted_total' => $currency . ' ' . number_format($session->total_price, 0),
            'saved_at' => Carbon::now()->format('h:i:s A'),
        ]);
    }

    /**
     * AJAX: Update Payment Status (Paid / Unpaid).
     */
    public function updatePayment(Request $request, $id)
    {
        $session = GameSession::findOrFail($id);

        if ($session->isCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update payment for a cancelled session.',
            ], 422);
        }

        $validated = $request->validate([
            'payment_status' => ['required', 'in:paid,unpaid'],
            'payment_method' => ['nullable', 'string', 'in:cash,card,online,other'],
        ]);

        $status = $validated['payment_status'];
        $method = $validated['payment_method'] ?? 'cash';
        $now = Carbon::now();

        DB::transaction(function () use ($session, $status, $method, $now) {
            if ($status === 'paid') {
                $session->update([
                    'payment_status' => 'paid',
                    'payment_time' => $now,
                    'payment_method' => $method,
                ]);

                // Record or update Payment ledger
                Payment::updateOrCreate(
                    ['game_session_id' => $session->id],
                    [
                        'amount' => $session->total_price,
                        'payment_method' => $method,
                        'status' => 'completed',
                        'paid_at' => $now,
                        'user_id' => Auth::id(),
                    ]
                );
            } else {
                $session->update([
                    'payment_status' => 'unpaid',
                    'payment_time' => null,
                ]);

                // Remove or delete payment record
                Payment::where('game_session_id', $session->id)->delete();
            }
        });

        return response()->json([
            'success' => true,
            'payment_status' => $session->payment_status,
            'payment_time' => $session->payment_time ? $session->payment_time->format('h:i A') : null,
            'saved_at' => Carbon::now()->format('h:i:s A'),
        ]);
    }

    /**
     * AJAX: Autosave session state (Customer notes, phone, etc.).
     */
    public function autosave(Request $request, $id)
    {
        $session = GameSession::with('customer')->findOrFail($id);

        if (!$session->isActive()) {
            return response()->json(['success' => false, 'message' => 'Session is not active.'], 422);
        }

        if ($request->has('notes')) {
            $session->update(['notes' => $request->notes]);
        }

        if ($request->filled('customer_name') && $session->customer) {
            $session->customer->update([
                'name' => trim($request->customer_name),
                'phone' => $request->filled('customer_phone') ? trim($request->customer_phone) : $session->customer->phone,
            ]);
        }

        return response()->json([
            'success' => true,
            'saved_at' => Carbon::now()->format('h:i:s A'),
        ]);
    }

    /**
     * Checkout & settle active session (Idempotent).
     */
    public function checkout(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $session = GameSession::lockForUpdate()->findOrFail($id);

            // Business Rule: Prevent accidental duplicate checkout
            if ($session->isCompleted()) {
                return redirect()->route('sessions.show', $session->id)
                    ->with('info', 'This session was already checked out.');
            }

            if ($session->isCancelled()) {
                return redirect()->route('dashboard')
                    ->with('error', 'Cannot checkout a cancelled session.');
            }

            $endTime = Carbon::now();
            $durationSeconds = max(1, $endTime->diffInSeconds($session->start_time));
            $finalTotal = $session->calculateTotal();

            // Check payment status from request or keep existing
            $paymentStatus = $request->input('payment_status', $session->payment_status);
            $paymentMethod = $request->input('payment_method', $session->payment_method ?? 'cash');

            $session->update([
                'end_time' => $endTime,
                'duration_seconds' => $durationSeconds,
                'total_price' => $finalTotal,
                'payment_status' => $paymentStatus,
                'payment_time' => $paymentStatus === 'paid' ? $endTime : null,
                'payment_method' => $paymentMethod,
                'status' => 'completed',
            ]);

            // If marked as paid, record ledger entry
            if ($paymentStatus === 'paid') {
                Payment::updateOrCreate(
                    ['game_session_id' => $session->id],
                    [
                        'amount' => $finalTotal,
                        'payment_method' => $paymentMethod,
                        'status' => 'completed',
                        'paid_at' => $endTime,
                        'user_id' => Auth::id(),
                    ]
                );
            }

            // Free up the table
            if ($session->table_id) {
                ClubTable::where('id', $session->table_id)->update([
                    'status' => 'available',
                    'current_session_id' => null,
                ]);
            }

            return redirect()->route('sessions.show', $session->id)
                ->with('success', "Session checkout completed! Table is now Available.");
        });
    }

    /**
     * Cancel an active session.
     */
    public function cancel(Request $request, $id)
    {
        return DB::transaction(function () use ($id) {
            $session = GameSession::lockForUpdate()->findOrFail($id);

            if (!$session->isActive()) {
                return back()->with('error', 'Only active sessions can be cancelled.');
            }

            $session->update([
                'status' => 'cancelled',
                'end_time' => Carbon::now(),
            ]);

            if ($session->table_id) {
                ClubTable::where('id', $session->table_id)->update([
                    'status' => 'available',
                    'current_session_id' => null,
                ]);
            }

            return redirect()->route('dashboard')
                ->with('info', "Session {$session->session_code} has been cancelled.");
        });
    }
}
