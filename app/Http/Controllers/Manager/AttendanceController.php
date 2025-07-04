<?php
// php artisan make:controller Manager/AttendanceController

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\AttendanceSettings;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth()->user()->employee;

        $query = Attendance::with(['employee.user', 'employee.location'])
            ->where('employee_id', $employee->id)
            ->orderBy('date', 'desc');

        // Date filtering
        if ($request->filled('month')) {
            $month = Carbon::parse($request->month);
            $query->whereMonth('date', $month->month)
                ->whereYear('date', $month->year);
        } else {
            // Default to current month
            $query->whereMonth('date', now()->month)
                ->whereYear('date', now()->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        $attendance = $query->paginate($request->get('per_page', 15));

        // Today's attendance
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->first();

        // Monthly stats
        $monthlyStats = [
            'total_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
            'present_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->whereIn('status', ['present', 'late'])
                ->count(),
            'absent_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('status', 'absent')
                ->count(),
            'late_days' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('status', 'late')
                ->count(),
            'total_hours' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('total_hours'),
        ];

        return inertia('Manager/Attendance/Index', [
            'attendance' => $attendance,
            'todayAttendance' => $todayAttendance,
            'monthlyStats' => $monthlyStats,
            'employee' => $employee->load('location'),
            'filters' => $request->only(['month', 'status', 'shift', 'per_page']),
        ]);
    }


// Updated Employee Attendance Controller checkIn method

    public function checkIn(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'accuracy' => 'required|numeric|min:0',
                'address' => 'required|string|max:500',
                'shift' => 'required|in:morning,evening,full-time',
            ]);

            $employee = auth()->user()->employee;

            // Check if employee record exists
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found. Please contact admin.',
                    'errors' => ['employee' => ['Employee record not found.']]
                ], 422);
            }

            // Check if already checked in today for this shift
//            $existingAttendance = Attendance::where('employee_id', $employee->id)
//                ->whereDate('date', today())
//                ->where('shift', $request->shift)
//                ->first();
//
//            if ($existingAttendance && $existingAttendance->check_in_time) {
//                return response()->json([
//                    'success' => false,
//                    'message' => 'You have already checked in for ' . $request->shift . ' shift today.',
//                    'errors' => ['checkin' => ['Already checked in for this shift today.']]
//                ], 422);
//            }

            // Location validation (if enabled)
            if ($employee->location && $employee->location->isLocationValidationEnabled()) {
                if (!$employee->location->latitude || !$employee->location->longitude) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Office location coordinates not configured. Please contact admin.',
                        'errors' => ['location' => ['Office location not properly configured.']]
                    ], 422);
                }

                $distance = $this->calculateDistance(
                    $request->latitude,
                    $request->longitude,
                    $employee->location->latitude,
                    $employee->location->longitude
                );

                if ($distance > $employee->location->allowed_radius) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are outside your assigned office location. Please reach your office to check in.',
                        'errors' => ['location' => ['Outside office location radius.']]
                    ], 422);
                }
            }

            // Validate location accuracy
            $minAccuracy = AttendanceSettings::get('min_location_accuracy', 50);
