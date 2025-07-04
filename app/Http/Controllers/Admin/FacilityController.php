<?php
// php artisan make:controller Admin/FacilityController --resource

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Facility::withCount('employees');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $facilities = $query->paginate($request->get('per_page', 15));

        return inertia('Admin/Facilities/Index', [
            'facilities' => $facilities,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function create()
    {
        return inertia('Admin/Facilities/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:facilities',
            'description' => 'nullable|string',
        ]);

        Facility::create($validated);

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility created successfully.');
    }

    public function show(Facility $facility)
    {
        $facility->load(['employees.user']);

        return inertia('Admin/Facilities/Show', [
            'facility' => $facility,
        ]);
    }

    public function edit(Facility $facility)
    {
        return inertia('Admin/Facilities/Edit', [
            'facility' => $facility,
        ]);
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:facilities,name,' . $facility->id,
            'description' => 'nullable|string',
        ]);

        $facility->update($validated);

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility updated successfully.');
    }

    public function destroy(Facility $facility)
    {
        if ($facility->employees()->count() > 0) {
            return redirect()->route('admin.facilities.index')
                ->with('error', 'Cannot delete facility with existing employees.');
        }

        $facility->delete();

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility deleted successfully.');
    }
}