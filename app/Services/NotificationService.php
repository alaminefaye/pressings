<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification to user
     */
    public function send(User $user, string $type, string $title, string $message, array $data = [])
    {
        try {
            // Create in-app notification
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);

            // Send push notification if enabled
            if (config('services.push_enabled', true)) {
                $this->sendPushNotification($user, $title, $message, $data);
            }

            // Send SMS if critical notification
            if ($this->isCriticalNotification($type)) {
                $this->sendSmsNotification($user->phone, $message);
            }

            $notification->markAsSent();

            return [
                'success' => true,
                'notification' => $notification,
            ];

        } catch (\Exception $e) {
            Log::error("Failed to send notification: " . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send push notification (Firebase Cloud Messaging)
     */
    protected function sendPushNotification(User $user, string $title, string $message, array $data = [])
    {
        // TODO: Implement Firebase Cloud Messaging
        // This requires:
        // 1. Firebase project setup
        // 2. FCM tokens stored in user table/separate table
        // 3. Laravel Firebase package (kreait/laravel-firebase)

        Log::info("Push notification to {$user->id}: {$title}");

        /* Example implementation:
        
        use Kreait\Firebase\Messaging\CloudMessage;
        use Kreait\Firebase\Messaging\Notification as FCMNotification;
        
        $messaging = app('firebase.messaging');
        
        $message = CloudMessage::withTarget('token', $user->fcm_token)
            ->withNotification(FCMNotification::create($title, $message))
            ->withData($data);
            
        $messaging->send($message);
        */
    }

    /**
     * Send SMS notification
     */
    protected function sendSmsNotification(string $phone, string $message)
    {
        // Reuse OtpService SMS sending capability
        $otpService = app(OtpService::class);
        
        // Call the protected method via reflection or make it public
        // For now, just log
        Log::info("SMS notification to {$phone}: {$message}");

        // TODO: Call actual SMS sending method
    }

    /**
     * Check if notification type is critical
     */
    protected function isCriticalNotification(string $type): bool
    {
        $criticalTypes = [
            'order_ready',
            'order_delivered',
            'delivery_arriving',
            'payment_failed',
        ];

        return in_array($type, $criticalTypes);
    }

    /**
     * Notify order status change
     */
    public function notifyOrderStatus(User $user, $order, string $newStatus)
    {
        $messages = [
            'pending' => [
                'title' => 'Commande reçue',
                'message' => "Votre commande {$order->order_number} a été reçue.",
            ],
            'received' => [
                'title' => 'Articles collectés',
                'message' => "Nous avons collecté vos articles pour la commande {$order->order_number}.",
            ],
            'washing' => [
                'title' => 'Lavage en cours',
                'message' => "Vos articles sont en cours de lavage.",
            ],
            'ironing' => [
                'title' => 'Repassage en cours',
                'message' => "Vos articles sont en cours de repassage.",
            ],
            'ready' => [
                'title' => 'Commande prête! 🎉',
                'message' => "Votre commande {$order->order_number} est prête à être récupérée ou livrée.",
            ],
            'in_delivery' => [
                'title' => 'En cours de livraison',
                'message' => "Votre commande est en cours de livraison.",
            ],
            'delivered' => [
                'title' => 'Commande livrée ✓',
                'message' => "Votre commande {$order->order_number} a été livrée avec succès.",
            ],
            'cancelled' => [
                'title' => 'Commande annulée',
                'message' => "Votre commande {$order->order_number} a été annulée.",
            ],
        ];

        $notification = $messages[$newStatus] ?? [
            'title' => 'Mise à jour de commande',
            'message' => "Statut de votre commande: {$newStatus}",
        ];

        return $this->send(
            $user,
            "order_{$newStatus}",
            $notification['title'],
            $notification['message'],
            ['order_id' => $order->id, 'order_number' => $order->order_number]
        );
    }

    /**
     * Notify payment received
     */
    public function notifyPaymentReceived(User $user, $order, $payment)
    {
        return $this->send(
            $user,
            'payment_received',
            'Paiement reçu',
            "Nous avons bien reçu votre paiement de {$payment->amount} FCFA pour la commande {$order->order_number}.",
            ['order_id' => $order->id, 'payment_id' => $payment->id]
        );
    }

    /**
     * Notify delivery assigned
     */
    public function notifyDeliveryAssigned(User $driver, $delivery)
    {
        return $this->send(
            $driver,
            'delivery_assigned',
            'Nouvelle livraison assignée',
            "Une nouvelle livraison vous a été assignée pour {$delivery->scheduled_at->format('d/m/Y H:i')}.",
            ['delivery_id' => $delivery->id, 'order_id' => $delivery->order_id]
        );
    }

    /**
     * Notify delivery arriving
     */
    public function notifyDeliveryArriving(User $user, $delivery)
    {
        return $this->send(
            $user,
            'delivery_arriving',
            'Le livreur arrive! 🚗',
            "Votre livreur {$delivery->driver->full_name} arrive dans quelques minutes.",
            ['delivery_id' => $delivery->id, 'driver_name' => $delivery->driver->full_name]
        );
    }

    /**
     * Notify promotion available
     */
    public function notifyPromotionAvailable(User $user, $promotion)
    {
        return $this->send(
            $user,
            'promotion_available',
            'Promotion spéciale! 🎁',
            "Utilisez le code {$promotion->code} pour bénéficier de {$promotion->value}" . 
            ($promotion->type === 'percentage' ? '%' : ' FCFA') . " de réduction.",
            ['promotion_id' => $promotion->id, 'code' => $promotion->code]
        );
    }
}


