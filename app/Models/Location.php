<?php
// Updated Location Model

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reporting_manager_id',
        'address',
        'latitude',
        'longitude',
        'allowed_radius',
        'location_validation_enabled',
        'location_notes',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'location_validation_enabled' => 'boolean',
    ];

    // Relationships
    public function reportingManager()
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function attendance()
    {
        return $this->hasManyThrough(Attendance::class, Employee::class);
    }

    // Helper methods
    public function hasCoordinates()
    {
        return $this->latitude && $this->longitude;
    }

    public function isLocationValidationEnabled()
    {
        return $this->location_validation_enabled && $this->hasCoordinates();
    }

    public function getFormattedCoordinates()
    {
        if ($this->hasCoordinates()) {
            return "{$this->latitude}, {$this->longitude}";
        }
        return null;
    }
}