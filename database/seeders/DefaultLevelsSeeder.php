<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            \App\Models\Level::create([
                'type_education_id' => 1,
                'name' => $i . 'º Ano',
                'year' => $i
            ]);
        }

        for ($i = 7; $i <= 9; $i++) {
            \App\Models\Level::create([
                'type_education_id' => 2,
                'name' => $i . 'º Ano',
                'year' => $i
            ]);
        }

        for ($i = 10; $i <= 13; $i++) {
            \App\Models\Level::create([
                'type_education_id' => 4,
                'name' => $i . 'º Ano',
                'year' => $i
            ]);
        }
    }
}
