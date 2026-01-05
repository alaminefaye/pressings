<?php

namespace Database\Seeders;

use App\Models\ClothingType;
use Illuminate\Database\Seeder;

class ClothingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clothingTypes = [
            [
                'name' => 'Chemise',
                'slug' => 'chemise',
                'description' => 'Chemise homme ou femme',
                'icon' => 'shirt',
            ],
            [
                'name' => 'Pantalon',
                'slug' => 'pantalon',
                'description' => 'Pantalon classique',
                'icon' => 'pants',
            ],
            [
                'name' => 'Robe',
                'slug' => 'robe',
                'description' => 'Robe femme',
                'icon' => 'dress',
            ],
            [
                'name' => 'Costume',
                'slug' => 'costume',
                'description' => 'Costume complet (veste + pantalon)',
                'icon' => 'suit',
            ],
            [
                'name' => 'Veste/Blazer',
                'slug' => 'veste-blazer',
                'description' => 'Veste ou blazer',
                'icon' => 'jacket',
            ],
            [
                'name' => 'Jupe',
                'slug' => 'jupe',
                'description' => 'Jupe femme',
                'icon' => 'skirt',
            ],
            [
                'name' => 'T-shirt',
                'slug' => 't-shirt',
                'description' => 'T-shirt ou polo',
                'icon' => 'tshirt',
            ],
            [
                'name' => 'Pull/Gilet',
                'slug' => 'pull-gilet',
                'description' => 'Pull ou gilet',
                'icon' => 'sweater',
            ],
            [
                'name' => 'Manteau',
                'slug' => 'manteau',
                'description' => 'Manteau ou pardessus',
                'icon' => 'coat',
            ],
            [
                'name' => 'Jean',
                'slug' => 'jean',
                'description' => 'Pantalon en jean',
                'icon' => 'jeans',
            ],
            [
                'name' => 'Couverture',
                'slug' => 'couverture',
                'description' => 'Couverture ou plaid',
                'icon' => 'blanket',
            ],
            [
                'name' => 'Drap',
                'slug' => 'drap',
                'description' => 'Drap de lit',
                'icon' => 'bedsheet',
            ],
            [
                'name' => 'Rideau',
                'slug' => 'rideau',
                'description' => 'Rideau',
                'icon' => 'curtain',
            ],
        ];

        foreach ($clothingTypes as $type) {
            ClothingType::create($type);
        }
    }
}

