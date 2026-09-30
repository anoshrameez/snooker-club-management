<?php

namespace Database\Seeders;

use App\Models\ClubTable;
use App\Models\Customer;
use App\Models\GameSession;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Global Default Settings for the 4 Gameplays (Pakistani PKR)
        Setting::set('default_rate_century', '10', 'Century charge per minute (Rs)');
        Setting::set('default_rate_6ball', '130', '6 Ball charge per frame (Rs)');
        Setting::set('default_rate_10ball', '150', '10 Ball charge per frame (Rs)');
        Setting::set('default_rate_oneball', '120', 'One Ball charge per frame (Rs)');
        Setting::set('club_name', 'CueMaster Snooker Club', 'Official business name of the snooker club');
        Setting::set('currency', 'Rs.', 'Currency symbol');
        Setting::set('club_phone', '0300-1234567', 'Reception phone number');
        Setting::set('club_address', 'Snooker Club, Lahore, Pakistan', 'Address');

        // 2. Seed Users with Username & Password
        $admin = User::updateOrCreate(
            ['email' => 'admin@snooker.club'],
            [
                'username' => 'admin',
                'name' => 'Club Owner',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'staff@snooker.club'],
            [
                'username' => 'staff',
                'name' => 'Reception Staff',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        // 3. Seed Tables with individual per-table pricing for all 4 gameplays
        $tablesData = [
            [
                'name' => 'Table 1',
                'type' => 'Standard Snooker',
                'rate_century' => 10.00,
                'rate_6ball' => 130.00,
                'rate_10ball' => 150.00,
                'rate_oneball' => 120.00,
                'status' => 'available'
            ],
            [
                'name' => 'Table 2',
                'type' => 'Standard Snooker',
                'rate_century' => 10.00,
                'rate_6ball' => 130.00,
                'rate_10ball' => 150.00,
                'rate_oneball' => 120.00,
                'status' => 'available'
            ],
            [
                'name' => 'Table 3',
                'type' => 'Tournament Match',
                'rate_century' => 12.00,
                'rate_6ball' => 140.00,
                'rate_10ball' => 160.00,
                'rate_oneball' => 130.00,
                'status' => 'available'
            ],
            [
                'name' => 'Table 4',
                'type' => 'Standard Snooker',
                'rate_century' => 10.00,
                'rate_6ball' => 130.00,
                'rate_10ball' => 150.00,
                'rate_oneball' => 120.00,
                'status' => 'available'
            ],
            [
                'name' => 'Table 5',
                'type' => 'English Pool',
                'rate_century' => 10.00,
                'rate_6ball' => 130.00,
                'rate_10ball' => 150.00,
                'rate_oneball' => 120.00,
                'status' => 'available'
            ],
            [
                'name' => 'Table 6',
                'type' => 'VIP Championship',
                'rate_century' => 15.00,
                'rate_6ball' => 180.00,
                'rate_10ball' => 200.00,
                'rate_oneball' => 150.00,
                'status' => 'available'
            ],
        ];

        foreach ($tablesData as $t) {
            ClubTable::updateOrCreate(
                ['name' => $t['name']],
                [
                    'type' => $t['type'],
                    'rate_century' => $t['rate_century'],
                    'rate_6ball' => $t['rate_6ball'],
                    'rate_10ball' => $t['rate_10ball'],
                    'rate_oneball' => $t['rate_oneball'],
                    'status' => $t['status']
                ]
            );
        }

        // 4. Update existing sessions if any
        GameSession::whereNull('game_type')->update([
            'game_type' => '6_ball',
            'rate_applied' => 130.00
        ]);
    }
}
