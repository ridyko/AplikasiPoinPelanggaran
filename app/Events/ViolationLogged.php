<?php

namespace App\Events;

use App\Models\ViolationLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ViolationLogged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ViolationLog $violationLog;

    /**
     * Create a new event instance.
     */
    public function __construct(ViolationLog $violationLog)
    {
        $this->violationLog = $violationLog;
    }
}
