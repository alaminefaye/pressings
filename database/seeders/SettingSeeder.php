<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Informations générales
            [
                'key' => 'app_name',
                'value' => 'Pressing App',
                'type' => 'string',
                'description' => 'Nom de l\'application',
            ],
            [
                'key' => 'app_phone',
                'value' => '+225 XX XX XX XX XX',
                'type' => 'string',
                'description' => 'Numéro de téléphone du pressing',
            ],
            [
                'key' => 'app_email',
                'value' => 'contact@pressing.com',
                'type' => 'string',
                'description' => 'Email de contact',
            ],
            [
                'key' => 'app_address',
                'value' => 'Abidjan, Côte d\'Ivoire',
                'type' => 'string',
                'description' => 'Adresse du pressing',
            ],

            // Tarification
            [
                'key' => 'delivery_fee',
                'value' => '1000',
                'type' => 'number',
                'description' => 'Frais de livraison par défaut',
            ],
            [
                'key' => 'free_delivery_threshold',
                'value' => '10000',
                'type' => 'number',
                'description' => 'Montant minimum pour livraison gratuite',
            ],

            // Programme de fidélité
            [
                'key' => 'loyalty_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Activer le programme de fidélité',
            ],
            [
                'key' => 'loyalty_points_per_amount',
                'value' => '100',
                'type' => 'number',
                'description' => 'Montant dépensé pour gagner 1 point',
            ],
            [
                'key' => 'loyalty_points_value',
                'value' => '10',
                'type' => 'number',
                'description' => 'Valeur monétaire d\'1 point de fidélité',
            ],

            // OTP
            [
                'key' => 'otp_expiry_minutes',
                'value' => '5',
                'type' => 'number',
                'description' => 'Durée de validité du code OTP en minutes',
            ],
            [
                'key' => 'otp_length',
                'value' => '6',
                'type' => 'number',
                'description' => 'Nombre de chiffres du code OTP',
            ],

            // Notifications
            [
                'key' => 'sms_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Activer les notifications SMS',
            ],
            [
                'key' => 'push_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Activer les notifications push',
            ],

            // Horaires
            [
                'key' => 'opening_hours',
                'value' => json_encode([
                    'monday' => ['open' => '08:00', 'close' => '18:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '18:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '18:00'],
                    'thursday' => ['open' => '08:00', 'close' => '18:00'],
                    'friday' => ['open' => '08:00', 'close' => '18:00'],
                    'saturday' => ['open' => '09:00', 'close' => '14:00'],
                    'sunday' => ['open' => null, 'close' => null],
                ]),
                'type' => 'json',
                'description' => 'Horaires d\'ouverture',
            ],

            // Zones de livraison
            [
                'key' => 'delivery_zones',
                'value' => json_encode([
                    'Zone 1' => 1000,
                    'Zone 2' => 1500,
                    'Zone 3' => 2000,
                ]),
                'type' => 'json',
                'description' => 'Zones et frais de livraison',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}

