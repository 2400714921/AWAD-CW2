@php($record = $student ?? null)

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <x-input-label for="student_number" value="Admission number" />
        <x-text-input id="student_number" name="student_number" type="text" class="block w-full mt-1"
                      :value="old('student_number', $record?->student_number)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('student_number')" />
    </div>

    <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" class="block w-full mt-1"
                      :value="old('email', $record?->email)" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <x-input-label for="first_name" value="First name" />
        <x-text-input id="first_name" name="first_name" type="text" class="block w-full mt-1"
                      :value="old('first_name', $record?->first_name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
    </div>

    <div>
        <x-input-label for="last_name" value="Last name" />
        <x-text-input id="last_name" name="last_name" type="text" class="block w-full mt-1"
                      :value="old('last_name', $record?->last_name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
    </div>

    <div>
        <x-input-label for="programme" value="Programme" />
        <x-text-input id="programme" name="programme" type="text" class="block w-full mt-1"
                      :value="old('programme', $record?->programme ?? 'Bachelor of Business Computing')" required />
        <x-input-error class="mt-2" :messages="$errors->get('programme')" />
    </div>

    <div>
        <x-input-label for="year_of_study" value="Year of study" />
        <x-text-input id="year_of_study" name="year_of_study" type="number" min="1" max="6" class="block w-full mt-1"
                      :value="old('year_of_study', $record?->year_of_study ?? 1)" required />
        <x-input-error class="mt-2" :messages="$errors->get('year_of_study')" />
    </div>

    <div>
        <x-input-label for="date_of_birth" value="Date of birth" />
        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="block w-full mt-1"
                      :value="old('date_of_birth', $record?->date_of_birth?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
    </div>

    <div>
        <x-input-label for="user_id" value="Login account" />
        <select id="user_id" name="user_id" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">Not linked yet</option>
            @foreach ($accounts as $account)
                <option value="{{ $account->id }}" @selected(old('user_id', $record?->user_id) == $account->id)>
                    {{ $account->name }} — {{ $account->email }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
    </div>
</div>
