<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            'Finestra 1 Anta',
            'Finestra 2 Ante',
            'Portafinestra 1 Anta',
            'Portafinestra 2 Ante',
            'Tapparella',
            'Zanzariera',
            'Porta Interna',
            'Porta Blindata',
            'Basculante Garage',
            'Ringhiera',
            'Veranda',
        ];

        foreach ($products as $name) {
            \App\Models\Product::firstOrCreate(['name' => $name]);
        }
    }
}
