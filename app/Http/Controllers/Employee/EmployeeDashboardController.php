<?php
// php artisan make:controller Employee/DashboardController

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\Document;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)
            ->with(['department', 'designation', 'facility', 'location', 'reportingManager'])
            ->first();

        if (!$employee) {
            abort(404, 'Employee profile not found.');
        }

        // Dashboard statistics
        $stats = [
            'annual_leave_balance' => $employee->annual_leave_balance,
            'medical_leave_balance' => $employee->medical_leave_balance,
            'pending_leave_requests' => LeaveRequest::where('employee_id', $employee->id)
                ->where('overall_status', 'pending')
                ->count(),
            'documents_expiring_soon' => Document::where('employee_id', $employee->id)
                ->where('expiry_date', '<=', Carbon::now()->addDays(30))
                ->where('expiry_date', '>=', Carbon::now())
                ->count(),
        ];

        // Recent leave requests
        $recentLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->latest()
            ->take(5)
            ->get();

        // This month's attendance
        $thisMonthAttendance = Attendance::where('employee_id', $employee->id)
            ->whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->get();

        // Recent payroll
        $recentPayroll = Payroll::where('employee_id', $employee->id)
            ->latest()
            ->take(3)
            ->get();

        // Upcoming document expirations
        $expiringDocuments = Document::where('employee_id', $employee->id)
            ->where('expiry_date', '<=', Carbon::now()->addDays(60))
            ->where('expiry_date', '>=', Carbon::now())
            ->orderBy('expiry_date')
            ->get();

        // Today's attendance status
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->first();

        return inertia('Employee/Dashboard', [
            'employee' => $employee,
            'stats' => $stats,
            'recentLeaves' => $recentLeaves,
            'thisMonthAttendance' => $thisMonthAttendance,
            'recentPayroll' => $recentPayroll,
            'expiringDocuments' => $expiringDocuments,
            'todayAttendance' => $todayAttendance,
        ]);
    }
}