<?php
// php artisan make:controller Admin/LocationController --resource

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::with(['reportingManager', 'employees']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $locations = $query->paginate($request->get('per_page', 15));

        return inertia('Admin/Locations/Index', [
            'locations' => $locations,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function create()
    {
        return inertia('Admin/Locations/Create', [
            'reporting_managers' => User::where('role', 'reporting_manager')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations',
            'address' => 'nullable|string',
            'reporting_manager_id' => 'nullable|exists:users,id',
        ]);

        Location::create($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function show(Location $location)
    {
        $location->load(['reportingManager', 'employees.user']);

        return inertia('Admin/Locations/Show', [
            'location' => $location,
        ]);
    }

    public function edit(Location $location)
    {
        return inertia('Admin/Locations/Edit', [
            'location' => $location,
            'reporting_managers' => User::where('role', 'reporting_manager')->get(),
        ]);
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id,
            'address' => 'nullable|string',
            'reporting_manager_id' => 'nullable|exists:users,id',
        ]);

        $location->update($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        if ($location->employees()->count() > 0) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Cannot delete location with existing employees.');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }
}