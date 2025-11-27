<?php

namespace App\Notifications;

use App\Models\StockTransferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class StockRequestSendNotification extends Notification
{
    use Queueable;

    public $stockRequest;
    public $title;
    public $message;
    public $notificationType;

    /**
     * Create a new notification instance, accepting the StockTransferRequest object.
     * @param StockTransferRequest $stockRequest The newly created stock request.
     * @param string|null $notificationType Optional type to specify notification context (e.g., 'dispatched')
     */
    public function __construct(StockTransferRequest $stockRequest, $notificationType = null)
    {
        $this->stockRequest = $stockRequest;
        $this->notificationType = $notificationType;
        
        // Customize message based on notification type or stock request type
        if ($notificationType === 'dispatched') {
            $this->title = 'Stock Dispatched Successfully';
            $this->message = "Your stock (#{$this->stockRequest->id}) has been dispatched successfully by the admin and is on its way to your warehouse.";
        } elseif ($stockRequest->type === 'admin') {
            $this->title = 'Stock is created by the admin';
            $this->message = "Admin has created a new stock entry (#{$this->stockRequest->id}) from supplier '{$this->stockRequest->supplier_name}' for your warehouse.";
        } else {
            $this->title = 'Outlet Stock Request';
            $this->message = "An outlet has submitted a new stock request (#{$this->stockRequest->id}) that requires your review and fulfillment.";
        }
    }

    /**
     * Get the notification's delivery channels.
     * We only use 'database' for persistence.
     *
     * @param mixed $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Use 'database' only to store a persistent record
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     * This data will be saved in the `notifications` table.
     * @param mixed $notifiable
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        // Determine the notification type
        if ($this->notificationType === 'dispatched') {
            $type = 'stock_dispatched';
        } elseif ($this->stockRequest->type === 'admin') {
            $type = 'new_stock_entry_created';
        } else {
            $type = 'new_stock_request_submitted';
        }

        return [
            'title' => $this->title,
            'message' => $this->message,
            'stock_request_id' => $this->stockRequest->id,
            'receiver_id' => $this->stockRequest->receiver_id,
            'type' => $type,
            'status' => $this->stockRequest->status,
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }
}