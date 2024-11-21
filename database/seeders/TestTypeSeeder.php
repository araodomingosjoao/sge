<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Prova',
                'description' => 'Avaliação formal escrita'
            ],
            [
                'name' => 'Trabalho',
                'description' => 'Projeto ou trabalho em grupo/individual'
            ],
            [
                'name' => 'Apresentação',
                'description' => 'Apresentação oral de trabalho'
            ],
            [
                'name' => 'Participação',
                'description' => 'Nota de participação em aula'
            ],
            [
                'name' => 'Recuperação',
                'description' => 'Avaliação de recuperação'
            ],
        ];

        foreach ($types as $type) {
            \App\Models\TestType::create($type);
        }
    }
}