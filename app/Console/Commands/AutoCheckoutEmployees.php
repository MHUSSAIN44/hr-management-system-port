<?php
// Updated AutoCheckoutEmployees Command - 12 hours after check-in

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\AttendanceSettings;
use Carbon\Carbon;

class AutoCheckoutEmployees extends Command
{
    protected $signature = 'attendance:auto-checkout';
    protected $description = 'Automatically checkout employees 12 hours after check-in (DISABLED)';

    public function handle()
    {
        $enabled = AttendanceSettings::get('auto_checkout_enabled', false);

        if (!$enabled) {
            $this->info('Auto checkout is disabled.');
            return;
        }

        $hoursAfterCheckin = AttendanceSettings::get('auto_checkout_hours_after_checkin', 12);

        // Find attendance records that are checked in and have been so for more than X hours
        $cutoffTime = now()->subHours($hoursAfterCheckin);

        $activeAttendance = Attendance::where('status', 'checked_in')
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->where('check_in_time', '<=', $cutoffTime)
            ->with('employee')
            ->get();

        $checkedOutCount = 0;

        foreach ($activeAttendance as $attendance) {
            // Auto checkout with system location
            $attendance->checkOut(
                0, // lat
                0, // lng
                'System Auto Checkout', // address
                0 // accuracy
            );

            $attendance->update([
                'notes' => "Automatically checked out by system {$hoursAfterCheckin} hours after check-in"
            ]);
            $checkedOutCount++;

            $this->info("Auto checked out: {$attendance->employee->employee_name} (checked in for {$hoursAfterCheckin}+ hours)");
        }

        $this->info("Auto checkout completed. {$checkedOutCount} employees checked out.");
    }
}