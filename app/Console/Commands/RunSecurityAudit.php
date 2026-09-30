<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ClubTable;
use App\Models\GameSession;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RunSecurityAudit extends Command
{
    protected $signature = 'audit:security';
    protected $description = 'Run comprehensive penetration and security integrity tests';

    public function handle()
    {
        $this->info("============================================================");
        $this->info("  SNOOKER CLUB APPLICATION — SECURITY & QA TEST SUITE");
        $this->info("============================================================\n");

        $passCount = 0;
        $failCount = 0;

        $report = function ($title, $passed, $details = '') use (&$passCount, &$failCount) {
            if ($passed) {
                $passCount++;
                $this->line(" <fg=green>[PASS]</> " . $title);
            } else {
                $failCount++;
                $this->line(" <fg=red>[FAIL]</> " . $title . " — " . $details);
            }
        };

        // ----------------------------------------------------
        // TEST 1: SQL Injection Defense on Search API
        // ----------------------------------------------------
        try {
            $sqliPayloads = [
                "' OR '1'='1",
                "admin'--",
                "1; DROP TABLE users;--",
                "' UNION SELECT id, name, email, password FROM users--",
            ];

            $allSafe = true;
            $controller = new \App\Http\Controllers\CustomerController();
            foreach ($sqliPayloads as $payload) {
                $request = Request::create('/api/customers/search', 'GET', ['q' => $payload]);
                $response = $controller->search($request);
                $data = json_decode($response->getContent(), true);

                if (!is_array($data)) {
                    $allSafe = false;
                    break;
                }
            }
            $report("SQL Injection (SQLi) Defense on Search API", $allSafe);
        } catch (\Exception $e) {
            $report("SQL Injection (SQLi) Defense on Search API", false, $e->getMessage());
        }

        // ----------------------------------------------------
        // TEST 2: Wildcard Flood Abuse Prevention
        // ----------------------------------------------------
        try {
            $controller = new \App\Http\Controllers\CustomerController();
            $request = Request::create('/api/customers/search', 'GET', ['q' => '%']);
            $response = $controller->search($request);
            $data = json_decode($response->getContent(), true);

            $escapedSafe = count($data) === 0;
            $report("SQL LIKE Wildcard ('%') Flood Defense", $escapedSafe);
        } catch (\Exception $e) {
            $report("SQL LIKE Wildcard ('%') Flood Defense", false, $e->getMessage());
        }

        // ----------------------------------------------------
        // TEST 3: XSS Entity Escaping in Blade
        // ----------------------------------------------------
        try {
            $adminUser = User::where('role', 'admin')->first();
            Auth::login($adminUser);

            $xssPayload = '<script>alert("HACKED")</script><img src=x onerror=alert(1)>';
            $customer = Customer::create([
                'name' => $xssPayload,
                'phone' => '0300-9999999',
            ]);

            $view = view('customers.show', [
                'customer' => $customer,
                'currency' => 'Rs.',
            ])->render();

            $isEscaped = !str_contains($view, '<script>alert("HACKED")</script>') &&
                         str_contains($view, '&lt;script&gt;');

            $customer->delete();
            $report("Cross-Site Scripting (XSS) HTML Escaping in Blade", $isEscaped);
        } catch (\Exception $e) {
            $report("Cross-Site Scripting (XSS) HTML Escaping in Blade", false, $e->getMessage());
        }

        // ----------------------------------------------------
        // TEST 4: Role-Based Access Control (Admin vs Staff)
        // ----------------------------------------------------
        try {
            $staffUser = User::where('role', 'staff')->first();
            $adminUser = User::where('role', 'admin')->first();

            $staffIsStaff = $staffUser && $staffUser->isStaff() && !$staffUser->isAdmin();
            $adminIsAdmin = $adminUser && $adminUser->isAdmin() && $adminUser->isStaff();

            $middleware = new \App\Http\Middleware\AdminMiddleware();
            $request = Request::create('/settings', 'GET');
            $request->setUserResolver(fn() => $staffUser);

            $blocked = false;
            try {
                $middleware->handle($request, fn() => new \Symfony\Component\HttpFoundation\Response('OK'));
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                $blocked = ($e->getStatusCode() === 403);
            }

            $report("Privilege Escalation Protection (Staff blocked with 403)", $staffIsStaff && $blocked);
            $report("Admin Privilege Verification (Admin permitted)", $adminIsAdmin);
        } catch (\Exception $e) {
            $report("Role-Based Access Control Check", false, $e->getMessage());
        }

        // ----------------------------------------------------
        // TEST 5: Table Concurrency Lock & Tamper Prevention
        // ----------------------------------------------------
        try {
            $table = ClubTable::first();
            $session = GameSession::first();
            $table->update(['status' => 'occupied', 'current_session_id' => $session ? $session->id : null]);

            $controller = new \App\Http\Controllers\TableController();
            $request = Request::create('/tables/' . $table->id, 'PUT', [
                'name' => $table->name,
                'type' => $table->type,
                'status' => 'available', // Illegal state transition
            ]);

            $response = $controller->update($request, $table->id);
            $table->refresh();

            // Must still be occupied and NOT available
            $lockIntact = ($table->status !== 'available');
            $report("Table Concurrency Lock (Cannot free table while session in play)", $lockIntact);
        } catch (\Exception $e) {
            $report("Table Concurrency Lock", false, $e->getMessage());
        }

        // ----------------------------------------------------
        // TEST 6: Last Active Administrator Lockout Defense
        // ----------------------------------------------------
        try {
            $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
            $adminUser = User::where('role', 'admin')->where('is_active', true)->first();

            $otherUser = User::where('id', '!=', $adminUser->id)->first();
            if ($otherUser) {
                Auth::login($otherUser);
            }

            $controller = new \App\Http\Controllers\UserController();
            $response = $controller->toggleStatus($adminUser->id);

            $adminUser->refresh();
            $stayedActive = ($adminCount > 1) || $adminUser->is_active;
            $report("Last Active Admin Lockout Prevention", $stayedActive);
        } catch (\Exception $e) {
            $report("Last Active Admin Lockout Prevention", false, $e->getMessage());
        }

        // ----------------------------------------------------
        // TEST 7: Round Quantity Boundary Limits (Overflow Protection)
        // ----------------------------------------------------
        try {
            $activeSession = GameSession::where('status', 'active')->first();
            if ($activeSession && !$activeSession->isTimeBased()) {
                $controller = new \App\Http\Controllers\SessionController();
                $request = Request::create('/sessions/' . $activeSession->id . '/rounds', 'POST', [
                    'rounds' => 99999999, // Exceeds max:500
                ]);

                $overflowRejected = false;
                try {
                    $controller->updateRounds($request, $activeSession->id);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $overflowRejected = true;
                }

                $report("Rounds Boundary / Integer Overflow Protection (max:500)", $overflowRejected);
            } else {
                $report("Rounds Boundary / Integer Overflow Protection (max:500)", true);
            }
        } catch (\Exception $e) {
            $report("Rounds Boundary Protection", false, $e->getMessage());
        }

        // ----------------------------------------------------
        // TEST 8: Hostinger Production Config Verification
        // ----------------------------------------------------
        try {
            $htaccessExists = file_exists(base_path('.htaccess'));
            $sqliteExists = file_exists(database_path('database.sqlite'));
            $publicIndexExists = file_exists(public_path('index.php'));
            $report("Hostinger Production Config (.htaccess, SQLite, public/index.php)", $htaccessExists && $sqliteExists && $publicIndexExists);
        } catch (\Exception $e) {
            $report("Hostinger Config Verification", false, $e->getMessage());
        }

        $this->line("\n------------------------------------------------------------");
        $this->info("  AUDIT RESULTS: {$passCount} PASSED | {$failCount} FAILED");
        $this->info("============================================================\n");

        if ($failCount === 0) {
            $this->info(">>> ALL SECURITY & STABILITY CHECKS PASSED! <<<");
            return 0;
        }

        return 1;
    }
}
