<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\School;
use Carbon\Carbon;

class AcademicYearService
{
    /**
     * Cria um novo ano letivo para a escola fornecida.
     *
     * @param School $school A escola para a qual o ano letivo será criado.
     * @param int|null $year O ano (opcional). Se não fornecido, será o ano atual.
     * @param string|null $startDate Data de início do ano letivo (opcional). Se não fornecida, será 1º de janeiro do ano.
     * @param string|null $endDate Data de término do ano letivo (opcional). Se não fornecida, será 31 de dezembro do ano.
     * @return AcademicYear O ano letivo recém-criado.
     */
    public function createAcademicYear(School $school, int $year = null, string $startDate = null, string $endDate = null): AcademicYear
    {
        $currentYear = $year ?? Carbon::now()->year;

        $startDate = $startDate ?? Carbon::create($currentYear, 1, 1)->toDateString();
        $endDate = $endDate ?? Carbon::create($currentYear, 12, 31)->toDateString();

        $academicYear = AcademicYear::create([
            'school_id' => $school->id,
            'year' => $currentYear,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
        ]);

        $this->createTrimesters($academicYear);

        return $academicYear;
    }

    /**
     * Cria o próximo ano letivo com base no último ano registrado para a escola.
     *
     * @param School $school A escola para a qual o próximo ano letivo será criado.
     * @throws \Exception Se nenhum ano letivo anterior for encontrado.
     */
    public function createNextYear(School $school): void
    {
        $lastYear = $school->academicYears()->latest('year')->first();

        if (!$lastYear) {
            throw new \Exception('Ano letivo anterior não encontrado.');
        }

        $newYear = AcademicYear::create([
            'school_id' => $school->id,
            'year' => $lastYear->year + 1,
            'start_date' => Carbon::create($lastYear->year + 1, 1, 1),
            'end_date' => Carbon::create($lastYear->year + 1, 12, 31),
            'status' => 'active',
        ]);

        $this->createTrimesters($newYear);
    }

    /**
     * Cria os trimestres para o ano letivo fornecido.
     *
     * @param AcademicYear $academicYear O ano letivo para o qual os trimestres serão criados.
     */
    public function createTrimesters(AcademicYear $academicYear): void
    {
        $academicYear->trimesters()->createMany([
            [
                'name' => 'Primeiro Trimestre',
                'start_date' => Carbon::create($academicYear->year, 1, 1),
                'end_date' => Carbon::create($academicYear->year, 4, 30),
            ],
            [
                'name' => 'Segundo Trimestre',
                'start_date' => Carbon::create($academicYear->year, 5, 1),
                'end_date' => Carbon::create($academicYear->year, 8, 31),
            ],
            [
                'name' => 'Terceiro Trimestre',
                'start_date' => Carbon::create($academicYear->year, 9, 1),
                'end_date' => Carbon::create($academicYear->year, 12, 31),
            ],
        ]);
    }
}
