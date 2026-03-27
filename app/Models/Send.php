<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Send extends Model
{
    protected $table = 'sends';
    protected $fillable = [
        'uuid',
        'mail_class',
        'subject',
        'content',
        'from',
        'to',
        'sent_at',
    ];
    public $timestamps = true;
}