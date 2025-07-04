<?php
// php artisan make:model Facility

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class);
    }
}