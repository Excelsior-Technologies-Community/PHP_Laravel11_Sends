<?php

namespace App\Http\Controllers;

use App\Models\Send;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    // ── Feature 2: Live Email Analytics Panel ───────────────────────────────
    public function index()
    {
        $total    = Send::count();
        $sent     = Send::where('status', 'sent')->count();
        $failed   = Send::where('status', 'failed')->count();
        $pending  = Send::where('status', 'pending')->count();

        $deliveryRate = $total > 0 ? round(($sent / $total) * 100, 1) : 0;
        $failureRate  = $total > 0 ? round(($failed / $total) * 100, 1) : 0;

        // Last 7 days daily breakdown for chart
        $dailyStats = Send::select(
                DB::raw('DATE(sent_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status="sent" THEN 1 ELSE 0 END) as sent'),
                DB::raw('SUM(CASE WHEN status="failed" THEN 1 ELSE 0 END) as failed')
            )
            ->whereNotNull('sent_at')
            ->where('sent_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top recipients
        $topRecipients = Send::select('to', DB::raw('COUNT(*) as count'))
            ->groupBy('to')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Recent 10 emails
        $recentEmails = Send::latest('sent_at')->limit(10)->get();

        return view('analytics', compact(
            'total', 'sent', 'failed', 'pending',
            'deliveryRate', 'failureRate',
            'dailyStats', 'topRecipients', 'recentEmails'
        ));
    }
}
