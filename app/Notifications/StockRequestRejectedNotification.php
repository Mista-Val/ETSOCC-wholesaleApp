<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class StockRequestRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $stock;

    public function __construct($stock)
    {
        $this->stock = $stock;
    }

    public function via($notifiable)
    {
        // You can add 'mail' if you want email notification too
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Stock Request Rejected',
            'message' => 'Your stock request #' . $this->stock->id . ' has been rejected by the warehouse.',
            'stock_id' => $this->stock->id,
            'status' => 'rejected',
        ];
    }
}
