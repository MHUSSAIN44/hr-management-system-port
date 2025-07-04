<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\Employee\PayrollController as EmployeePayrollController;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\Manager\ProfileController as ManagerProfileController;


use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\AssetController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication routes (provided by Laravel Breeze)
require __DIR__.'/auth.php';


Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isManager()) {
        return redirect()->route('manager.dashboard');
    } else {
        return redirect()->route('employee.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Employee Management
    Route::resource('employees', EmployeeController::class);
    Route::post('/employees/{employee}/promote', [EmployeeController::class, 'promoteToManager'])->name('employees.promote');
    Route::post('/employees/{employee}/demote', [EmployeeController::class, 'demoteToEmployee'])->name('employees.demote');

    // Master Data Management
    Route::resource('departments', DepartmentController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('designations', DesignationController::class);
    Route::resource('facilities', FacilityController::class);
    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::resource('assets', AssetController::class);
    Route::post('/assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
    Route::post('/assets/{asset}/unassign', [AssetController::class, 'unassign'])->name('assets.unassign');

    // Attendance management
    Route::get('/attendance', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/map', [App\Http\Controllers\Admin\AttendanceController::class, 'mapView'])->name('attendance.map');
    Route::get('/attendance/live-tracking', [App\Http\Controllers\Admin\AttendanceController::class, 'liveTracking'])->name('attendance.live-tracking');
    Route::get('/attendance/report', [App\Http\Controllers\Admin\AttendanceController::class, 'report'])->name('attendance.report');
    Route::get('/attendance/{attendance}', [App\Http\Controllers\Admin\AttendanceController::class, 'show'])->name('attendance.show');
    Route::patch('/attendance/{attendance}', [App\Http\Controllers\Admin\AttendanceController::class, 'update'])->name('attendance.update');

    // Leave management
    // Add these routes to your web.php file under the admin group

// Leave Management Routes
    Route::get('/leave', [App\Http\Controllers\Admin\LeaveController::class, 'index'])->name('leave.index');
    Route::get('/leave/report', [App\Http\Controllers\Admin\LeaveController::class, 'report'])->name('leave.report');
    Route::get('/leave/{leave}', [App\Http\Controllers\Admin\LeaveController::class, 'show'])->name('leave.show');
    Route::post('/leave/{leave}/approve', [App\Http\Controllers\Admin\LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{leave}/reject', [App\Http\Controllers\Admin\LeaveController::class, 'reject'])->name('leave.reject');
    Route::post('/employees/{employee}/update-leave-balance', [App\Http\Controllers\Admin\LeaveController::class, 'updateLeaveBalance'])->name('employees.update-leave-balance');


    // Payroll Management Routes
    Route::resource('payroll', App\Http\Controllers\Admin\PayrollController::class);
    Route::get('/payroll/{payroll}/generate-payslip', [App\Http\Controllers\Admin\PayrollController::class, 'generatePayslip'])->name('payroll.generate-payslip');
    Route::post('/payroll/generate-payrolls', [App\Http\Controllers\Admin\PayrollController::class, 'generatePayrolls'])->name('payroll.generate-payrolls');
    Route::post('/payroll/{payroll}/mark-as-paid', [App\Http\Controllers\Admin\PayrollController::class, 'markAsPaid'])->name('payroll.mark-as-paid');

});

// Manager Routes
Route::middleware(['auth', 'manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ManagerProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ManagerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ManagerProfileController::class, 'update'])->name('profile.update');

    // Team Management
    Route::get('/team', [ManagerDashboardController::class, 'team'])->name('team');
    Route::get('/team/{employee}', [ManagerDashboardController::class, 'teamMember'])->name('team.show');

    // Leave Management
    Route::get('/leave-requests', [App\Http\Controllers\Manager\LeaveController::class, 'index'])->name('leave.index');
    Route::post('/leave-requests/{leave}/approve', [App\Http\Controllers\Manager\LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('/leave-requests/{leave}/reject', [App\Http\Controllers\Manager\LeaveController::class, 'reject'])->name('leave.reject');
    Route::get('/my-leave', [App\Http\Controllers\Manager\LeaveController::class, 'myLeave'])->name('leave.my');
    Route::post('/my-leave', [App\Http\Controllers\Manager\LeaveController::class, 'storeMyLeave'])->name('leave.store');


    // Manager Payroll Routes
    Route::get('/payroll', [App\Http\Controllers\Manager\PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/{payroll}', [App\Http\Controllers\Manager\PayrollController::class, 'show'])->name('payroll.show');
    Route::get('/payroll/{payroll}/payslip', [App\Http\Controllers\Manager\PayrollController::class, 'payslip'])->name('payroll.payslip');

    // Overtime Management
    Route::get('/overtime', [ManagerOvertimeController::class, 'index'])->name('overtime.index');
    Route::post('/overtime', [ManagerOvertimeController::class, 'store'])->name('overtime.store');

    // Attendance Management
//    Route::get('/attendance', [App\Http\Controllers\Manager\AttendanceController::class, 'index'])->name('attendance.index');
//    Route::get('/attendance/{attendance}', [App\Http\Controllers\Manager\AttendanceController::class, 'show'])->name('attendance.show');

    Route::get('/attendance', [App\Http\Controllers\Manager\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/checkin', [App\Http\Controllers\Manager\AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/checkout', [App\Http\Controllers\Manager\AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/attendance', [App\Http\Controllers\Manager\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/checkin', [App\Http\Controllers\Manager\AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/checkout', [App\Http\Controllers\Manager\AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::post('/attendance/start-break', [App\Http\Controllers\Manager\AttendanceController::class, 'startBreak'])->name('attendance.start-break');
    Route::post('/attendance/end-break', [App\Http\Controllers\Manager\AttendanceController::class, 'endBreak'])->name('attendance.end-break');
    Route::get('/attendance/{attendance}', [App\Http\Controllers\Manager\AttendanceController::class, 'show'])->name('attendance.show');

});

// Employee Routes
Route::middleware(['auth', 'employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [EmployeeProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [EmployeeProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [EmployeeProfileController::class, 'update'])->name('profile.update');

    // Leave Management
    Route::get('/leave', [App\Http\Controllers\Employee\LeaveController::class, 'index'])->name('leave.index');
    Route::get('/leave/create', [App\Http\Controllers\Employee\LeaveController::class, 'create'])->name('leave.create');
    Route::post('/leave', [App\Http\Controllers\Employee\LeaveController::class, 'store'])->name('leave.store');
    Route::get('/leave/{leave}', [App\Http\Controllers\Employee\LeaveController::class, 'show'])->name('leave.show');

    // Attendance Management
    Route::get('/attendance', [EmployeeAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/checkin', [EmployeeAttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/checkout', [EmployeeAttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/attendance', [EmployeeAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/checkin', [EmployeeAttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/checkout', [EmployeeAttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::post('/attendance/start-break', [EmployeeAttendanceController::class, 'startBreak'])->name('attendance.start-break');
    Route::post('/attendance/end-break', [EmployeeAttendanceController::class, 'endBreak'])->name('attendance.end-break');
    Route::get('/attendance/{attendance}', [EmployeeAttendanceController::class, 'show'])->name('attendance.show');



    // Payroll
    Route::get('/payroll', [EmployeePayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/{payroll}', [EmployeePayrollController::class, 'show'])->name('payroll.show');
    Route::get('/payroll/{payroll}/payslip', [EmployeePayrollController::class, 'payslip'])->name('payroll.payslip');

    // Documents
    Route::get('/documents', [EmployeeDocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}/download', [EmployeeDocumentController::class, 'download'])->name('documents.download');

});
