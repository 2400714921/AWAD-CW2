<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('course')) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'course_code' => ['required', 'string', 'max:20', Rule::unique('courses', 'course_code')->ignore($course)],
            'title' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'between:1,12'],
            'semester' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:2000'],
            'lecturer_id' => ['required', 'integer', 'exists:lecturers,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'course_code.unique' => 'Another course already carries that code.',
            'credits.between' => 'Credit units must be between 1 and 12.',
            'lecturer_id.exists' => 'The chosen lecturer is not in the staff register.',
        ];
    }
}
