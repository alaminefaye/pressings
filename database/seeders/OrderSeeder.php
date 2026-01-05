<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\ClothingType;
use App\Models\Address;
use App\Models\Payment;
use App\Models\OrderStatusHistory;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $client = User::where('role', 'client')->first();
        $employee = User::where('role', 'employee')->first();
        $services = Service::all();
        $clothingTypes = ClothingType::all();

        // Créer une adresse pour le client si elle n'existe pas
        if (!$client->addresses()->exists()) {
            Address::create([
                'user_id' => $client->id,
                'label' => 'Maison',
                'address_line' => 'Cocody Angré 8ème tranche, Près de la pharmacie',
                'city' => 'Abidjan',
                'postal_code' => '00225',
                'latitude' => 5.3599517,
                'longitude' => -4.0082563,
                'is_default' => true,
            ]);
        }

        $address = $client->addresses()->first();

        // Créer 15 commandes avec différents statuts
        $statuses = [
            'pending' => 2,
            'received' => 2,
            'washing' => 3,
            'ironing' => 2,
            'ready' => 3,
            'in_delivery' => 1,
            'delivered' => 2,
        ];

        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                // Créer la commande (order_number est généré automatiquement)
                $order = Order::create([
                    'client_id' => $client->id,
                    'employee_id' => $employee->id,
                    'delivery_type' => rand(0, 1) ? 'pickup' : 'home_delivery',
                    'pickup_address_id' => $address->id,
                    'delivery_address_id' => $address->id,
                    'pickup_date' => now()->addDays(rand(1, 3)),
                    'delivery_date' => now()->addDays(rand(4, 7)),
                    'status' => $status,
                    'subtotal' => 0,
                    'discount' => 0,
                    'delivery_fee' => rand(0, 1) ? 1000 : 0,
                    'total' => 0,
                    'special_instructions' => rand(0, 1) ? 'Urgent - Client VIP' : null,
                ]);

                // Ajouter des articles à la commande
                $itemCount = rand(2, 5);
                $subtotal = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $service = $services->random();
                    $clothingType = $clothingTypes->random();
                    $price = \App\Models\Price::where('service_id', $service->id)
                        ->where('clothing_type_id', $clothingType->id)
                        ->first();

                    if ($price) {
                        $quantity = rand(1, 3);
                        $itemSubtotal = $price->price * $quantity;
                        $subtotal += $itemSubtotal;

                        OrderItem::create([
                            'order_id' => $order->id,
                            'service_id' => $service->id,
                            'clothing_type_id' => $clothingType->id,
                            'quantity' => $quantity,
                            'unit_price' => $price->price,
                            'subtotal' => $itemSubtotal,
                        ]);
                    }
                }

                // Mettre à jour le total de la commande
                $order->subtotal = $subtotal;
                $order->total = $subtotal + $order->delivery_fee - $order->discount;
                $order->save();

                // Créer l'historique des statuts
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'user_id' => $employee->id,
                    'old_status' => null,
                    'new_status' => 'pending',
                    'comment' => 'Commande créée',
                    'created_at' => $order->created_at,
                ]);

                if ($status !== 'pending') {
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'user_id' => $employee->id,
                        'old_status' => 'pending',
                        'new_status' => $status,
                        'comment' => 'Statut mis à jour',
                        'created_at' => $order->created_at->addHours(rand(1, 12)),
                    ]);
                }

                // Créer un paiement pour les commandes livrées ou prêtes
                if (in_array($status, ['ready', 'in_delivery', 'delivered'])) {
                    Payment::create([
                        'order_id' => $order->id,
                        'payment_method' => collect(['cash', 'wallet', 'mobile_money'])->random(),
                        'amount' => $order->total,
                        'status' => $status === 'delivered' ? 'completed' : 'pending',
                        'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                        'paid_at' => $status === 'delivered' ? now() : null,
                    ]);
                }
            }
        }

        $this->command->info('✅ 15 commandes de test créées avec succès !');
    }
}

