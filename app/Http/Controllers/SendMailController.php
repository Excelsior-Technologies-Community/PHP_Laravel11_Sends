<?php

namespace App\Http\Controllers;

use App\Jobs\SendQueuedMailJob;
use App\Models\Send;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendMailController extends Controller
{
    public function index()
    {
        return view('mailform');
    }

    // ── Feature 1: Queue + Rate Limiter + Failover dispatch ─────────────────
    public function send(Request $request)
    {
        $request->validate(['to' => 'required|email']);

        $rateLimitKey = 'mail_rate_' . now()->format('Y_m_d_H_i');
        $currentCount = (int) Cache::get($rateLimitKey, 0);

        // Dynamic threshold check before even queuing
        if ($currentCount >= 50) {
            Log::warning("[RateLimiter] Burst limit hit. Queuing deferred for: {$request->to}");
            return back()->with('error', 'Rate limit reached (50/min). Email queued for next window.');
        }

        $data = [
            'title' => 'Welcome to Laravel Sends',
            'body'  => 'This is a test email using Laravel Sends!',
        ];

        // Dispatch to queue — job handles failover + rate tracking
        SendQueuedMailJob::dispatch(
            $request->to,
            'Test Email from Laravel Sends',
            $data,
            'emails.hello',
            null,
            $request->mailer ?? config('mail.default')
        );

        Log::info("[SendMailController] Queued email to: {$request->to}");

        return back()->with('success', "Email queued successfully for {$request->to}!");
    }

    // LIST + SEARCH + FILTER
    public function allSends(Request $request)
    {
        $query = Send::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('to', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $emails = $query->orderBy('id', 'desc')->paginate(10);

        return view('sends', compact('emails'));
    }
}
