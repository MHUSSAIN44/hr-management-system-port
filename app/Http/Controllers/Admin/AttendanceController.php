<?php
// php artisan make:controller Admin/AttendanceController

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['employee.user', 'employee.department', 'employee.location'])
            ->orderBy('date', 'desc')
            ->orderBy('check_in_time', 'desc');

        // Apply filters with null checks
        if ($request->filled('employee')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('employee_name', 'like', '%' . $request->employee . '%');
            });
        }

        if ($request->filled('location_id')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('location_id', $request->location_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('month')) {
            $month = Carbon::parse($request->month);
            $query->whereMonth('date', $month->month)
                ->whereYear('date', $month->year);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $attendance = $query->paginate($request->get('per_page', 15));

        // Simplified statistics
        $stats = [
            'total_records' => Attendance::count(),
            'present_today' => Attendance::whereDate('date', today())->whereIn('status', ['present', 'checked_in'])->count(),
            'absent_today' => Attendance::whereDate('date', today())->where('status', 'absent')->count(),
            'checked_in_now' => Attendance::where('status', 'checked_in')->count(),
        ];

        return inertia('Admin/Attendance/Index', [
            'attendance' => $attendance,
            'stats' => $stats,
            'locations' => Location::all(['id', 'name']),
            'filters' => $request->only(['employee', 'location_id', 'status', 'shift', 'date_from', 'date_to', 'month', 'department_id', 'per_page']) ?? [],
        ]);
    }


    public function show(Attendance $attendance)
    {
        $attendance->load(['employee.user', 'employee.department', 'employee.location', 'employee.designation']);

        return inertia('Admin/Attendance/Show', [
            'attendance' => $attendance,
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'status' => 'required|in:present,absent,late,half_day',
            'notes' => 'nullable|string|max:1000',
            'total_hours' => 'nullable|numeric|min:0|max:24',
        ]);

        $attendance->update($request->only(['status', 'notes', 'total_hours']));

        return redirect()->route('admin.attendance.show', $attendance)
            ->with('success', 'Attendance record updated successfully.');
    }

    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $employees = Employee::with(['user', 'department', 'location'])
            ->where('status', 'active')
            ->get()
            ->map(function ($employee) use ($startDate, $endDate) {
                $attendanceRecords = Attendance::where('employee_id', $employee->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                return [
                    'id' => $employee->id,
                    'name' => $employee->employee_name,
                    'department' => $employee->department?->name,
                    'location' => $employee->location?->name,
                    'total_days' => $attendanceRecords->count(),
                    'present_days' => $attendanceRecords->whereIn('status', ['present', 'late'])->count(),
                    'absent_days' => $attendanceRecords->where('status', 'absent')->count(),
                    'late_days' => $attendanceRecords->where('status', 'late')->count(),
                    'half_days' => $attendanceRecords->where('status', 'half_day')->count(),
                    'total_hours' => round($attendanceRecords->sum('total_hours'), 2),
                    'average_hours' => $attendanceRecords->count() > 0 ? round($attendanceRecords->avg('total_hours'), 2) : 0,
                ];
            });

        return inertia('Admin/Attendance/Report', [
            'employees' => $employees,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'filters' => $request->only(['start_date', 'end_date']),
        ]);
    }

    public function mapView(Request $request)
    {
        $query = Attendance::with(['employee.user', 'employee.location'])
            ->whereNotNull('check_in_latitude')
            ->whereNotNull('check_in_longitude');

        // Apply filters
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', today());
        }

        if ($request->filled('location_id')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('location_id', $request->location_id);
            });
        }

        $attendance = $query->get()->map(function ($record) {
            return [
                'id' => $record->id,
                'employee_name' => $record->employee->employee_name,
                'check_in_time' => $record->check_in_time?->format('H:i'),
               'check_out_time' => $record->check_out_time?->format('H:i'),
               'status' => $record->status,
               'shift' => $record->shift,
               'check_in_location' => [
                'lat' => (float) $record->check_in_latitude,
                'lng' => (float) $record->check_in_longitude,
                'address' => $record->check_in_address,
            ],
               'check_out_location' => $record->check_out_latitude && $record->check_out_longitude ? [
                'lat' => (float) $record->check_out_latitude,
                'lng' => (float) $record->check_out_longitude,
                'address' => $record->check_out_address,
            ] : null,
               'office_location' => $record->employee->location && $record->employee->location->hasCoordinates() ? [
                'lat' => (float) $record->employee->location->latitude,
                'lng' => (float) $record->employee->location->longitude,
                'name' => $record->employee->location->name,
                'radius' => $record->employee->location->allowed_radius,
            ] : null,
           ];
       });

        $locations = Location::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id', 'name', 'latitude', 'longitude', 'allowed_radius']);

        return inertia('Admin/Attendance/MapView', [
            'attendance' => $attendance,
            'locations' => $locations,
            'filters' => $request->only(['date', 'location_id']),
        ]);
    }

    public function liveTracking()
    {
        $activeAttendance = Attendance::with(['employee.user', 'employee.location'])
            ->where('status', 'checked_in')
            ->whereDate('date', today())
            ->whereNotNull('check_in_latitude')
            ->whereNotNull('check_in_longitude')
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'employee_name' => $record->employee->employee_name,
                    'check_in_time' => $record->check_in_time->format('H:i'),
                    'duration' => $record->check_in_time->diffForHumans(null, true),
                    'location' => [
                        'lat' => (float) $record->check_in_latitude,
                        'lng' => (float) $record->check_in_longitude,
                        'address' => $record->check_in_address,
                    ],
                    'office_location' => $record->employee->location && $record->employee->location->hasCoordinates() ? [
                        'lat' => (float) $record->employee->location->latitude,
                        'lng' => (float) $record->employee->location->longitude,
                        'name' => $record->employee->location->name,
                        'radius' => $record->employee->location->allowed_radius,
                    ] : null,
                ];
            });

        return inertia('Admin/Attendance/LiveTracking', [
            'activeAttendance' => $activeAttendance,
        ]);
    }
}