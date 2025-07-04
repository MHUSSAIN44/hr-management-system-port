<?php
// Updated Attendance Model

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'date',
        'shift',
        'check_in_time',
        'check_out_time',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_address',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_address',
        'check_in_ip',
        'check_out_ip',
        'check_in_user_agent',
        'check_out_user_agent',
        'check_in_accuracy',
        'check_out_accuracy',
        'total_hours',
        'break_start_time',
        'break_end_time',
        'break_duration',
        'notes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'break_start_time' => 'datetime',
        'break_end_time' => 'datetime',
        'check_in_latitude' => 'decimal:8',
        'check_in_longitude' => 'decimal:8',
        'check_out_latitude' => 'decimal:8',
        'check_out_longitude' => 'decimal:8',
        'check_in_accuracy' => 'decimal:2',
        'check_out_accuracy' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'break_duration' => 'decimal:2',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper methods
    public function isPresent()
    {
        return $this->status === 'present';
    }

    public function isAbsent()
    {
        return $this->status === 'absent';
    }

    public function isLate()
    {
        return $this->status === 'late';
    }

    public function isCheckedIn()
    {
        return $this->status === 'checked_in' && $this->check_in_time && !$this->check_out_time;
    }

    public function checkIn($latitude, $longitude, $address, $accuracy, $shift = 'morning')
    {
        $this->update([
            'check_in_time' => now(),
            'check_in_latitude' => $latitude,
            'check_in_longitude' => $longitude,
            'check_in_address' => $address,
            'check_in_accuracy' => $accuracy,
            'check_in_ip' => request()->ip(),
            'check_in_user_agent' => request()->userAgent(),
            'shift' => $shift,
            'status' => 'checked_in',
        ]);
    }

    public function checkOut($latitude, $longitude, $address, $accuracy)
    {
        $checkInTime = Carbon::parse($this->check_in_time);
        $checkOutTime = now();

        // Calculate total hours correctly
        $totalMinutes = $checkInTime->diffInMinutes($checkOutTime);
        $totalHours = $totalMinutes / 60;

        // Simple calculation without break duration (since breaks are disabled)
        $workingHours = $totalHours;

        $this->update([
            'check_out_time' => $checkOutTime,
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
            'check_out_address' => $address,
            'check_out_accuracy' => $accuracy,
            'check_out_ip' => request()->ip(),
            'check_out_user_agent' => request()->userAgent(),
            'total_hours' => round($workingHours, 2), // Round to 2 decimal places
            'status' => 'present', // Simple present status
        ]);

        // Debug log to check calculation
        \Log::info('Checkout calculation', [
            'check_in' => $checkInTime->format('Y-m-d H:i:s'),
            'check_out' => $checkOutTime->format('Y-m-d H:i:s'),
            'total_minutes' => $totalMinutes,
            'total_hours' => $workingHours,
            'rounded_hours' => round($workingHours, 2)
        ]);
    }

    public function startBreak()
    {
//        $this->update([
//            'break_start_time' => now(),
//        ]);
        return false;
    }

    public function endBreak()
    {
//        if ($this->break_start_time) {
//            $breakStart = Carbon::parse($this->break_start_time);
//            $breakEnd = now();
//            $breakDuration = $breakEnd->diffInMinutes($breakStart) / 60;
//
//            $this->update([
//                'break_end_time' => $breakEnd,
//                'break_duration' => $this->break_duration + $breakDuration,
//            ]);
//        }
        return false;
    }

    protected function determineStatus()
    {
        if (!$this->check_in_time) {
            return 'absent';
        }

//        $checkInTime = Carbon::parse($this->check_in_time);
//        $lateThreshold = Carbon::parse($this->date->format('Y-m-d') . ' 09:30:00'); // 9:30 AM threshold
//
//        if ($this->shift === 'morning' && $checkInTime->gt($lateThreshold)) {
//            return 'late';
//        }
//
//        if ($this->total_hours && $this->total_hours < 4) {
//            return 'half_day';
//        }

        return 'present';
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c; // Distance in meters
    }

    public function isWithinOfficeRadius()
    {
        $employee = $this->employee;
        $location = $employee->location;

        if (!$location || !$location->latitude || !$location->longitude || !$this->check_in_latitude || !$this->check_in_longitude) {
            return true; // Allow if location data is incomplete
        }

        $distance = $this->calculateDistance(
            $this->check_in_latitude,
            $this->check_in_longitude,
            $location->latitude,
            $location->longitude
        );

        return $distance <= $location->allowed_radius;
    }

    // Scope for filtering
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeForStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }
}