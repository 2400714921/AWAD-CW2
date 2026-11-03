<?php

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Course::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_code' => ['required', 'string', 'max:20', 'unique:courses,course_code'],
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
            'course_code.unique' => 'A course with that code is already on the catalogue.',
            'credits.between' => 'Credit units must be between 1 and 12.',
            'lecturer_id.required' => 'Every course must be assigned to a lecturer.',
            'lecturer_id.exists' => 'The chosen lecturer is not in the staff register.',
        ];
    }
}
