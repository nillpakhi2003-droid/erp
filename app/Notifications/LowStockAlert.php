<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;
    protected $currentStock;

    public function __construct($product, $currentStock)
    {
        $this->product = $product;
        $this->currentStock = $currentStock;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert - ' . $this->product->name)
            ->warning()
            ->greeting('Low Stock Alert!')
            ->line('The following product is running low on stock:')
            ->line('Product: ' . $this->product->name)
            ->line('Current Stock: ' . $this->currentStock)
            ->line('Minimum Stock: ' . ($this->product->min_stock ?? 'Not set'))
            ->action('View Product', url('/products/' . $this->product->id))
            ->line('Please restock soon to avoid stockouts.');
    }

    public function toArray($notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->currentStock,
            'type' => 'low_stock',
        ];
    }
}
