<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Students</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-alerts />

            @can('create', App\Models\Student::class)
                <a href="{{ route('students.create') }}"
                   class="inline-block px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded hover:bg-gray-700">
                    New student record
                </a>
            @endcan

            <div class="bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admission number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programme</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Records</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($students as $student)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $student->student_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $student->full_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $student->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $student->programme }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $student->year_of_study }}</td>
                                <td class="px-6 py-4 text-sm space-x-3">
                                    <a href="{{ route('students.show', $student) }}" class="text-indigo-600 hover:underline">Open</a>
                                    @can('update', $student)
                                        <a href="{{ route('students.edit', $student) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-sm text-gray-500">There are no student records to show you.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
