<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'name', 'subject', 'body', 'template', 'mailer',
        'status', 'scheduled_at', 'total', 'sent_count', 'failed_count',
    ];

    protected $casts = ['scheduled_at' => 'datetime'];

    public function contacts()
    {
        return $this->hasMany(CampaignContact::class);
    }
}
