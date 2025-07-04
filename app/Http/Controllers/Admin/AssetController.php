<?php
// app/Http/Controllers/Admin/AssetController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with(['assignedEmployee.user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_name', 'like', "%{$search}%")
                    ->orWhere('asset_type', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->asset_type);
        }

        // Sorting
        $query->orderBy('created_at', 'desc');

        $assets = $query->paginate($request->get('per_page', 15))->withQueryString();

        // Get filter options
        $assetTypes = Asset::distinct()->pluck('asset_type')->filter()->sort()->values();
        $statuses = ['available', 'assigned', 'maintenance', 'retired'];

        // Get employees for assignment dropdown - THIS WAS MISSING!
        $employees = Employee::with(['user', 'department'])
            ->where('status', 'active')
            ->get();

        // Statistics
        $stats = [
            'total' => Asset::count(),
            'available' => Asset::where('status', 'available')->count(),
            'assigned' => Asset::where('status', 'assigned')->count(),
            'maintenance' => Asset::where('status', 'maintenance')->count(),
            'retired' => Asset::where('status', 'retired')->count(),
        ];

        return inertia('Admin/Assets/Index', [
            'assets' => $assets,
            'assetTypes' => $assetTypes,
            'statuses' => $statuses,
            'stats' => $stats,
            'employees' => $employees, // ADD THIS LINE
            'filters' => $request->only(['search', 'status', 'asset_type', 'per_page']),
        ]);
    }

    public function create()
    {
        $employees = Employee::with(['user', 'department'])
            ->where('status', 'active')
            ->get();

        return inertia('Admin/Assets/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_type' => 'required|string|max:255',
            'asset_name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:assets',
            'description' => 'nullable|string',
            'status' => 'required|in:available,assigned,maintenance,retired',
            'assigned_to' => 'nullable|exists:employees,id',
            'assigned_date' => 'nullable|date',
        ]);

        // Validation logic for assigned status
        if ($validated['status'] === 'assigned') {
            if (!$validated['assigned_to']) {
                return redirect()->back()->withErrors(['assigned_to' => 'Employee is required when status is assigned.']);
            }
            $validated['assigned_date'] = $validated['assigned_date'] ?? now();
        } else {
            $validated['assigned_to'] = null;
            $validated['assigned_date'] = null;
        }

        Asset::create($validated);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset created successfully.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['assignedEmployee.user', 'assignedEmployee.department', 'assignedEmployee.designation']);

        return inertia('Admin/Assets/Show', [
            'asset' => $asset,
        ]);
    }

    public function edit(Asset $asset)
    {
        $asset->load(['assignedEmployee.user']);

        $employees = Employee::with(['user', 'department'])
            ->where('status', 'active')
            ->get();

        return inertia('Admin/Assets/Edit', [
            'asset' => $asset,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_type' => 'required|string|max:255',
            'asset_name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:assets,serial_number,' . $asset->id,
            'description' => 'nullable|string',
            'status' => 'required|in:available,assigned,maintenance,retired',
            'assigned_to' => 'nullable|exists:employees,id',
            'assigned_date' => 'nullable|date',
        ]);

        // Validation logic for assigned status
        if ($validated['status'] === 'assigned') {
            if (!$validated['assigned_to']) {
                return redirect()->back()->withErrors(['assigned_to' => 'Employee is required when status is assigned.']);
            }
            if (!$validated['assigned_date']) {
                $validated['assigned_date'] = now();
            }
        } else {
            $validated['assigned_to'] = null;
            $validated['assigned_date'] = null;
        }

        $asset->update($validated);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->status === 'assigned') {
            return redirect()->route('admin.assets.index')
                ->with('error', 'Cannot delete assigned asset. Please unassign first.');
        }

        $asset->delete();

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    public function assign(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::find($validated['employee_id']);
        $asset->assignTo($employee);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset assigned successfully.');
    }

    public function unassign(Asset $asset)
    {
        $asset->unassign();

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset unassigned successfully.');
    }
}