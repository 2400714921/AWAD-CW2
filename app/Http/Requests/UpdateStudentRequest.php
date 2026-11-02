<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('student')) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'student_number' => ['required', 'string', 'max:20', Rule::unique('students', 'student_number')->ignore($student)],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('students', 'email')->ignore($student)],
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
            'student_number.unique' => 'That admission number belongs to another student.',
            'email.unique' => 'That email address belongs to another student.',
            'year_of_study.between' => 'A student can only be in year one to year six.',
        ];
    }
}
