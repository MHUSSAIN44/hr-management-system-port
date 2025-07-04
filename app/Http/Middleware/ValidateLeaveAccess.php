<?php
// php artisan make:middleware ValidateLeaveAccess

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateLeaveAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $leave = $request->route('leave');

        if ($leave) {
            // Employees can only access their own leaves
            if ($user->isEmployee() && $leave->employee_id !== $user->employee->id) {
                abort(403, 'Unauthorized access to leave request.');
            }

            // Managers can only access leaves from their team members
            if ($user->isManager() && $leave->employee->reporting_manager_id !== $user->id) {
                abort(403, 'Unauthorized access to leave request.');
            }
        }

        return $next($request);
    }
}