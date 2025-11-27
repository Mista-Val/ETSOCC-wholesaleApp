<?php

namespace App\Notifications;

use App\Models\CashRemittance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class SuperVisorNotification extends Notification
{
    use Queueable;

    protected $cashRemittance;
    protected $actionType;

    public function __construct(CashRemittance $cashRemittance, $actionType = 'created')
    {
        $this->cashRemittance = $cashRemittance;
        $this->actionType = $actionType;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        // Detect source name (warehouse or outlet)
        $location = optional($this->cashRemittance->location)->name
            ?? optional($this->cashRemittance->warehouse)->name
            ?? 'Location';

        $amount = number_format($this->cashRemittance->amount, 2);

        $title = match ($this->actionType) {
            'created'  => 'New Cash Remittance Created',
            'approved' => 'Cash Remittance Approved',
            'rejected' => 'Cash Remittance Rejected',
            default    => 'Cash Remittance Update',
        };

        $message = match ($this->actionType) {
            'created'  => "{$location} has created a new cash remittance of ₹{$amount}.",
            'approved' => "Your cash remittance of ₹{$amount} has been approved by the supervisor.",
            'rejected' => "Your cash remittance of ₹{$amount} has been rejected by the supervisor.",
            default    => "{$location} has updated a cash remittance of ₹{$amount}.",
        };

        return [
            'title'       => $title,
            'message'     => $message,
            'remittance_id' => $this->cashRemittance->id,
            'amount'      => $this->cashRemittance->amount,
            'status'      => $this->cashRemittance->status,
            'created_at'  => Carbon::now()->toDateTimeString(),
        ];
    }
}
