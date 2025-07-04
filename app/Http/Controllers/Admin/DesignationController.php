<?php
// php artisan make:controller Admin/DesignationController --resource

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index(Request $request)
    {
        $query = Designation::withCount('employees');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $designations = $query->paginate($request->get('per_page', 15));

        return inertia('Admin/Designations/Index', [
            'designations' => $designations,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function create()
    {
        return inertia('Admin/Designations/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:designations',
            'description' => 'nullable|string',
        ]);

        Designation::create($validated);

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation created successfully.');
    }

    public function show(Designation $designation)
    {
        $designation->load(['employees.user']);

        return inertia('Admin/Designations/Show', [
            'designation' => $designation,
        ]);
    }

    public function edit(Designation $designation)
    {
        return inertia('Admin/Designations/Edit', [
            'designation' => $designation,
        ]);
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:designations,name,' . $designation->id,
            'description' => 'nullable|string',
        ]);

        $designation->update($validated);

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation updated successfully.');
    }

    public function destroy(Designation $designation)
    {
        if ($designation->employees()->count() > 0) {
            return redirect()->route('admin.designations.index')
                ->with('error', 'Cannot delete designation with existing employees.');
        }

        $designation->delete();

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation deleted successfully.');
    }
}