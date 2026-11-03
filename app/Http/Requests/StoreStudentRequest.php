<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Student::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_number' => ['required', 'string', 'max:20', 'unique:students,student_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'programme' => ['required', 'string', 'max:150'],
            'year_of_study' => ['required', 'integer', 'between:1,6'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_number.required' => 'The admission number is needed before a record can be opened.',
            'student_number.unique' => 'That admission number is already on the register.',
            'email.unique' => 'Another student record already uses that email address.',
            'year_of_study.between' => 'A student can only be in year one to year six.',
            'date_of_birth.before' => 'The date of birth must be in the past.',
        ];
    }
}
