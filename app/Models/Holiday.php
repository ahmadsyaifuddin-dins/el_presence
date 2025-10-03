<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'name',
        'description',
        'type',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('date', $year);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', Carbon::today());
    }

    // Helper Methods
    public static function isHoliday($date)
    {
        return self::where('date', $date)
            ->where('is_active', true)
            ->exists();
    }

    public static function getHolidaysInRange($startDate, $endDate)
    {
        return self::whereBetween('date', [$startDate, $endDate])
            ->where('is_active', true)
            ->get();
    }
}