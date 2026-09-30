<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount(['gameSessions' => function ($q) {
            $q->where('status', '!=', 'cancelled');
        }])->orderBy('name');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(15)->withQueryString();
        $currency = Setting::get('currency', 'Rs.');

        return view('customers.index', compact('customers', 'currency'));
    }

    public function show($id)
    {
        $customer = Customer::with(['gameSessions' => function ($q) {
            $q->with('table')->orderBy('id', 'desc');
        }])->findOrFail($id);

        $currency = Setting::get('currency', 'Rs.');

        return view('customers.show', compact('customer', 'currency'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Customer::create($validated);

        return back()->with('success', 'Customer added successfully.');
    }

    /**
     * AJAX search endpoint for quick customer lookup during session check-in.
     */
    public function search(Request $request)
    {
        $rawQuery = substr(trim($request->get('q', '')), 0, 50);

        if (strlen($rawQuery) < 1) {
            return response()->json([]);
        }

        // Escape SQL LIKE wildcard characters to prevent pattern matching abuse
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $rawQuery);

        $customers = Customer::where('name', 'like', "%{$escaped}%")
            ->orWhere('phone', 'like', "%{$escaped}%")
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($customers);
    }
}
