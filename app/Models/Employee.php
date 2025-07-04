<?php
// php artisan make:model Employee

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'employee_name',
        'department_id',
        'designation_id',
        'facility_id',
        'status',
        'driving_license_expiry',
        'covid_vaccinated',
        'location_id',
        'contact',
        'whatsapp_number',
        'company_first_paid_working_day',
        'nationality',
        'passport_number',
        'passport_expiry',
        'passport_file',
        'visa_number',
        'visa_start_date',
        'visa_file',
        'visa_expiry_date',
        'insurance_issued_on',
        'insurance_expiry_date',
        'eid_number',
        'eid_expiry_date',
        'date_of_birth',
        'malpractice_expiry_date',
        'last_working_day',
        'email_address',
        'reporting_manager_id',
        'annual_leave_balance',
        'medical_leave_balance',
    ];

    protected $casts = [
        'driving_license_expiry' => 'date',
        'covid_vaccinated' => 'boolean',
        'company_first_paid_working_day' => 'date',
        'passport_expiry' => 'date',
        'visa_start_date' => 'date',
        'visa_expiry_date' => 'date',
        'insurance_issued_on' => 'date',
        'insurance_expiry_date' => 'date',
        'eid_expiry_date' => 'date',
        'date_of_birth' => 'date',
        'malpractice_expiry_date' => 'date',
        'last_working_day' => 'date',
        'annual_leave_balance' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function reportingManager()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'assigned_to');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payroll()
    {
        return $this->hasMany(Payroll::class);
    }

    // Helper methods
    public function calculateAnnualLeaveAccrual()
    {
        if (!$this->visa_start_date) {
            return 0;
        }

        $startDate = Carbon::parse($this->visa_start_date);
        $currentDate = Carbon::now();

        // Calculate months worked (including partial months)
        $monthsWorked = $startDate->diffInMonths($currentDate);

        // Add partial month if there are remaining days
        if ($startDate->copy()->addMonths($monthsWorked)->lt($currentDate)) {
            $daysInPartialMonth = $startDate->copy()->addMonths($monthsWorked)->diffInDays($currentDate);
            $partialMonth = $daysInPartialMonth / 30; // Approximate days in month
            $monthsWorked += $partialMonth;
        }

        return round($monthsWorked * 2.5, 2);
    }

    /**
     * Check if employee can apply for annual leave (30 days after visa start)
     */
    public function canApplyAnnualLeave()
    {
        if (!$this->visa_start_date) {
            return false;
        }

        return Carbon::parse($this->visa_start_date)->addDays(30)->isPast();
    }

    /**
     * Get available annual leave balance
     */
    public function getAvailableAnnualLeave()
    {
        $accrued = $this->calculateAnnualLeaveAccrual();
        return max(0, $accrued - $this->getUsedAnnualLeave());
    }

    /**
     * Get used annual leave for current year
     */
    public function getUsedAnnualLeave()
    {
        if (!$this->visa_start_date) {
            return 0;
        }

        $startDate = Carbon::parse($this->visa_start_date);
        $yearStart = $startDate->copy();

        // If visa started last year, use this year's start
        if ($startDate->year < Carbon::now()->year) {
            $yearStart = Carbon::create(Carbon::now()->year, $startDate->month, $startDate->day);
        }

        return $this->leaveRequests()
            ->where('leave_type', 'annual')
            ->where('overall_status', 'approved')
            ->where('start_date', '>=', $yearStart)
            ->sum('days_requested');
    }

    /**
     * Get available medical leave balance
     */
    public function getAvailableMedicalLeave()
    {
        return max(0, 14 - $this->getUsedMedicalLeave());
    }

    /**
     * Get used medical leave for current year
     */
    public function getUsedMedicalLeave()
    {
        if (!$this->visa_start_date) {
            return 0;
        }

        $startDate = Carbon::parse($this->visa_start_date);
        $yearStart = $startDate->copy();

        // If visa started last year, use this year's start
        if ($startDate->year < Carbon::now()->year) {
            $yearStart = Carbon::create(Carbon::now()->year, $startDate->month, $startDate->day);
        }

        return $this->leaveRequests()
            ->where('leave_type', 'medical')
            ->where('overall_status', 'approved')
            ->where('start_date', '>=', $yearStart)
            ->sum('days_requested');
    }

    /**
     * Update leave balances (to be called monthly via cron job)
     */
    public function updateLeaveBalances()
    {
        $this->annual_leave_balance = $this->getAvailableAnnualLeave();
        $this->medical_leave_balance = $this->getAvailableMedicalLeave();
        $this->save();
    }

    /**
     * Reset medical leave balance annually
     */
    public function resetMedicalLeaveBalance()
    {
        $this->update(['medical_leave_balance' => 14]);
    }
}