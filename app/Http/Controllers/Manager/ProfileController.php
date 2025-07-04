<?php
// php artisan make:controller Manager/ProfileController

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)
            ->with([
                'user', 'department', 'designation', 'facility', 'location',
                'reportingManager', 'documents', 'assets',
                'leaveRequests' => function($query) {
                    $query->latest()->take(5);
                }
            ])
            ->first();

        if (!$employee) {
            abort(404, 'Manager profile not found.');
        }

        return inertia('Manager/Profile/Show', [
            'employee' => $employee,
        ]);
    }

    public function edit()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)
            ->with(['user', 'department', 'designation', 'facility', 'location'])
            ->first();

        if (!$employee) {
            abort(404, 'Manager profile not found.');
        }

        return inertia('Manager/Profile/Edit', [
            'employee' => $employee,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            abort(404, 'Manager profile not found.');
        }

        $validated = $request->validate([
            'contact' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:255',
            'email_address' => ['required', 'email', Rule::unique('employees')->ignore($employee->id)],
        ]);

        // Update employee information
        $employee->update($validated);

        // Update user email if changed
        if ($user->email !== $validated['email_address']) {
            $user->update(['email' => $validated['email_address']]);
        }

        return redirect()->route('manager.profile')
            ->with('success', 'Manager profile updated successfully.');
    }
}