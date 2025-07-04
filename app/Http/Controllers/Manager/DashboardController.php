<?php
// php artisan make:controller Manager/DashboardController

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\OvertimeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $manager = Auth::user();

        // Get manager's location and team
        $managerEmployee = Employee::where('user_id', $manager->id)->first();
        $managedLocation = $manager->managedLocation;
        $teamMembers = $manager->managedEmployees()->with(['user', 'department', 'designation', 'facility'])->get();

        // Dashboard statistics
        $stats = [
            'total_team_members' => $teamMembers->count(),
            'active_team_members' => $teamMembers->where('status', 'active')->count(),
            'pending_leave_requests' => LeaveRequest::whereHas('employee', function($q) use ($manager) {
                $q->where('reporting_manager_id', $manager->id);
            })->where('manager_status', 'pending')->count(),
            'overtime_requests' => OvertimeRequest::where('requested_by', $manager->id)->where('status', 'pending')->count(),
        ];

        // Recent leave requests needing approval
        $pendingLeaves = LeaveRequest::with(['employee.user'])
            ->whereHas('employee', function($q) use ($manager) {
                $q->where('reporting_manager_id', $manager->id);
            })
            ->where('manager_status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Today's attendance for team
        $todayAttendance = Attendance::with(['employee.user'])
            ->whereHas('employee', function($q) use ($manager) {
                $q->where('reporting_manager_id', $manager->id);
            })
            ->whereDate('date', today())
            ->get();

        // Recent team activities
        $recentActivities = [
            'new_team_members' => $teamMembers->where('created_at', '>', Carbon::now()->subDays(7)),
            'recent_leave_requests' => LeaveRequest::with(['employee.user'])
                ->whereHas('employee', function($q) use ($manager) {
                    $q->where('reporting_manager_id', $manager->id);
                })
                ->latest()
                ->take(3)
                ->get(),
        ];

        return inertia('Manager/Dashboard', [
            'stats' => $stats,
            'teamMembers' => $teamMembers,
            'pendingLeaves' => $pendingLeaves,
            'todayAttendance' => $todayAttendance,
            'recentActivities' => $recentActivities,
            'managedLocation' => $managedLocation,
            'managerEmployee' => $managerEmployee,
        ]);
    }

    public function team(Request $request)
    {
        $manager = Auth::user();

        $query = $manager->managedEmployees()
            ->with(['user', 'department', 'designation', 'facility', 'location']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('email_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $teamMembers = $query->paginate($perPage);

        // Get unique departments and locations for filters
        $departments = Department::whereHas('employees', function($q) use ($manager) {
            $q->where('reporting_manager_id', $manager->id);
        })->get();

        $locations = Location::whereHas('employees', function($q) use ($manager) {
            $q->where('reporting_manager_id', $manager->id);
        })->get();

        return inertia('Manager/Team/Index', [
            'employees' => $teamMembers,
            'departments' => $departments,
            'locations' => $locations,
            'statuses' => ['active', 'inactive', 'terminated'],
            'filters' => $request->only(['search', 'department', 'location', 'status', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function teamMember(Employee $employee)
    {
        // Ensure this employee reports to the current manager
        if ($employee->reporting_manager_id !== Auth::id()) {
            abort(403, 'Unauthorized access to employee data.');
        }

        $employee->load([
            'user', 'department', 'designation', 'facility', 'location',
            'documents', 'assets', 'leaveRequests' => function($query) {
                $query->latest()->take(5);
            }
        ]);

        return inertia('Manager/Team/Show', [
            'employee' => $employee,
        ]);
    }
}