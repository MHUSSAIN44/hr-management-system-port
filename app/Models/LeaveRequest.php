<?php
// php artisan make:model LeaveRequest

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_requested',
        'reason',
        'manager_status',
        'manager_comments',
        'manager_approved_at',
        'admin_status',
        'admin_comments',
        'admin_approved_at',
        'overall_status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'manager_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper methods
    public function isPending()
    {
        return $this->overall_status === 'pending';
    }

    public function isApproved()
    {
        return $this->overall_status === 'approved';
    }

    public function isRejected()
    {
        return $this->overall_status === 'rejected';
    }

    public function needsManagerApproval()
    {
        return $this->manager_status === 'pending';
    }

    public function needsAdminApproval()
    {
        return $this->manager_status === 'approved' && $this->admin_status === 'pending';
    }

    public function approveByManager($comments = null)
    {
        $this->update([
            'manager_status' => 'approved',
            'manager_comments' => $comments,
            'manager_approved_at' => now(),
        ]);
    }

    public function rejectByManager($comments = null)
    {
        $this->update([
            'manager_status' => 'rejected',
            'manager_comments' => $comments,
            'overall_status' => 'rejected',
        ]);
    }

    public function approveByAdmin($comments = null)
    {
        $this->update([
            'admin_status' => 'approved',
            'admin_comments' => $comments,
            'admin_approved_at' => now(),
            'overall_status' => 'approved',
        ]);
    }

    public function rejectByAdmin($comments = null)
    {
        $this->update([
            'admin_status' => 'rejected',
            'admin_comments' => $comments,
            'overall_status' => 'rejected',
        ]);
    }
}