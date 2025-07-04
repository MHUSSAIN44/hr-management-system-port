<?php
// php artisan make:controller Manager/LeaveController

namespace App\Http\Controllers\Manager;

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
        $user = auth()->user();

        // Get leave requests from team members
        $query = LeaveRequest::with(['employee.user', 'employee.department', 'employee.designation'])
            ->whereHas('employee', function($q) use ($user) {
                $q->where('reporting_manager_id', $user->id);
            })
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('manager_status', $request->status);
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        $leaves = $query->paginate($request->get('per_page', 15));

        // Statistics
        $stats = [
            'total' => LeaveRequest::whereHas('employee', function($q) use ($user) {
                $q->where('reporting_manager_id', $user->id);
            })->count(),
            'pending' => LeaveRequest::whereHas('employee', function($q) use ($user) {
                $q->where('reporting_manager_id', $user->id);
            })->where('manager_status', 'pending')->count(),
            'approved' => LeaveRequest::whereHas('employee', function($q) use ($user) {
                $q->where('reporting_manager_id', $user->id);
            })->where('manager_status', 'approved')->count(),
            'rejected' => LeaveRequest::whereHas('employee', function($q) use ($user) {
                $q->where('reporting_manager_id', $user->id);
            })->where('manager_status', 'rejected')->count(),
        ];

        return Inertia::render('Manager/Leave/Index', [
            'leaves' => $leaves,
            'stats' => $stats,
            'filters' => $request->only(['status', 'leave_type', 'per_page'])
        ]);
    }

    public function approve(Request $request, LeaveRequest $leave)
    {
        // Verify this leave belongs to manager's team
        if ($leave->employee->reporting_manager_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'comments' => 'nullable|string|max:1000'
        ]);

        // Check if employee has sufficient leave balance
        if (!$this->validateLeaveBalance($leave)) {
            return back()->withErrors(['message' => 'Employee does not have sufficient leave balance.']);
        }

        $leave->approveByManager($request->comments);

        // If this leave request needs admin approval, update admin status to pending
        if ($leave->leave_type !== 'emergency') {
            $leave->update(['admin_status' => 'pending']);
        } else {
            // Emergency leaves are auto-approved by admin after manager approval
            $leave->update([
                'admin_status' => 'approved',
                'admin_approved_at' => now(),
                'overall_status' => 'approved'
            ]);
        }

        return redirect()->route('manager.leave.index')
            ->with('success', 'Leave request approved successfully.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        // Verify this leave belongs to manager's team
        if ($leave->employee->reporting_manager_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'comments' => 'required|string|max:1000'
        ]);

        $leave->rejectByManager($request->comments);

        return redirect()->route('manager.leave.index')
            ->with('success', 'Leave request rejected.');
    }

    public function myLeave(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('manager.dashboard')
                ->withErrors(['message' => 'Employee profile not found.']);
        }

        $query = LeaveRequest::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc');

        $leaves = $query->paginate($request->get('per_page', 15));

        // Leave balance information
        $leaveBalance = [
            'annual_accrued' => $employee->calculateAnnualLeaveAccrual(),
            'annual_used' => $employee->getUsedAnnualLeave(),
            'annual_available' => $employee->getAvailableAnnualLeave(),
            'medical_used' => $employee->getUsedMedicalLeave(),
            'medical_available' => $employee->getAvailableMedicalLeave(),
            'can_apply_annual' => $employee->canApplyAnnualLeave(),
        ];

        return Inertia::render('Manager/Leave/MyLeave', [
            'leaves' => $leaves,
            'leaveBalance' => $leaveBalance,
            'employee' => $employee
        ]);
    }

    public function storeMyLeave(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->withErrors(['message' => 'Employee profile not found.']);
        }

        $request->validate([
            'leave_type' => 'required|in:annual,medical,emergency',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        // Additional validation based on leave type
        $this->validateLeaveRequest($request, $employee);

        // Calculate days
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $days = $startDate->diffInDays($endDate) + 1;

        // Create leave request - Managers' leaves go directly to admin
        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_requested' => $days,
            'reason' => $request->reason,
            'manager_status' => 'approved', // Auto-approved for managers
            'manager_approved_at' => now(),
            'admin_status' => 'pending',
            'overall_status' => 'pending',
        ]);

        return redirect()->route('manager.leave.my')
            ->with('success', 'Leave request submitted successfully.');
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
                return true;
            default:
                return false;
        }
    }

    private function validateLeaveRequest(Request $request, Employee $employee)
    {
        $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;

        switch ($request->leave_type) {
            case 'annual':
                if (!$employee->canApplyAnnualLeave()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'leave_type' => 'You can apply for annual leave only after 30 days from your visa start date.'
                    ]);
                }

                if ($employee->getAvailableAnnualLeave() < $days) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'days' => "You only have {$employee->getAvailableAnnualLeave()} annual leave days available."
                    ]);
                }
                break;

            case 'medical':
                if ($employee->getAvailableMedicalLeave() < $days) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'days' => "You only have {$employee->getAvailableMedicalLeave()} medical leave days available."
                    ]);
                }
                break;

            case 'emergency':
                // Emergency leave can be applied from current date onward
                if (Carbon::parse($request->start_date)->lt(Carbon::today())) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'start_date' => 'Emergency leave can only be applied from today onwards.'
                    ]);
                }
                break;
        }
    }
}