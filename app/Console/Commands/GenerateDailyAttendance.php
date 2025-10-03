<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateDailyAttendance extends Command
{
    protected $signature = 'attendance:generate {date?}';
    protected $description = 'Generate daily attendance records for all active employees';

    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::today();
        
        $this->info("Generating attendance records for: " . $date->format('Y-m-d'));

        // Skip weekends
        if ($date->isWeekend()) {
            $this->warn('Skipping weekend date: ' . $date->format('l, Y-m-d'));
            return;
        }

        // Skip holidays
        if (Holiday::isHoliday($date)) {
            $holiday = Holiday::where('date', $date)->first();
            $this->warn('Skipping holiday: ' . $holiday->name . ' (' . $date->format('Y-m-d') . ')');
            return;
        }

        $employees = Employee::active()->get();
        $created = 0;
        $existing = 0;

        foreach ($employees as $employee) {
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', $date)
                ->first();

            if (!$attendance) {
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'status' => 'Alpa',
                ]);
                $created++;
            } else {
                $existing++;
            }
        }

        $this->info("✅ Created: {$created} records");
        $this->info("ℹ️  Existing: {$existing} records");
        $this->info("Total employees: " . $employees->count());
    }
}