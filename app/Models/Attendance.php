<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'notes',
        'is_late',
        'late_minutes',
        'checked_in_at',
        'checked_out_at',
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
        'is_late' => 'boolean',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    // Constants
    const STATUS_HADIR = 'Hadir';
    const STATUS_TERLAMBAT = 'Terlambat';
    const STATUS_IZIN = 'Izin';
    const STATUS_ALPA = 'Alpa';
    const STATUS_LIBUR = 'Libur';

    const WORK_START_TIME = '08:00:00';
    const WORK_END_TIME = '14:00:00';

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Scopes
    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->where('date', Carbon::today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year);
    }

    // Helper Methods
    public function checkIn($time = null)
    {
        $currentTime = $time ?: Carbon::now()->format('H:i:s');
        $workStartTime = self::WORK_START_TIME;
        
        $this->time_in = $currentTime;
        $this->checked_in_at = Carbon::now();
        
        // Check if late
        if ($currentTime > $workStartTime) {
            $this->is_late = true;
            $this->status = self::STATUS_TERLAMBAT;
            
            // Calculate late minutes
            $start = Carbon::createFromFormat('H:i:s', $workStartTime);
            $checkin = Carbon::createFromFormat('H:i:s', $currentTime);
            $this->late_minutes = $start->diffInMinutes($checkin);
        } else {
            $this->is_late = false;
            $this->status = self::STATUS_HADIR;
            $this->late_minutes = 0;
        }
        
        $this->save();
    }

    public function checkOut($time = null)
    {
        $currentTime = $time ?: Carbon::now()->format('H:i:s');
        
        $this->time_out = $currentTime;
        $this->checked_out_at = Carbon::now();
        $this->save();
    }

    public function setPermission($reason)
    {
        $this->status = self::STATUS_IZIN;
        $this->notes = $reason;
        $this->save();
    }

    public function getWorkingHours()
    {
        if ($this->time_in && $this->time_out) {
            $timeIn = Carbon::createFromFormat('H:i:s', $this->time_in);
            $timeOut = Carbon::createFromFormat('H:i:s', $this->time_out);
            return $timeIn->diffInHours($timeOut);
        }
        return 0;
    }

    public function getStatusColor()
    {
        return match($this->status) {
            self::STATUS_HADIR => 'green',
            self::STATUS_TERLAMBAT => 'yellow',
            self::STATUS_IZIN => 'blue',
            self::STATUS_ALPA => 'red',
            self::STATUS_LIBUR => 'gray',
            default => 'gray'
        };
    }

    public function getStatusIcon()
    {
        return match($this->status) {
            self::STATUS_HADIR => 'fas fa-check-circle',
            self::STATUS_TERLAMBAT => 'fas fa-exclamation-triangle',
            self::STATUS_IZIN => 'fas fa-info-circle',
            self::STATUS_ALPA => 'fas fa-times-circle',
            self::STATUS_LIBUR => 'fas fa-calendar',
            default => 'fas fa-question-circle'
        };
    }
}