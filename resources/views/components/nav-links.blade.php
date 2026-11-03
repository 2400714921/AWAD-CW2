@php
    $user = auth()->user();

    // /dashboard shows the same desk as the address named after the role, so the account's own desk is
    // listed once, as Overview, and only the desks another role holds would need their own row.
    $ownDesk = match (true) {
        $user->checkPermissionTo('manage students') => 'administrator',
        $user->checkPermissionTo('view assigned courses') => 'lecturer',
        $user->checkPermissionTo('view available courses') => 'student',
        default => 'dashboard',
    };

    // Every link here is decided by what the signed-in account is allowed to open, so the sidebar and
    // the mobile drawer can both read from the same list.
    $links = [
        [
            'route' => $ownDesk,
            'pattern' => ['dashboard', $ownDesk],
            'label' => 'Overview',
            'allowed' => true,
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

    <x-nav-link :href="route($link['route'])" :active="request()->routeIs($link['pattern'])">
        {{ $link['label'] }}
    </x-nav-link>
@endforeach
