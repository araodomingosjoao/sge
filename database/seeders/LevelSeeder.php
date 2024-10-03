<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['type_education_id' => 1, 'year' => 1, 'name' => '1º Classe'],
            ['type_education_id' => 1, 'year' => 2, 'name' => '2º Classe'],
            ['type_education_id' => 1, 'year' => 3, 'name' => '3º Classe'],
            ['type_education_id' => 1, 'year' => 4, 'name' => '4º Classe'],
            ['type_education_id' => 1, 'year' => 5, 'name' => '5º Classe'],
            ['type_education_id' => 1, 'year' => 6, 'name' => '6º Classe'],
            ['type_education_id' => 2, 'year' => 7, 'name' => '7º Classe'],
            ['type_education_id' => 2, 'year' => 8, 'name' => '8º Classe'],
            ['type_education_id' => 2, 'year' => 9, 'name' => '9º Classe'],
        ];

        foreach ($levels as $year) {
            Level::create($year);
        }
    }
}
