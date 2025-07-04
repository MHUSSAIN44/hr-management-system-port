<?php
// php artisan make:controller Admin/DepartmentController --resource

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::withCount('employees');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $departments = $query->paginate($request->get('per_page', 15));

        return inertia('Admin/Departments/Index', [
            'departments' => $departments,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function create()
    {
        return inertia('Admin/Departments/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['employees.user']);

        return inertia('Admin/Departments/Show', [
            'department' => $department,
        ]);
    }

    public function edit(Department $department)
    {
        return inertia('Admin/Departments/Edit', [
            'department' => $department,
        ]);
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if ($department->employees()->count() > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department with existing employees.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}