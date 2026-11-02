<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $heading }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-alerts />

            @can('create', App\Models\Course::class)
                <a href="{{ route('courses.create') }}"
                   class="inline-block px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded hover:bg-gray-700">
                    New course
                </a>
            @endcan

            <div class="bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credits</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Semester</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lecturer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Students</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Records</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($courses as $course)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $course->course_code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $course->title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $course->credits }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $course->semester }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $course->lecturer?->full_name ?? 'Unassigned' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $course->students_count ?? $course->students()->count() }}</td>
                                <td class="px-6 py-4 text-sm space-x-3">
                                    <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:underline">Open</a>
                                    @can('update', $course)
                                        <a href="{{ route('courses.edit', $course) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-sm text-gray-500">There are no courses to show here.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
