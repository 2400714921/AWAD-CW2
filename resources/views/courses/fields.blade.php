@php($record = $course ?? null)

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <x-input-label for="course_code" value="Course code" />
        <x-text-input id="course_code" name="course_code" type="text" class="block w-full mt-1"
                      :value="old('course_code', $record?->course_code)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('course_code')" />
    </div>

    <div>
        <x-input-label for="credits" value="Credit units" />
        <x-text-input id="credits" name="credits" type="number" min="1" max="12" class="block w-full mt-1"
                      :value="old('credits', $record?->credits)" required />
        <x-input-error class="mt-2" :messages="$errors->get('credits')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="title" value="Title" />
        <x-text-input id="title" name="title" type="text" class="block w-full mt-1"
                      :value="old('title', $record?->title)" required />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div>
        <x-input-label for="semester" value="Semester" />
        <x-text-input id="semester" name="semester" type="text" class="block w-full mt-1"
                      :value="old('semester', $record?->semester ?? 'Semester 1')" required />
        <x-input-error class="mt-2" :messages="$errors->get('semester')" />
    </div>

    <div>
        <x-input-label for="lecturer_id" value="Lecturer" />
        <select id="lecturer_id" name="lecturer_id" class="block w-full mt-1 border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
            <option value="">Choose a lecturer</option>
            @foreach ($lecturers as $lecturer)
                <option value="{{ $lecturer->id }}" @selected(old('lecturer_id', $record?->lecturer_id) == $lecturer->id)>
                    {{ $lecturer->full_name }} — {{ $lecturer->staff_number }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('lecturer_id')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="description" value="Description" />
        <textarea id="description" name="description" rows="4"
                  class="block w-full mt-1 border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">{{ old('description', $record?->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>
</div>
