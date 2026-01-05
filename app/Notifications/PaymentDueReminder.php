<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentDueReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sale;
    protected $daysOverdue;

    public function __construct($sale, $daysOverdue)
    {
        $this->sale = $sale;
        $this->daysOverdue = $daysOverdue;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Payment Due Reminder - Invoice #' . $this->sale->voucher_number)
            ->greeting('Hello ' . ($this->sale->customer_name ?? 'Customer') . '!');

        if ($this->daysOverdue > 0) {
            $message->error()
                ->line('This is a reminder that your payment is overdue.')
                ->line('Days Overdue: ' . $this->daysOverdue . ' days');
        } else {
            $message->warning()
                ->line('This is a friendly reminder about your upcoming payment.');
        }

        return $message
            ->line('Invoice Number: #' . $this->sale->voucher_number)
            ->line('Amount Due: ৳' . number_format($this->sale->due_amount, 2))
            ->line('Due Date: ' . $this->sale->clear_date)
            ->action('View Invoice', url('/sales/' . $this->sale->id))
            ->line('Please settle this payment at your earliest convenience.');
    }
}
