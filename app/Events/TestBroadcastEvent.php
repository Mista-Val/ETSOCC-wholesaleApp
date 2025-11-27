<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TestBroadcastEvent implements ShouldBroadcast
{
    use SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    // The channel name the event broadcasts on
    public function broadcastOn()
    {
        return new Channel('test-channel');
    }

    // The event name (optional)
    public function broadcastAs()
    {
        return 'TestBroadcast';
    }
}
