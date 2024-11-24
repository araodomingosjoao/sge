<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ManageAcademicYear extends Command
{
    protected $signature = 'academic:manage {action : Action to perform (close-current/create-next)}';
    protected $description = 'Manage academic years - close current or create next year';

    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'close-current':
                $this->closeCurrentYear();
                break;
            case 'create-next':
                $this->createNextYear();
                break;
        }
    }

    private function closeCurrentYear()
    {
        AcademicYear::where('status', 'active')
            ->update(['status' => 'closed']);
        
        $this->info('Current academic year closed successfully');
    }

    private function createNextYear()
    {
        $schools = School::all();

        foreach ($schools as $school) {
            $lastYear = $school->academicYears()
                ->latest('year')
                ->first();

            if ($lastYear) {
                $newYear = $lastYear->year + 1;
                
                // Create new academic year
                $academicYear = AcademicYear::create([
                    'school_id' => $school->id,
                    'year' => $newYear,
                    'start_date' => Carbon::create($newYear, 1, 1),
                    'end_date' => Carbon::create($newYear, 12, 31),
                    'status' => 'active'
                ]);

                // Create trimesters
                $this->createTrimesters($academicYear);
            }
        }

        $this->info('Next academic year created successfully');
    }

    private function createTrimesters($academicYear)
    {
        $trimesters = [
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
            ]
        ];

        foreach ($trimesters as $trimesterData) {
            $academicYear->trimesters()->create($trimesterData);
        }
    }
}