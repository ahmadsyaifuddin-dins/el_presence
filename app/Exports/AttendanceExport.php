<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Attendance::with(['employee.user'])
            ->whereBetween('date', [
                $this->filters['start_date'] ?? now()->startOfMonth(),
                $this->filters['end_date'] ?? now()->endOfMonth()
            ]);

        if (isset($this->filters['department'])) {
            $query->whereHas('employee', function ($q) {
                $q->where('department', $this->filters['department']);
            });
        }

        if (isset($this->filters['employee_id'])) {
            $query->where('employee_id', $this->filters['employee_id']);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Karyawan',
            'Kode Karyawan',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Terlambat (Menit)',
            'Keterangan',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->date->format('d/m/Y'),
            $attendance->employee->full_name,
            $attendance->employee->employee_code,
            $attendance->time_in ? $attendance->time_in->format('H:i') : '-',
            $attendance->time_out ? $attendance->time_out->format('H:i') : '-',
            $attendance->status,
            $attendance->late_minutes,
            $attendance->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}