<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignContact extends Model
{
    protected $fillable = [
        'campaign_id', 'name', 'email', 'status', 'error', 'sent_at',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
