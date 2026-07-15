<?php

namespace App\Jobs;

use App\Models\CampaignContact;
use App\Models\Send;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendQueuedMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    // Failover chain — tries in order
    protected array $mailerChain = ['smtp', 'log'];

    public function __construct(
        public string  $to,
        public string  $subject,
        public array   $data,
        public string  $template = 'emails.hello',
        public ?int    $contactId = null,
        public ?string $preferredMailer = null,
    ) {}

    public function handle(): void
    {
        // ── RATE LIMITER: max 50 emails per minute ──────────────────────
        $rateLimitKey = 'mail_rate_' . now()->format('Y_m_d_H_i');
        $currentCount = (int) Cache::get($rateLimitKey, 0);

        if ($currentCount >= 50) {
            Log::warning("[RateLimiter] Throttled: {$this->to} — re-queuing after 60s.");
            $this->release(60);
            return;
        }

        Cache::put($rateLimitKey, $currentCount + 1, 65);
        // ────────────────────────────────────────────────────────────────

        // Build mailer chain: preferred first, then fallbacks
        $chain = $this->preferredMailer
            ? array_unique(array_merge([$this->preferredMailer], $this->mailerChain))
            : $this->mailerChain;

        $sent      = false;
        $lastError = null;

        foreach ($chain as $mailerName) {
            try {
                Mail::mailer($mailerName)->send(
                    $this->template,
                    $this->data,
                    function ($msg) {
                        $msg->to($this->to)
                            ->subject($this->subject)
                            ->from(config('mail.from.address'), config('mail.from.name'));
                    }
                );

                $this->logSend('sent', null, $mailerName);
                Log::info("[Mailer:{$mailerName}] Sent → {$this->to}");
                $sent = true;
                break;

            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::error("[Mailer:{$mailerName}] Failed → {$this->to}: {$lastError}");
            }
        }

        if (!$sent) {
            $this->logSend('failed', $lastError, 'none');
            $this->fail(new \RuntimeException($lastError));
        }
    }

    private function logSend(string $status, ?string $error, string $mailerUsed): void
    {
        Send::create([
            'uuid'       => Str::uuid(),
            'mail_class' => self::class . '@' . $mailerUsed,
            'subject'    => $this->subject,
            'content'    => $error ?? ($this->data['body'] ?? ''),
            'from'       => config('mail.from.address'),
            'to'         => $this->to,
            'status'     => $status,
            'sent_at'    => now(),
        ]);

        if ($this->contactId) {
            CampaignContact::where('id', $this->contactId)->update([
                'status'  => $status,
                'error'   => $error,
                'sent_at' => $status === 'sent' ? now() : null,
            ]);
        }
    }
}
