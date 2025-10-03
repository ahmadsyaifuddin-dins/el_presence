<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            [
                'date' => '2025-01-01',
                'name' => 'Tahun Baru Masehi',
                'type' => 'national'
            ],
            [
                'date' => '2025-02-12',
                'name' => 'Imlek',
                'type' => 'national'
            ],
            [
                'date' => '2025-03-14',
                'name' => 'Hari Raya Nyepi',
                'type' => 'national'
            ],
            [
                'date' => '2025-03-29',
                'name' => 'Wafat Isa Almasih',
                'type' => 'national'
            ],
            [
                'date' => '2025-04-01',
                'name' => 'Isra Miraj',
                'type' => 'national'
            ],
            [
                'date' => '2025-05-01',
                'name' => 'Hari Buruh',
                'type' => 'national'
            ],
            [
                'date' => '2025-05-29',
                'name' => 'Kenaikan Isa Almasih',
                'type' => 'national'
            ],
            [
                'date' => '2025-06-01',
                'name' => 'Hari Pancasila',
                'type' => 'national'
            ],
            [
                'date' => '2025-08-17',
                'name' => 'Hari Kemerdekaan RI',
                'type' => 'national'
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::create([
                'date' => $holiday['date'],
                'name' => $holiday['name'],
                'description' => 'Hari libur ' . $holiday['name'],
                'type' => $holiday['type'],
                'is_active' => true,
            ]);
        }
    }
}