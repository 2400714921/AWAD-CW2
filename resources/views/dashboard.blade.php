<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }} — {{ $title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-alerts />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($figures as $label => $value)
                    <div class="p-5 bg-white shadow-sm sm:rounded-lg">
                        <p class="text-sm text-gray-500">{{ $label }}</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <h3 class="text-base font-medium text-gray-900">Actions open to you</h3>
                <p class="mt-1 text-sm text-gray-500">
                    These are listed by the same permissions that protect the routes behind them, so
                    nothing here leads to a page you would be refused.
                </p>

                <div class="flex flex-wrap gap-3 mt-4">
                    @can('create', App\Models\Student::class)
                        <a href="{{ route('students.create') }}"
                           class="inline-block px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded hover:bg-gray-700">
                            New student record
                        </a>
                    @endcan

                    @can('create', App\Models\Course::class)
                        <a href="{{ route('courses.create') }}"
                           class="inline-block px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded hover:bg-gray-700">
                            New course
                        </a>
                    @endcan

                    @can('create', App\Models\CourseRegistration::class)
                        <a href="{{ route('registrations.create') }}"
                           class="inline-block px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded hover:bg-indigo-500">
                            Register for a course
                        </a>
                    @endcan

                    <a href="{{ route('students.index') }}" class="text-sm text-indigo-600 hover:underline">Students</a>
                    <a href="{{ route('courses.index') }}" class="text-sm text-indigo-600 hover:underline">Courses</a>
                    <a href="{{ route('registrations.index') }}" class="text-sm text-indigo-600 hover:underline">Registrations</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
