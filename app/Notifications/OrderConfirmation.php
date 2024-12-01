<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail', 'broadcast'];
    }

    public function broadcastOn()
    {
        return ['orders'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Order Confirmation - Morris Pizza #' . $this->order->id)
            ->greeting('Thank you for your order, ' . $this->order->customer_name . '!')
            ->line('We have received your order and will start preparing it soon.')
            ->line('Order Details:')
            ->line('Order #: ' . $this->order->id);

        // Add order items
        foreach ($this->order->items as $item) {
            $message->line($item->quantity . 'x ' . $item->menuItem->item_name . ' - £' . number_format($item->subtotal, 2));
        }

        $message->line('Total Amount: £' . number_format($this->order->total_amount, 2))
            ->line('Delivery Type: ' . ucfirst($this->order->order_type));

        if ($this->order->order_type === 'delivery') {
            $message->line('Delivery Address: ' . $this->order->delivery_address);
            if ($this->order->delivery_instructions) {
                $message->line('Delivery Instructions: ' . $this->order->delivery_instructions);
            }
        }

        $message->line('We will keep you updated on your order status.')
            ->line('Thank you for choosing Morris Pizza!');

        return $message;
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'total' => $this->order->total_amount,
            'status' => $this->order->status,
            'title' => 'New Order Received',
            'message' => 'Order #' . $this->order->id . ' has been received',
            'sound' => 'notifications.mp3'
        ];
    }

    public function toBroadcast($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'title' => 'New Order Received',
            'message' => 'Order #' . $this->order->id . ' has been received',
            'sound' => 'notification.mp3'
        ];
    }
} 