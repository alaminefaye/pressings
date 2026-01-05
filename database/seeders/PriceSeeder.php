<?php

namespace Database\Seeders;

use App\Models\ClothingType;
use App\Models\Price;
use App\Models\Service;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Prix de base par type de vêtement et service (en FCFA ou votre devise)
        $priceMatrix = [
            'Chemise' => [
                'Lavage simple' => 500,
                'Lavage + Repassage' => 1000,
                'Repassage seul' => 500,
                'Nettoyage à sec' => 1500,
                'Pressing express' => 1500,
            ],
            'Pantalon' => [
                'Lavage simple' => 500,
                'Lavage + Repassage' => 1000,
                'Repassage seul' => 500,
                'Nettoyage à sec' => 1500,
                'Pressing express' => 1500,
            ],
            'Robe' => [
                'Lavage simple' => 800,
                'Lavage + Repassage' => 1500,
                'Repassage seul' => 700,
                'Nettoyage à sec' => 2000,
                'Pressing express' => 2500,
            ],
            'Costume' => [
                'Lavage simple' => 1500,
                'Lavage + Repassage' => 2500,
                'Repassage seul' => 1000,
                'Nettoyage à sec' => 3000,
                'Pressing express' => 3500,
            ],
            'Veste/Blazer' => [
                'Lavage simple' => 1000,
                'Lavage + Repassage' => 1800,
                'Repassage seul' => 800,
                'Nettoyage à sec' => 2500,
                'Pressing express' => 2800,
            ],
            'Jupe' => [
                'Lavage simple' => 500,
                'Lavage + Repassage' => 1000,
                'Repassage seul' => 500,
                'Nettoyage à sec' => 1500,
                'Pressing express' => 1500,
            ],
            'T-shirt' => [
                'Lavage simple' => 300,
                'Lavage + Repassage' => 700,
                'Repassage seul' => 400,
                'Nettoyage à sec' => 1000,
                'Pressing express' => 1000,
            ],
            'Pull/Gilet' => [
                'Lavage simple' => 800,
                'Lavage + Repassage' => 1500,
                'Repassage seul' => 700,
                'Nettoyage à sec' => 2000,
                'Pressing express' => 2200,
            ],
            'Manteau' => [
                'Lavage simple' => 1500,
                'Lavage + Repassage' => 2500,
                'Repassage seul' => 1000,
                'Nettoyage à sec' => 3500,
                'Pressing express' => 4000,
            ],
            'Jean' => [
                'Lavage simple' => 500,
                'Lavage + Repassage' => 1000,
                'Repassage seul' => 500,
                'Nettoyage à sec' => 1500,
                'Pressing express' => 1500,
            ],
            'Couverture' => [
                'Lavage simple' => 2000,
                'Lavage + Repassage' => 3000,
                'Repassage seul' => 1000,
                'Nettoyage à sec' => 4000,
                'Pressing express' => 4500,
            ],
            'Drap' => [
                'Lavage simple' => 1000,
                'Lavage + Repassage' => 1500,
                'Repassage seul' => 500,
                'Nettoyage à sec' => 2000,
                'Pressing express' => 2200,
            ],
            'Rideau' => [
                'Lavage simple' => 1500,
                'Lavage + Repassage' => 2500,
                'Repassage seul' => 1000,
                'Nettoyage à sec' => 3000,
                'Pressing express' => 3500,
            ],
        ];

        foreach ($priceMatrix as $clothingTypeName => $services) {
            $clothingType = ClothingType::where('name', $clothingTypeName)->first();
            
            if (!$clothingType) continue;

            foreach ($services as $serviceName => $price) {
                $service = Service::where('name', $serviceName)->first();
                
                if (!$service) continue;

                Price::create([
                    'service_id' => $service->id,
                    'clothing_type_id' => $clothingType->id,
                    'price' => $price,
                    'is_active' => true,
                ]);
            }
        }
    }
}

