@props(['responsive' => false])

@php
    $user = auth()->user();

    // Every link here is decided by what the signed-in account is allowed to open, so the desktop
    // bar and the mobile drawer can both read from the same list.
    $links = [
        [
            'route' => 'dashboard',
            'pattern' => 'dashboard',
            'label' => 'Overview',
            'allowed' => true,
        ],
        [
            'route' => 'administrator',
            'pattern' => 'administrator',
            'label' => 'Administrator desk',
            'allowed' => $user->checkPermissionTo('manage students'),
        ],
        [
            'route' => 'lecturer',
            'pattern' => 'lecturer',
            'label' => 'Lecturer desk',
            'allowed' => $user->checkPermissionTo('view assigned courses'),
        ],
        [
            'route' => 'student',
            'pattern' => 'student',
            'label' => 'My studies',
            'allowed' => $user->checkPermissionTo('view available courses'),
        ],
        [
            'route' => 'students.index',
            'pattern' => 'students.*',
            'label' => 'Students',
            'allowed' => $user->can('viewAny', App\Models\Student::class),
        ],
        [
            'route' => 'courses.index',
            'pattern' => 'courses.*',
            'label' => 'Courses',
            'allowed' => $user->can('viewAny', App\Models\Course::class),
        ],
        [
            'route' => 'registrations.index',
            'pattern' => 'registrations.*',
            'label' => 'Registrations',
            'allowed' => $user->can('viewAny', App\Models\CourseRegistration::class),
        ],
        [
            'route' => 'registrations.create',
            'pattern' => 'registrations.create',
            'label' => 'Register for a course',
            'allowed' => $user->can('create', App\Models\CourseRegistration::class),
        ],
    ];
@endphp

@foreach ($links as $link)
    @continue(! $link['allowed'])

    @if ($responsive)
        <x-responsive-nav-link :href="route($link['route'])" :active="request()->routeIs($link['pattern'])">
            {{ $link['label'] }}
        </x-responsive-nav-link>
    @else
        <x-nav-link :href="route($link['route'])" :active="request()->routeIs($link['pattern'])">
            {{ $link['label'] }}
        </x-nav-link>
    @endif
@endforeach
