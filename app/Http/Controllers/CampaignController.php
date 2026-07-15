<?php

namespace App\Http\Controllers;

use App\Jobs\SendQueuedMailJob;
use App\Models\Campaign;
use App\Models\CampaignContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{
    // ── Feature 3: Campaign Scheduling Orchestration ────────────────────────

    public function index()
    {
        $campaigns = Campaign::withCount('contacts')->latest()->paginate(10);
        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $templates = ['emails.hello', 'emails.campaign'];
        $mailers   = array_keys(config('mail.mailers'));
        return view('campaigns.create', compact('templates', 'mailers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'subject'      => 'required|string|max:255',
            'body'         => 'required|string',
            'template'     => 'required|string',
            'mailer'       => 'required|string',
            'scheduled_at' => 'required|date|after:now',
        ]);

        $campaign = Campaign::create($request->only(
            'name', 'subject', 'body', 'template', 'mailer', 'scheduled_at'
        ));

        Log::info("[Campaign] Created: {$campaign->name} scheduled at {$campaign->scheduled_at}");

        return redirect()->route('campaigns.index')
            ->with('success', "Campaign '{$campaign->name}' scheduled successfully!");
    }

    // Dispatch campaign emails immediately (or called by scheduler)
    public function dispatch(Campaign $campaign)
    {
        if (!in_array($campaign->status, ['draft', 'scheduled'])) {
            return back()->with('error', 'Campaign already running or completed.');
        }

        $contacts = $campaign->contacts()->where('status', 'pending')->get();

        if ($contacts->isEmpty()) {
            return back()->with('error', 'No pending contacts in this campaign.');
        }

        $campaign->update(['status' => 'running', 'total' => $contacts->count()]);

        foreach ($contacts as $contact) {
            SendQueuedMailJob::dispatch(
                $contact->email,
                $campaign->subject,
                ['title' => $campaign->name, 'body' => $campaign->body],
                $campaign->template,
                $contact->id,
                $campaign->mailer
            );
        }

        Log::info("[Campaign] Dispatched {$contacts->count()} emails for: {$campaign->name}");

        return back()->with('success', "Campaign dispatched: {$contacts->count()} emails queued!");
    }

    public function show(Campaign $campaign)
    {
        $contacts = $campaign->contacts()->paginate(15);
        return view('campaigns.show', compact('campaign', 'contacts'));
    }
}
