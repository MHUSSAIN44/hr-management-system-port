<?php
// php artisan make:controller Admin/LeaveController

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['employee.user', 'employee.department', 'employee.designation'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('overall_status', $request->status);
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        if ($request->filled('employee')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('employee_name', 'like', '%' . $request->employee . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $leaves = $query->paginate($request->get('per_page', 15));

        // Statistics
        $stats = [
            'total' => LeaveRequest::count(),
            'pending' => LeaveRequest::where('overall_status', 'pending')->count(),
            'approved' => LeaveRequest::where('overall_status', 'approved')->count(),
            'rejected' => LeaveRequest::where('overall_status', 'rejected')->count(),
            'pending_admin_approval' => LeaveRequest::where('manager_status', 'approved')
                ->where('admin_status', 'pending')->count(),
        ];

        return Inertia::render('Admin/Leave/Index', [
            'leaves' => $leaves,
            'stats' => $stats,
            'filters' => $request->only(['status', 'leave_type', 'employee', 'date_from', 'date_to', 'per_page'])
        ]);
    }

    public function show(LeaveRequest $leave)
    {
        $leave->load(['employee.user', 'employee.department', 'employee.designation', 'employee.location']);

        return Inertia::render('Admin/Leave/Show', [
            'leave' => $leave
        ]);
    }

    public function approve(Request $request, LeaveRequest $leave)
    {
        $request->validate([
            'comments' => 'nullable|string|max:1000'
        ]);

        // Check if employee has sufficient leave balance
        if (!$this->validateLeaveBalance($leave)) {
            return back()->withErrors(['message' => 'Employee does not have sufficient leave balance.']);
        }

        $leave->approveByAdmin($request->comments);

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave request approved successfully.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $request->validate([
            'comments' => 'required|string|max:1000'
        ]);

        $leave->rejectByAdmin($request->comments);

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave request rejected.');
    }

    public function report(Request $request)
    {
        $employees = Employee::with(['user', 'department', 'designation'])
            ->where('status', 'active')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->employee_name,
                    'department' => $employee->department?->name,
                    'designation' => $employee->designation?->name,
                    'visa_start_date' => $employee->visa_start_date,
                    'annual_leave_accrued' => $employee->calculateAnnualLeaveAccrual(),
                    'annual_leave_used' => $employee->getUsedAnnualLeave(),
                    'annual_leave_balance' => $employee->getAvailableAnnualLeave(),
                    'medical_leave_used' => $employee->getUsedMedicalLeave(),
                    'medical_leave_balance' => $employee->getAvailableMedicalLeave(),
                    'can_apply_annual' => $employee->canApplyAnnualLeave(),
                ];
            });

        return Inertia::render('Admin/Leave/Report', [
            'employees' => $employees
        ]);
    }

    public function updateLeaveBalance(Request $request, Employee $employee)
    {
        $request->validate([
            'annual_leave_balance' => 'required|numeric|min:0|max:50',
            'medical_leave_balance' => 'required|numeric|min:0|max:14',
            'reason' => 'required|string|max:500'
        ]);

        $employee->update([
            'annual_leave_balance' => $request->annual_leave_balance,
            'medical_leave_balance' => $request->medical_leave_balance,
        ]);

        return back()->with('success', 'Leave balance updated successfully.');
    }

    private function validateLeaveBalance(LeaveRequest $leave)
    {
        $employee = $leave->employee;

        switch ($leave->leave_type) {
            case 'annual':
                return $employee->getAvailableAnnualLeave() >= $leave->days_requested;
            case 'medical':
                return $employee->getAvailableMedicalLeave() >= $leave->days_requested;
            case 'emergency':
                return true; // Emergency leave doesn't require balance check
            default:
                return false;
        }
    }
}