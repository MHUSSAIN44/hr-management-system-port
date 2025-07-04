<?php
// Update existing User model

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function managedLocation()
    {
        return $this->hasOne(Location::class, 'reporting_manager_id');
    }

    public function managedEmployees()
    {
        return $this->hasMany(Employee::class, 'reporting_manager_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class, 'requested_by');
    }

    // Role checking methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'reporting_manager';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }
}