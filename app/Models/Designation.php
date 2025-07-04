<?php
// php artisan make:model Designation

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
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
}