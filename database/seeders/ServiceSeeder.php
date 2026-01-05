<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Lavage simple',
                'slug' => 'lavage-simple',
                'description' => 'Lavage à l\'eau uniquement',
                'duration_hours' => 24,
            ],
            [
                'name' => 'Lavage + Repassage',
                'slug' => 'lavage-repassage',
                'description' => 'Lavage à l\'eau et repassage',
                'duration_hours' => 48,
            ],
            [
                'name' => 'Repassage seul',
                'slug' => 'repassage-seul',
                'description' => 'Repassage uniquement',
                'duration_hours' => 24,
            ],
            [
                'name' => 'Nettoyage à sec',
                'slug' => 'nettoyage-sec',
                'description' => 'Nettoyage à sec pour tissus délicats',
                'duration_hours' => 72,
            ],
            [
                'name' => 'Pressing express',
                'slug' => 'pressing-express',
                'description' => 'Service rapide en 6 heures',
                'duration_hours' => 6,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}

