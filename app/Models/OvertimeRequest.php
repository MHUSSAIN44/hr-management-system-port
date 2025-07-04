<?php
// php artisan make:model OvertimeRequest

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by',
        'facility_id',
        'month',
        'year',
        'hours_requested',
        'reason',
        'status',
        'admin_comments',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function approve($comments = null)
    {
        $this->update([
            'status' => 'approved',
            'admin_comments' => $comments,
            'approved_at' => now(),
        ]);
    }

    public function reject($comments = null)
    {
        $this->update([
            'status' => 'rejected',
            'admin_comments' => $comments,
        ]);
    }
}