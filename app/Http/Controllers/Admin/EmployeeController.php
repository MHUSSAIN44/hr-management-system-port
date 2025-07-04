<?php
// php artisan make:controller Admin/EmployeeController --resource

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Facility;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'department', 'designation', 'facility', 'location', 'reportingManager']);

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

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('user', function ($userQuery) use ($request) {
                $userQuery->where('role', $request->role);
            });
        }

        // Other existing filters...
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        $employees = $query->paginate($request->get('per_page', 15));

        return inertia('Admin/Employees/Index', [
            'employees' => $employees,
            'departments' => Department::all(),
            'locations' => Location::all(),
            'statuses' => ['active', 'inactive', 'terminated'],
            'roles' => ['admin', 'reporting_manager', 'employee'], // Add roles filter
            'filters' => $request->only(['search', 'department', 'location', 'status', 'role', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function create()
    {
        return inertia('Admin/Employees/Create', [
            'departments' => Department::all(),
            'designations' => Designation::all(),
            'facilities' => Facility::all(),
            'locations' => Location::all(),
            'reporting_managers' => User::where('role', 'reporting_manager')
                ->with('employee')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'employee_name' => $user->employee ? $user->employee->employee_name : $user->name,
                    ];
                }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'title' => 'required|in:Mr.,Mrs.,Ms.,Dr.',
            'employee_name' => 'required|string|max:255',
            'email_address' => 'required|email|unique:employees,email_address',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'facility_id' => 'required|exists:facilities,id',
            'location_id' => 'required|exists:locations,id',
            'contact' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:255',
            'company_first_paid_working_day' => 'required|date',
            'nationality' => 'required|string|max:255',
            'passport_number' => 'required|string|max:255',
            'passport_expiry' => 'required|date',
            'passport_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'visa_number' => 'required|string|max:255',
            'visa_start_date' => 'required|date',
            'visa_expiry_date' => 'required|date|after:visa_start_date',
            'visa_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'date_of_birth' => 'required|date|before:today',
            'driving_license_expiry' => 'nullable|date',
            'covid_vaccinated' => 'boolean',
            'insurance_issued_on' => 'nullable|date',
            'insurance_expiry_date' => 'nullable|date',
            'eid_number' => 'nullable|string|max:255',
            'eid_expiry_date' => 'nullable|date',
            'malpractice_expiry_date' => 'nullable|date',
//            'status' => 'required|in:active,inactive,terminated',
            'reporting_manager_id' => 'nullable|exists:users,id',
            'user_role' => 'required|in:employee,reporting_manager',



            // Document validation
            'documents' => 'nullable|array',
            'documents.*.title' => 'required_with:documents.*.file|string|max:255',
            'documents.*.file' => 'required_with:documents.*.title|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'documents.*.expiry_date' => 'nullable|date|after:today',
        ]);

        // If creating a reporting manager, they can't have a reporting manager
        if ($validated['user_role'] === 'reporting_manager') {
            $validated['reporting_manager_id'] = null;
        }

        // Create user account with selected role
        $user = User::create([
            'name' => $validated['employee_name'],
            'email' => $validated['email_address'],
            'password' => Hash::make('password123'), // Default password
            'role' => $validated['user_role'], // Use selected role
        ]);

        // Handle file uploads
        if ($request->hasFile('passport_file')) {
            $validated['passport_file'] = $request->file('passport_file')->store('passports', 'public');
        }

        if ($request->hasFile('visa_file')) {
            $validated['visa_file'] = $request->file('visa_file')->store('visas', 'public');
        }

        // Create employee
        $validated['user_id'] = $user->id;
        $documentsData = $validated['documents'] ?? [];
        unset($validated['documents'], $validated['user_role']);

        $employee = Employee::create($validated);

        // Handle document uploads
        if (!empty($documentsData)) {
            foreach ($documentsData as $documentData) {
                if (isset($documentData['file']) && $documentData['file']) {
                    $filePath = $documentData['file']->store('documents', 'public');

                    Document::create([
                        'employee_id' => $employee->id,
                        'title' => $documentData['title'],
                        'file_path' => $filePath,
                        'expiry_date' => $documentData['expiry_date'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    // Add method to promote employee to reporting manager
    public function promoteToManager(Employee $employee)
    {
        // Update user role
        $employee->user->update(['role' => 'reporting_manager']);

        // Remove their current reporting manager (they don't report to anyone now)
        $employee->update(['reporting_manager_id' => null]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee promoted to Reporting Manager successfully.');
    }

    // Add method to demote reporting manager to employee
    public function demoteToEmployee(Employee $employee)
    {
        // Check if they are managing any location
        if (Location::where('reporting_manager_id', $employee->user_id)->exists()) {
            return redirect()->route('admin.employees.index')
                ->with('error', 'Cannot demote: This manager is assigned to location(s). Please reassign locations first.');
        }

        // Check if they are managing other employees
        if (Employee::where('reporting_manager_id', $employee->user_id)->exists()) {
            return redirect()->route('admin.employees.index')
                ->with('error', 'Cannot demote: This manager has employees reporting to them. Please reassign employees first.');
        }

        // Update user role
        $employee->user->update(['role' => 'employee']);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Reporting Manager demoted to Employee successfully.');
    }
    public function show(Employee $employee)
    {
        $employee->load(['user', 'department', 'designation', 'facility', 'location', 'reportingManager', 'documents', 'assets']);

        return inertia('Admin/Employees/Show', [
            'employee' => $employee,
        ]);
    }

    public function edit(Employee $employee)
    {
        $employee->load(['user', 'department', 'designation', 'facility', 'location', 'documents']);

        return inertia('Admin/Employees/Edit', [
            'employee' => $employee,
            'departments' => Department::all(),
            'designations' => Designation::all(),
            'facilities' => Facility::all(),
            'locations' => Location::all(),
            'reporting_managers' => User::where('role', 'reporting_manager')
                ->with('employee')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'employee_name' => $user->employee ? $user->employee->employee_name : $user->name,
                    ];
                }),
        ]);
    }


    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'title' => 'required|in:Mr.,Mrs.,Ms.,Dr.',
            'employee_name' => 'required|string|max:255',
            'email_address' => ['required', 'email', Rule::unique('employees')->ignore($employee->id)],
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'facility_id' => 'required|exists:facilities,id',
            'location_id' => 'required|exists:locations,id',
            'contact' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:255',
            'company_first_paid_working_day' => 'required|date',
            'nationality' => 'required|string|max:255',
            'passport_number' => 'required|string|max:255',
            'passport_expiry' => 'required|date',
            'passport_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'visa_number' => 'required|string|max:255',
            'visa_start_date' => 'required|date',
            'visa_expiry_date' => 'required|date|after:visa_start_date',
            'visa_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'date_of_birth' => 'required|date|before:today',
            'driving_license_expiry' => 'nullable|date',
            'covid_vaccinated' => 'boolean',
            'insurance_issued_on' => 'nullable|date',
            'insurance_expiry_date' => 'nullable|date',
            'eid_number' => 'nullable|string|max:255',
            'eid_expiry_date' => 'nullable|date',
            'malpractice_expiry_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated',
            'reporting_manager_id' => 'nullable|exists:users,id',
            'user_role' => 'required|in:employee,reporting_manager',

            // Document validation for new documents
            'documents' => 'nullable|array',
            'documents.*.title' => 'required_with:documents.*.file|string|max:255',
            'documents.*.file' => 'required_with:documents.*.title|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'documents.*.expiry_date' => 'nullable|date|after:today',
        ], [
            'user_role.required' => 'Please select an employee role.',
            'user_role.in' => 'Invalid role selected.',
        ]);

        // Handle role-based logic
        if ($validated['user_role'] === 'reporting_manager') {
            $validated['reporting_manager_id'] = null;
        }

        // Handle file uploads for passport
        if ($request->hasFile('passport_file')) {
            // Delete old passport file if exists
            if ($employee->passport_file) {
                Storage::disk('public')->delete($employee->passport_file);
            }
            $validated['passport_file'] = $request->file('passport_file')->store('passports', 'public');
        }

        // Handle file uploads for visa
        if ($request->hasFile('visa_file')) {
            // Delete old visa file if exists
            if ($employee->visa_file) {
                Storage::disk('public')->delete($employee->visa_file);
            }
            $validated['visa_file'] = $request->file('visa_file')->store('visas', 'public');
        }

        // Update user role if changed
        if ($employee->user->role !== $validated['user_role']) {
            // Check if demoting from reporting manager
            if ($employee->user->role === 'reporting_manager' && $validated['user_role'] === 'employee') {
                // Check if they are managing any location
                if (Location::where('reporting_manager_id', $employee->user_id)->exists()) {
                    return redirect()->back()->withErrors([
                        'user_role' => 'Cannot change role: This manager is assigned to location(s). Please reassign locations first.'
                    ]);
                }

                // Check if they are managing other employees
                if (Employee::where('reporting_manager_id', $employee->user_id)->exists()) {
                    return redirect()->back()->withErrors([
                        'user_role' => 'Cannot change role: This manager has employees reporting to them. Please reassign employees first.'
                    ]);
                }
            }

            // Update user role
            $employee->user->update(['role' => $validated['user_role']]);
        }

        // Update user account
        $employee->user->update([
            'name' => $validated['employee_name'],
            'email' => $validated['email_address'],
        ]);

        // Handle new document uploads
        $documentsData = $validated['documents'] ?? [];
        unset($validated['documents'], $validated['user_role']);

        // Update employee
        $employee->update($validated);

        // Handle new document uploads
        if (!empty($documentsData)) {
            foreach ($documentsData as $documentData) {
                if (isset($documentData['file']) && $documentData['file']) {
                    $filePath = $documentData['file']->store('documents', 'public');

                    Document::create([
                        'employee_id' => $employee->id,
                        'title' => $documentData['title'],
                        'file_path' => $filePath,
                        'expiry_date' => $documentData['expiry_date'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        // Delete associated files
        if ($employee->passport_file) {
            Storage::disk('public')->delete($employee->passport_file);
        }
        if ($employee->visa_file) {
            Storage::disk('public')->delete($employee->visa_file);
        }

        // Delete user account
        $employee->user->delete();

        // Delete employee (will cascade delete due to foreign key)
        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}