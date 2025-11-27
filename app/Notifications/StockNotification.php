<?php

namespace App\Notifications;

use App\Models\StockTransferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class StockNotification extends Notification
{
    use Queueable;

    public $stockRequest;
    public $title;
    public $message;
    public $actionType;

    /**
     * Create a new notification instance.
     *
     * @param StockTransferRequest $stockRequest
     * @param string $actionType
     */
    public function __construct(StockTransferRequest $stockRequest, $actionType = 'created')
    {
        $this->stockRequest = $stockRequest;
        $this->actionType = $actionType;

        // Safely get outlet and warehouse names
        $outletName = optional($stockRequest->supplier)->name ?? 'the outlet';
        $warehouseName = optional($stockRequest->receiver)->name ?? 'the warehouse';

        $this->title = match ($actionType) {
            'created' => 'Stock Transfer Created',
            'dispatched' => 'Stock Transfer Dispatched',
            'return_accepted' => 'Return Request Accepted',
            'return_rejected' => 'Return Request Rejected',
            'return_created' => 'Return Request Created',
            'stock_received_by_outlet' => 'Stock Transfer Completed',
            default => 'Stock Transfer Update',
        };

        $this->message = match ($actionType) {
            'created' => "Warehouse has initiated a stock transfer (#{$this->stockRequest->id}) to your outlet. It will be visible once dispatched.",
            'dispatched' => "Warehouse has dispatched your stock transfer (#{$this->stockRequest->id}). It will arrive soon.",
            'return_accepted' => "Warehouse has accepted your return request (#{$this->stockRequest->id}) and updated the stock successfully.",
            'return_rejected' => "Warehouse has rejected your return request (#{$this->stockRequest->id}). Please contact the warehouse for details.",
            'return_created' => "A return request (#{$this->stockRequest->id}) has been created by {$outletName} for {$warehouseName}.",
            'stock_received_by_outlet' => "Your stock transfer (#{$this->stockRequest->id}) has been received and accepted by {$outletName}.",
            default => "Warehouse has updated your stock transfer (#{$this->stockRequest->id}).",
        };
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'stock_request_id' => $this->stockRequest->id,
            'receiver_id' => $this->stockRequest->receiver_id,
            'supplier_id' => $this->stockRequest->supplier_id ?? null,
            'type' => 'warehouse_transfer',
            'status' => $this->stockRequest->status,
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }
}
