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
        // 1. Seed System Settings
        Setting::set('price_per_round', '500', 'Standard price charged per round/frame in PKR');
        Setting::set('club_name', 'CueMaster Snooker Club', 'Official business name of the snooker club');
        Setting::set('currency', 'Rs.', 'Currency prefix symbol');
        Setting::set('club_phone', '+92 300 1234567', 'Reception contact number');
        Setting::set('club_address', 'Main Boulevard, Phase 4, Commercial Area', 'Physical address');

        // 2. Seed Users (Admin & Staff)
        $admin = User::updateOrCreate(
            ['email' => 'admin@snooker.club'],
            [
                'name' => 'Club Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'staff@snooker.club'],
            [
                'name' => 'Reception Staff',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        // 3. Seed Snooker & Pool Tables
        $tablesData = [
            ['name' => 'Table 1', 'type' => 'Standard Snooker', 'status' => 'available'],
            ['name' => 'Table 2', 'type' => 'Standard Snooker', 'status' => 'available'],
            ['name' => 'Table 3', 'type' => 'Tournament Match', 'status' => 'available'],
            ['name' => 'Table 4', 'type' => 'Standard Snooker', 'status' => 'available'],
            ['name' => 'Table 5', 'type' => '8-Ball American Pool', 'status' => 'available'],
            ['name' => 'Table 6', 'type' => 'VIP Championship', 'status' => 'available'],
        ];

        $createdTables = [];
        foreach ($tablesData as $t) {
            $createdTables[$t['name']] = ClubTable::updateOrCreate(
                ['name' => $t['name']],
                ['type' => $t['type'], 'status' => $t['status']]
            );
        }

        // 4. Seed Customers
        $customersData = [
            ['name' => 'Ahmed Khan', 'phone' => '0300-1112233'],
            ['name' => 'Ali Raza', 'phone' => '0321-4455667'],
            ['name' => 'Usman Ahmed', 'phone' => '0333-7788990'],
            ['name' => 'Hassan', 'phone' => '0312-3344556'],
            ['name' => 'Bilal', 'phone' => '0345-9988776'],
        ];

        $createdCustomers = [];
        foreach ($customersData as $c) {
            $createdCustomers[$c['name']] = Customer::updateOrCreate(
                ['name' => $c['name']],
                ['phone' => $c['phone']]
            );
        }

        // 5. Seed Realistic Today Completed Sessions for Testing Dashboard & Reports
        if (GameSession::count() === 0) {
            // Past Session 1: Ahmed Khan (Paid)
            $start1 = Carbon::today()->setHour(13)->setMinute(15);
            $end1 = Carbon::today()->setHour(14)->setMinute(45);
            $sess1 = GameSession::create([
                'session_code' => 'SESS-1001',
                'customer_id' => $createdCustomers['Ahmed Khan']->id,
                'table_id' => $createdTables['Table 1']->id,
                'user_id' => $staff->id,
                'price_per_round' => 500,
                'rounds' => 3,
                'total_price' => 1500,
                'start_time' => $start1,
                'end_time' => $end1,
                'duration_seconds' => $end1->diffInSeconds($start1),
                'payment_status' => 'paid',
                'payment_time' => $end1,
                'payment_method' => 'cash',
                'status' => 'completed',
                'notes' => 'Match won by Ahmed',
            ]);

            Payment::create([
                'game_session_id' => $sess1->id,
                'amount' => 1500,
                'payment_method' => 'cash',
                'status' => 'completed',
                'paid_at' => $end1,
                'user_id' => $staff->id,
            ]);

            // Past Session 2: Ali Raza (Unpaid)
            $start2 = Carbon::today()->setHour(15)->setMinute(0);
            $end2 = Carbon::today()->setHour(16)->setMinute(10);
            GameSession::create([
                'session_code' => 'SESS-1002',
                'customer_id' => $createdCustomers['Ali Raza']->id,
                'table_id' => $createdTables['Table 2']->id,
                'user_id' => $staff->id,
                'price_per_round' => 500,
                'rounds' => 2,
                'total_price' => 1000,
                'start_time' => $start2,
                'end_time' => $end2,
                'duration_seconds' => $end2->diffInSeconds($start2),
                'payment_status' => 'unpaid',
                'payment_time' => null,
                'payment_method' => 'cash',
                'status' => 'completed',
                'notes' => 'Customer promised to pay tomorrow',
            ]);

            // Past Session 3: Usman Ahmed (Paid via Online)
            $start3 = Carbon::yesterday()->setHour(18)->setMinute(20);
            $end3 = Carbon::yesterday()->setHour(20)->setMinute(40);
            $sess3 = GameSession::create([
                'session_code' => 'SESS-1003',
                'customer_id' => $createdCustomers['Usman Ahmed']->id,
                'table_id' => $createdTables['Table 3']->id,
                'user_id' => $admin->id,
                'price_per_round' => 500,
                'rounds' => 5,
                'total_price' => 2500,
                'start_time' => $start3,
                'end_time' => $end3,
                'duration_seconds' => $end3->diffInSeconds($start3),
                'payment_status' => 'paid',
                'payment_time' => $end3,
                'payment_method' => 'online',
                'status' => 'completed',
                'notes' => 'Bank transfer',
            ]);

            Payment::create([
                'game_session_id' => $sess3->id,
                'amount' => 2500,
                'payment_method' => 'online',
                'status' => 'completed',
                'paid_at' => $end3,
                'user_id' => $admin->id,
            ]);
        }
    }
}
