<?php

namespace App\Http\Controllers;

use App\Models\ClubTable;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'rate_century' => Setting::get('rate_century', '10'),
            'rate_6ball' => Setting::get('rate_6ball', '130'),
            'rate_10ball' => Setting::get('rate_10ball', '150'),
            'rate_oneball' => Setting::get('rate_oneball', '120'),
            'club_name' => Setting::get('club_name', 'CueMaster Snooker Club'),
            'currency' => Setting::get('currency', 'Rs.'),
            'club_phone' => Setting::get('club_phone', ''),
            'club_address' => Setting::get('club_address', ''),
        ];

        $tables = ClubTable::orderBy('id')->get();

        return view('settings.index', compact('settings', 'tables'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'rate_century' => ['required', 'numeric', 'min:1'],
            'rate_6ball' => ['required', 'numeric', 'min:1'],
            'rate_10ball' => ['required', 'numeric', 'min:1'],
            'rate_oneball' => ['required', 'numeric', 'min:1'],
            'club_name' => ['required', 'string', 'max:100'],
            'currency' => ['required', 'string', 'max:10'],
            'club_phone' => ['nullable', 'string', 'max:50'],
            'club_address' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Default club gameplay prices and settings updated successfully.');
    }
}
