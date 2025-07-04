<?php
// php artisan make:controller Admin/DashboardController

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Location;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'total_departments' => Department::count(),
            'total_locations' => Location::count(),
            'pending_leave_requests' => LeaveRequest::where('overall_status', 'pending')->count(),
            'reporting_managers' => User::where('role', 'reporting_manager')->count(),
        ];

        $recent_employees = Employee::with(['user', 'department', 'location'])
            ->latest()
            ->take(5)
            ->get();

        $pending_leaves = LeaveRequest::with(['employee.user'])
            ->where('overall_status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return inertia('Admin/Dashboard', [
            'stats' => $stats,
            'recent_employees' => $recent_employees,
            'pending_leaves' => $pending_leaves,
        ]);
    }
}