//            $maxAccuracy = AttendanceSettings::get('max_location_accuracy', 200); // Add this setting
//
//            if ($request->accuracy > $maxAccuracy) {
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Location accuracy is too poor (' . round($request->accuracy) . 'm). Please go outside or enable high accuracy GPS.',
//                    'errors' => ['accuracy' => ['Location accuracy too low. low']]
//                ], 422);
//            } elseif ($request->accuracy > $minAccuracy) {
//                // Log warning but allow check-in
//                \Log::warning('Check-in with low accuracy', [
//                    'employee_id' => $employee->id,
//                    'accuracy' => $request->accuracy,
//                    'threshold' => $minAccuracy
//                ]);
//            }

            // Create or update attendance record
            $attendance = Attendance::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date' => today(),
                    'shift' => $request->shift,
                ],
                []
            );

            // Perform check-in
            $attendance->checkIn(
                $request->latitude,
                $request->longitude,
                $request->address,
                $request->accuracy,
                $request->shift
            );

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Checked in successfully!',
                'data' => [
                    'attendance' => $attendance->fresh(),
                    'check_in_time' => $attendance->check_in_time->format('h:i A'),
                    'location' => $request->address,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Check-in failed', [
                'employee_id' => auth()->user()->employee->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again or contact support.',
                'errors' => ['system' => ['System error occurred.']]
            ], 500);
        }
    }


    public function checkOut(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'accuracy' => 'required|numeric|min:0',
                'address' => 'required|string|max:500',
                'notes' => 'nullable|string|max:1000',
            ]);

            $employee = auth()->user()->employee;

            // Check if employee record exists
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found. Please contact admin.',
                    'errors' => ['employee' => ['Employee record not found.']]
                ], 422);
            }

            // Find today's attendance record that's checked in
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', today())
                ->where('status', 'checked_in')
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active check-in found for today. Please check in first.',
                    'errors' => ['checkout' => ['No active check-in found.']]
                ], 422);
            }

            // Validate minimum work duration
            $minWorkDuration = AttendanceSettings::get('min_work_duration_minutes', 30);
            $workDuration = now()->diffInMinutes($attendance->check_in_time);

//            if ($workDuration < $minWorkDuration) {
//                return response()->json([
//                    'success' => false,
//                    'message' => "Minimum work duration is {$minWorkDuration} minutes. You have worked only {$workDuration} minutes.",
//                    'errors' => ['duration' => ['Minimum work duration not met.']]
//                ], 422);
//            }

            // Validate location accuracy
//            $minAccuracy = AttendanceSettings::get('min_location_accuracy', 100);
//            if ($request->accuracy > $minAccuracy) {
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Location accuracy is not sufficient for checkout. Please enable high accuracy location and try again.',
//                    'errors' => ['accuracy' => ['Location accuracy too low for checkout.']]
//                ], 422);
//            }

            // Perform checkout
            $attendance->checkOut(
                $request->latitude,
                $request->longitude,
                $request->address,
                $request->accuracy
            );

            // Add notes if provided
            if ($request->notes) {
                $attendance->update(['notes' => $request->notes]);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Checked out successfully!',
                'data' => [
                    'attendance' => $attendance->fresh(),
                    'check_out_time' => $attendance->check_out_time->format('h:i A'),
                    'total_hours' => round($attendance->total_hours, 2),
                    'work_duration' => $workDuration . ' minutes',
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Check-out failed', [
                'employee_id' => auth()->user()->employee->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred during checkout. Please try again or contact support.',
                'errors' => ['system' => ['System error occurred.']]
            ], 500);
        }
    }

    public function startBreak(Request $request)
    {
        $employee = auth()->user()->employee;

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->where('status', 'checked_in')
            ->first();

        if (!$attendance) {
            throw ValidationException::withMessages([
                'message' => 'You must be checked in to start a break.'
            ]);
        }

        if ($attendance->break_start_time && !$attendance->break_end_time) {
            throw ValidationException::withMessages([
                'message' => 'Break is already in progress.'
            ]);
        }

        $attendance->startBreak();

        return response()->json([
            'message' => 'Break started!',
            'break_start_time' => $attendance->break_start_time->format('h:i A'),
        ]);
    }

    public function endBreak(Request $request)
    {
        $employee = auth()->user()->employee;

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->where('status', 'checked_in')
            ->whereNotNull('break_start_time')
            ->whereNull('break_end_time')
            ->first();

        if (!$attendance) {
            throw ValidationException::withMessages([
                'message' => 'No active break found.'
            ]);
        }

        $attendance->endBreak();

        return response()->json([
            'message' => 'Break ended!',
            'break_duration' => round($attendance->break_duration, 2) . ' hours',
        ]);
    }

    public function show(Attendance $attendance)
    {
        $employee = auth()->user()->employee;

        if ($attendance->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to attendance record.');
        }

        $attendance->load(['employee.user', 'employee.location']);

        return inertia('Manager/Attendance/Show', [
            'attendance' => $attendance,
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c; // Distance in meters
    }

}