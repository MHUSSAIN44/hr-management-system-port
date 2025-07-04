<?php
// php artisan make:command UpdateLeaveBalances

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Carbon\Carbon;

class UpdateLeaveBalances extends Command
{
    protected $signature = 'leave:update-balances';
    protected $description = 'Update employee leave balances based on their work period';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting leave balance update...');

        $employees = Employee::where('status', 'active')
            ->whereNotNull('visa_start_date')
            ->get();

        $updated = 0;

        foreach ($employees as $employee) {
            $oldAnnualBalance = $employee->annual_leave_balance;
            $oldMedicalBalance = $employee->medical_leave_balance;

            // Update leave balances
            $employee->updateLeaveBalances();

            if ($oldAnnualBalance != $employee->annual_leave_balance ||
                $oldMedicalBalance != $employee->medical_leave_balance) {
                $updated++;
                $this->line("Updated {$employee->employee_name}: Annual {$oldAnnualBalance} → {$employee->annual_leave_balance}, Medical {$oldMedicalBalance} → {$employee->medical_leave_balance}");
            }
        }

        $this->info("Leave balance update completed. Updated {$updated} employees.");

        return 0;
    }
}