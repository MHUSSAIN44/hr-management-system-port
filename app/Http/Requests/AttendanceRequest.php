<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
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
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ];

        if ($this->isMethod('post') && $this->routeIs('admin.attendance.store')) {
            $rules['employee_id'] = 'required|exists:employees,id';
            $rules['check_in'] = 'nullable|date_format:H:i';
            $rules['check_out'] = 'nullable|date_format:H:i|after:check_in';
            $rules['status'] = 'required|in:present,absent,leave,holiday';
        }

        if ($this->isMethod('post') && $this->routeIs('employee.attendance.check-in')) {
            $rules['notes'] = 'nullable|string|max:255';
        }

        if ($this->isMethod('post') && $this->routeIs('employee.attendance.check-out')) {
            $rules['notes'] = 'nullable|string|max:255';
        }

        return $rules;
    }
}
