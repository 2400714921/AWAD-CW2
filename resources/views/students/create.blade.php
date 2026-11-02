<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New student record</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('students.store') }}" class="space-y-6">
                    @csrf

                    @include('students.fields')

                    <div class="flex items-center gap-4">
                        <x-primary-button>Save record</x-primary-button>
                        <a href="{{ route('students.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
