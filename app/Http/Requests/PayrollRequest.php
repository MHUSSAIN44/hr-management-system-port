<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayrollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'required|numeric|min:0',
            'overtime' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string',
            'payment_status' => 'required|in:paid,unpaid',
            'notes' => 'nullable|string',
        ];

        if ($this->isMethod('post')) {
            $rules['employee_id'] = 'required|exists:employees,id';
            $rules['month'] = 'required|string|size:2';
            $rules['year'] = 'required|integer|min:2000|max:2099';
        }

        return $rules;
    }
}
