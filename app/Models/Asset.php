<?php
// php artisan make:model Asset

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_type',
        'asset_name',
        'serial_number',
        'description',
        'status',
        'assigned_to',
        'assigned_date',
    ];

    protected $casts = [
        'assigned_date' => 'date',
    ];

    // Relationships
    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    // Helper methods
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isAssigned()
    {
        return $this->status === 'assigned';
    }

    public function assignTo(Employee $employee)
    {
        $this->update([
            'assigned_to' => $employee->id,
            'assigned_date' => now(),
            'status' => 'assigned',
        ]);
    }

    public function unassign()
    {
        $this->update([
            'assigned_to' => null,
            'assigned_date' => null,
            'status' => 'available',
        ]);
    }
}