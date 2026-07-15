<?php

namespace App\Console\Commands;

use App\Jobs\SendQueuedMailJob;
use App\Models\Campaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DispatchScheduledCampaigns extends Command
{
    protected $signature   = 'campaigns:dispatch';
    protected $description = 'Dispatch all campaigns whose scheduled_at time has arrived';

    public function handle(): void
    {
        $campaigns = Campaign::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($campaigns->isEmpty()) {
            $this->info('No campaigns due for dispatch.');
            return;
        }

        foreach ($campaigns as $campaign) {
            $contacts = $campaign->contacts()->where('status', 'pending')->get();

            if ($contacts->isEmpty()) {
                $campaign->update(['status' => 'completed']);
                continue;
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

            Log::info("[Scheduler] Campaign '{$campaign->name}' dispatched {$contacts->count()} emails.");
            $this->info("Dispatched: {$campaign->name} → {$contacts->count()} emails");
        }
    }
}
