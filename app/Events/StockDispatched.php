<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PrivateChannel;
class StockDispatched implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $stockId;
    public $warehouseId;
    public $message;

    public function __construct($stockId, $warehouseId)
    {
        $this->stockId = $stockId;
        $this->warehouseId = $warehouseId;
        $this->message = "A stock item (ID: {$stockId}) has been dispatched.";
    }

   public function broadcastOn()
    {
        return new Channel('warehouse.' ,1);
    }
    


}
