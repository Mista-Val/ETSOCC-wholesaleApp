<?php

namespace App\Notifications;

use App\Models\StockTransferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StockRequestAcceptedNotification extends Notification
{
    use Queueable;

    public $stockRequest;
    public $message;
    public $title;

    public function __construct(StockTransferRequest $stockRequest, $title = 'Stock Request Accepted', $message = null)
    {
        $this->stockRequest = $stockRequest;
        $this->title = $title;
        $this->message = $message ?? "Stock request #{$stockRequest->id} has been accepted from warehouse.";
    }

    // Only use database channel (stores in database)
    public function via($notifiable)
    {
        return ['database'];
    }

    // This data will be saved in notifications table
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'stock_request_id' => $this->stockRequest->id,
            'type' => 'stock_request_accepted',
            'from' => 'warehouse',
            'created_at' => now(),
        ];
    }
}