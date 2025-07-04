<?php
// php artisan make:model Document

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'title',
        'file_path',
        'expiry_date',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= $days;
    }
}