<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailerSwitchController extends Controller
{
    // ── Feature 5: Multi-Mailer Provider Switcher / Failover Engine ─────────

    // All available mailers from config
    private function availableMailers(): array
    {
        return array_keys(config('mail.mailers', []));
    }

    public function index()
    {
        $mailers       = $this->availableMailers();
        $activeMailer  = Cache::get('active_mailer', config('mail.default'));
        $mailerStatus  = Cache::get('mailer_status', []);
        $switchLog     = Cache::get('mailer_switch_log', []);

        return view('mailer-switch', compact('mailers', 'activeMailer', 'mailerStatus', 'switchLog'));
    }

    // Manually switch active mailer
    public function switchMailer(Request $request)
    {
        $request->validate([
            'mailer' => 'required|string|in:' . implode(',', $this->availableMailers()),
        ]);

        $previous = Cache::get('active_mailer', config('mail.default'));
        $new      = $request->mailer;

        Cache::put('active_mailer', $new, now()->addDays(7));

        $log   = Cache::get('mailer_switch_log', []);
        $log[] = [
            'from'       => $previous,
            'to'         => $new,
            'reason'     => 'Manual switch',
            'switched_at'=> now()->toDateTimeString(),
        ];
        Cache::put('mailer_switch_log', array_slice($log, -20), now()->addDays(7));

        Log::info("[MailerSwitch] Manual: {$previous} → {$new}");

        return back()->with('success', "Active mailer switched to: {$new}");
    }

    // Test a specific mailer connection
    public function testMailer(Request $request)
    {
        $request->validate([
            'mailer' => 'required|string|in:' . implode(',', $this->availableMailers()),
            'test_to'=> 'required|email',
        ]);

        $mailerName = $request->mailer;
        $status     = Cache::get('mailer_status', []);

        try {
            Mail::mailer($mailerName)->raw(
                'Connection test from Laravel Sends — ' . now(),
                function ($msg) use ($request) {
                    $msg->to($request->test_to)
                        ->subject('Mailer Connection Test')
                        ->from(config('mail.from.address'), config('mail.from.name'));
                }
            );

            $status[$mailerName] = ['state' => 'online', 'checked_at' => now()->toDateTimeString()];
            Cache::put('mailer_status', $status, now()->addHours(1));
            Log::info("[MailerTest] {$mailerName} → OK");

            return back()->with('success', "{$mailerName} is ONLINE — test email sent!");

        } catch (\Throwable $e) {
            $status[$mailerName] = [
                'state'      => 'offline',
                'error'      => $e->getMessage(),
                'checked_at' => now()->toDateTimeString(),
            ];
            Cache::put('mailer_status', $status, now()->addHours(1));
            Log::error("[MailerTest] {$mailerName} FAILED: " . $e->getMessage());

            // Auto-failover: switch to next available online mailer
            $this->autoFailover($mailerName, $e->getMessage());

            return back()->with('error', "{$mailerName} is OFFLINE: " . $e->getMessage());
        }
    }

    // Auto-failover logic: switch to next mailer if current fails
    private function autoFailover(string $failedMailer, string $reason): void
    {
        $chain   = ['smtp', 'sendmail', 'log'];
        $current = Cache::get('active_mailer', config('mail.default'));

        if ($current !== $failedMailer) return;

        foreach ($chain as $candidate) {
            if ($candidate !== $failedMailer && in_array($candidate, $this->availableMailers())) {
                Cache::put('active_mailer', $candidate, now()->addDays(7));

                $log   = Cache::get('mailer_switch_log', []);
                $log[] = [
                    'from'       => $failedMailer,
                    'to'         => $candidate,
                    'reason'     => 'Auto-failover: ' . $reason,
                    'switched_at'=> now()->toDateTimeString(),
                ];
                Cache::put('mailer_switch_log', array_slice($log, -20), now()->addDays(7));

                Log::warning("[AutoFailover] {$failedMailer} → {$candidate}: {$reason}");
                break;
            }
        }
    }
}
