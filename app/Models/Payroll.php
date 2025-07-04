<?php
// php artisan make:model Payroll

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'payroll';

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'accommodation',
        'allowances',
        'overtime_amount',
        'deductions',
        'gross_salary',
        'net_salary',
        'month',
        'year',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'accommodation' => 'decimal:2',
        'allowances' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'deductions' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function calculateGrossSalary()
    {
        return $this->basic_salary + $this->accommodation + $this->allowances + $this->overtime_amount;
    }

    public function calculateNetSalary()
    {
        return $this->calculateGrossSalary() - $this->deductions;
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }
}