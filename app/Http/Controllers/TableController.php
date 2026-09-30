<?php

namespace App\Http\Controllers;

use App\Models\ClubTable;
use App\Models\Setting;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = ClubTable::with(['currentSession.customer'])->orderBy('id')->get();
        $currency = Setting::get('currency', 'Rs.');

        return view('tables.index', compact('tables', 'currency'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:tables,name'],
            'type' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:available,maintenance'],
            'rate_century' => ['nullable', 'numeric', 'min:0'],
            'rate_6ball' => ['nullable', 'numeric', 'min:0'],
            'rate_10ball' => ['nullable', 'numeric', 'min:0'],
            'rate_oneball' => ['nullable', 'numeric', 'min:0'],
        ]);

        ClubTable::create($validated);

        return back()->with('success', "Table {$validated['name']} added with custom rates successfully.");
    }

    public function update(Request $request, $id)
    {
        $table = ClubTable::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:tables,name,' . $table->id],
            'type' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:available,occupied,maintenance'],
            'rate_century' => ['nullable', 'numeric', 'min:0'],
            'rate_6ball' => ['nullable', 'numeric', 'min:0'],
            'rate_10ball' => ['nullable', 'numeric', 'min:0'],
            'rate_oneball' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Do not allow setting to available if an active session is linked
        if ($validated['status'] === 'available' && $table->current_session_id) {
            return back()->with('error', 'Cannot mark table Available while a session is actively in play.');
        }

        $table->update($validated);

        return back()->with('success', "Table {$table->name} rates and details updated successfully.");
    }
}
