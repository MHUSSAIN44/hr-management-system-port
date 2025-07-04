<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the employee's profile.
     */
    public function show()
    {
        $employee = Employee::where('user_id', Auth::id())
            ->with([
                'department',
                'designation',
                'facility',
                'location',
                'user',
                'reportingManager',
                'documents',
                'assets'
            ])
            ->firstOrFail();

        return Inertia::render('Employee/Profile/Show', [
            'employee' => $employee,
        ]);
    }

    /**
     * Show the form for editing the employee's profile.
     */
    public function edit()
    {
        $employee = Employee::where('user_id', Auth::id())
            ->with('department', 'user')
            ->firstOrFail();

        return Inertia::render('Employee/Profile/Edit', [
            'employee' => $employee,
        ]);
    }

    /**
     * Update the employee's profile.
     */
    public function update(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->firstOrFail();

        // Validate the request
        $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Handle profile photo update
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($employee->profile_photo) {
                    Storage::disk('public')->delete($employee->profile_photo);
                }
                $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            } else {
                $profilePhotoPath = $employee->profile_photo;
            }

            // Update allowed employee fields
            $employee->update([
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'profile_photo' => $profilePhotoPath,
            ]);

            return redirect()->route('employee.profile')
                ->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating profile: ' . $e->getMessage())
                ->withInput();
        }
    }
}
