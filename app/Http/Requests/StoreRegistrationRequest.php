<?php

namespace App\Http\Requests;

use App\Models\CourseRegistration;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CourseRegistration::class) ?? false;
    }

    /**
     * A student registers for themselves, so the student is read off the signed-in account
     * rather than trusted from the form. An administrator may register a named student.
     */
    public function prepareForValidation(): void
    {
        if ($this->user()?->hasPermissionTo('manage registrations')) {
            $this->merge(['student_id' => $this->input('student_id')]);

            return;
        }

        $this->merge(['student_id' => $this->user()?->student?->id]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'course_id' => [
                'required',
                'integer',
                'exists:courses,id',
                // The presence verifier hands this closure a query builder, not an Eloquent one.
                Rule::unique('course_registrations', 'course_id')
                    ->where(fn (Builder $query) => $query->where('student_id', (int) $this->input('student_id'))),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'This account is not linked to a student record yet, so there is nothing to register.',
            'student_id.exists' => 'The chosen student is not in the register.',
            'course_id.unique' => 'That student is already registered for this course.',
            'course_id.exists' => 'Choose a course that exists on the catalogue.',
        ];
    }
}
