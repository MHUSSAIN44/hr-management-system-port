<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // We'll handle authorization in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'job_title' => 'required|string|max:255',
            'employment_status' => 'required|string|in:Full-time,Part-time,Contract,Probation,Intern',
            'join_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date|before:today',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Employee ID validation with unique check that excludes the current employee when updating
        if ($this->isMethod('post')) {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['employee_id'] = 'required|string|max:50|unique:employees,employee_id';
        } else {
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('employee')->user_id ?? null),
            ];
            $rules['employee_id'] = [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'employee_id')->ignore($this->route('employee') ?? null),
            ];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'department_id' => 'department',
            'job_title' => 'job title',
            'employment_status' => 'employment status',
            'join_date' => 'join date',
            'phone' => 'phone number',
            'birth_date' => 'birth date',
            'emergency_contact_name' => 'emergency contact name',
            'emergency_contact_phone' => 'emergency contact phone',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'department_id.exists' => 'The selected department does not exist.',
            'birth_date.before' => 'The birth date must be a date before today.',
            'email.unique' => 'This email address is already in use.',
            'employee_id.unique' => 'This employee ID is already in use.',
        ];
    }
}
