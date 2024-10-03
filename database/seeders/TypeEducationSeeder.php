<?php

namespace Database\Seeders;

use App\Models\TypeEducation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeEducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TypeEducation::create([
            'name' => 'Ensino Primario',
        ]);

        TypeEducation::create([
            'name' => 'Ensino Secundario',
        ]);

        TypeEducation::create([
            'name' => 'Ensino Primario e Secundario',
        ]);

        TypeEducation::create([
            'name' => 'Ensino Médio',
        ]);
    }
}
