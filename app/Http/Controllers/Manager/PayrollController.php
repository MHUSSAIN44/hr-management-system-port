<?php
// php artisan make:controller Manager/PayrollController

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            abort(404, 'Employee profile not found.');
        }

        $query = Payroll::where('employee_id', $employee->id)
            ->with(['employee.user', 'employee.department', 'employee.designation']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('month', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Sorting
        $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('created_at', 'desc');

        $payrolls = $query->paginate($request->get('per_page', 15))->withQueryString();

        // Get filter options from manager's payroll records
        $years = Payroll::where('employee_id', $employee->id)
            ->distinct()
            ->pluck('year')
            ->sort()
            ->reverse()
            ->values();

        $months = collect(range(1, 12))->map(function ($month) {
            return [
                'value' => $month,
                'label' => Carbon::create()->month($month)->format('F')
            ];
        });

        // Statistics for current manager
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $stats = [
            'total_payrolls' => Payroll::where('employee_id', $employee->id)->count(),
            'current_month_payroll' => Payroll::where('employee_id', $employee->id)
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->first(),
            'current_year_earnings' => Payroll::where('employee_id', $employee->id)
                ->where('year', $currentYear)
                ->sum('net_salary'),
            'average_monthly_salary' => Payroll::where('employee_id', $employee->id)
                ->avg('net_salary'),
            'ytd_earnings' => Payroll::where('employee_id', $employee->id)
                ->where('year', $currentYear)
                ->sum('net_salary'),
            'pending_payments' => Payroll::where('employee_id', $employee->id)
                ->where('status', 'pending')
                ->count(),
        ];

        return inertia('Manager/Payroll/Index', [
            'payrolls' => $payrolls,
            'employee' => $employee,
            'years' => $years,
            'months' => $months,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'month', 'year', 'per_page']),
        ]);
    }

    public function show(Payroll $payroll)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        // Ensure the payroll belongs to the authenticated manager
        if ($payroll->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to payroll data.');
        }

        $payroll->load(['employee.user', 'employee.department', 'employee.designation', 'employee.location']);

        return inertia('Manager/Payroll/Show', [
            'payroll' => $payroll,
        ]);
    }

    public function payslip(Payroll $payroll)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        // Ensure the payroll belongs to the authenticated manager
        if ($payroll->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to payroll data.');
        }

        $payroll->load(['employee.user', 'employee.department', 'employee.designation', 'employee.location']);

        return inertia('Manager/Payroll/Payslip', [
            'payroll' => $payroll,
        ]);
    }
}