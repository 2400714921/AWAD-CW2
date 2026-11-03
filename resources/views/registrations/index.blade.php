<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Course registrations</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-alerts />

            @can('create', App\Models\CourseRegistration::class)
                <a href="{{ route('registrations.create') }}"
                   class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded hover:bg-blue-800">
                    Register for a course
                </a>
            @endcan

            <div class="bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Lecturer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Registered</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Records</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($registrations as $registration)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    @can('view', $registration->student)
                                        <a href="{{ route('students.show', $registration->student) }}" class="text-blue-600 hover:underline">
                                            {{ $registration->student->full_name }}
                                        </a>
                                    @else
                                        {{ $registration->student->full_name }}
                                    @endcan
                                    <span class="block text-slate-500">{{ $registration->student->student_number }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <a href="{{ route('courses.show', $registration->course) }}" class="text-blue-600 hover:underline">
                                        {{ $registration->course->course_code }}
                                    </a>
                                    <span class="block text-slate-500">{{ $registration->course->title }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $registration->course->lecturer?->full_name ?? 'Unassigned' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $registration->status }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $registration->registered_at?->format('d M Y') ?? $registration->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @can('delete', $registration)
                                        <form method="POST" action="{{ route('registrations.destroy', $registration) }}"
                                              onsubmit="return confirm('Cancel this registration?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button>Cancel</x-danger-button>
                                        </form>
                                    @else
                                        <span class="text-sm text-slate-400">Read only</span>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-sm text-slate-500">No registrations to show you yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
