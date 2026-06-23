<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaQueue extends Model
{
    protected $table = 'wa_queue';

    protected $fillable = [
        'violation_log_id',
        'phone_number',
        'message_body',
        'status',
        'error_message',
        'sent_at'
    ];

    public function violationLog()
    {
        return $this->belongsTo(ViolationLog::class, 'violation_log_id');
    }
}
