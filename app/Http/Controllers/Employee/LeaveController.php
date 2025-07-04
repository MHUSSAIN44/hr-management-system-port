<?php
// php artisan make:controller Employee/LeaveController

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('employee.dashboard')
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

        return Inertia::render('Employee/Leave/Index', [
            'leaves' => $leaves,
            'leaveBalance' => $leaveBalance
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('employee.dashboard')
                ->withErrors(['message' => 'Employee profile not found.']);
        }

        // Leave balance information
        $leaveBalance = [
            'annual_accrued' => $employee->calculateAnnualLeaveAccrual(),
            'annual_used' => $employee->getUsedAnnualLeave(),
            'annual_available' => $employee->getAvailableAnnualLeave(),
            'medical_used' => $employee->getUsedMedicalLeave(),
            'medical_available' => $employee->getAvailableMedicalLeave(),
            'can_apply_annual' => $employee->canApplyAnnualLeave(),
        ];

        return Inertia::render('Employee/Leave/Create', [
            'leaveBalance' => $leaveBalance,
            'employee' => $employee
        ]);
    }

    public function store(Request $request)
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

        // Create leave request
        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_requested' => $days,
            'reason' => $request->reason,
        ]);

        return redirect()->route('employee.leave.index')
            ->with('success', 'Leave request submitted successfully.');
    }

    public function show(LeaveRequest $leave)
    {
        // Verify this leave belongs to the authenticated employee
        if ($leave->employee_id !== auth()->user()->employee->id) {
            abort(403, 'Unauthorized');
        }

        $leave->load(['employee.user', 'employee.department', 'employee.designation']);

        return Inertia::render('Employee/Leave/Show', [
            'leave' => $leave
        ]);
    }

    private function validateLeaveRequest(Request $request, $employee)
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