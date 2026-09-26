<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'price_per_round' => Setting::get('price_per_round', '500'),
            'club_name' => Setting::get('club_name', 'CueMaster Snooker Club'),
            'currency' => Setting::get('currency', 'Rs.'),
            'club_phone' => Setting::get('club_phone', ''),
            'club_address' => Setting::get('club_address', ''),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'price_per_round' => ['required', 'numeric', 'min:1'],
            'club_name' => ['required', 'string', 'max:100'],
            'currency' => ['required', 'string', 'max:10'],
            'club_phone' => ['nullable', 'string', 'max:50'],
            'club_address' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Club settings updated successfully. New sessions will use this updated price per round.');
    }
}
