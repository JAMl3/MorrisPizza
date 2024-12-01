<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $statusMessages = [
            'pending' => 'We have received your order and will start preparing it soon.',
            'processing' => 'We are now preparing your order.',
            'out_for_delivery' => 'Your order is on its way!',
            'completed' => 'Your order has been delivered. Enjoy!',
            'cancelled' => 'Your order has been cancelled.',
        ];

        $message = $statusMessages[$this->order->status] ?? 'Your order status has been updated.';

        return (new MailMessage)
            ->subject('Order #' . $this->order->id . ' Status Update')
            ->greeting('Hi ' . $this->order->customer_name)
            ->line($message)
            ->line('Order Details:')
            ->line('Order #: ' . $this->order->id)
            ->line('Status: ' . ucfirst($this->order->status))
            ->line('Total Amount: £' . number_format($this->order->total_amount, 2))
            ->line('Thank you for choosing Morris Pizza!');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
        ];
    }
} 