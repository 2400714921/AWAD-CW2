<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Student Management System') }} — BUC3131 Coursework Two</title>
        <meta name="description" content="Student Management System for Makerere University Business School, BUC3131 Advanced Web Application Development, Coursework Two 2026/2027. Students, courses and registrations, guarded by three roles.">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|fraunces:600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Before the assets are built the page still has to read as a page rather than raw text. */
                body { font-family: Figtree, ui-sans-serif, system-ui, sans-serif; background: #f5f8fc; color: #0f172a; margin: 0; line-height: 1.6; }
                header, main, footer { max-width: 68rem; margin-inline: auto; padding: 2rem 1.5rem; }
                a { color: #1d4ed8; text-decoration: none; }
                h1 { font-size: 2.75rem; }
                table { width: 100%; border-collapse: collapse; }
            </style>
        @endif

        <style>
            .font-display { font-family: Fraunces, ui-serif, Georgia, serif; }
        </style>
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-700 antialiased">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-5 sm:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-700 text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path d="M7 7.5h9M7 11h9M7 14.5h5" stroke-linecap="round" />
                            <path d="M15.5 15.5l2 2 4-5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M19 4.5h.5A1.5 1.5 0 0121 6v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18V6a1.5 1.5 0 011.5-1.5H5" stroke-linecap="round" />
                        </svg>
                    </span>
                    <span class="leading-tight">
                        <span class="block text-sm font-semibold text-slate-900">Student Management System</span>
                        <span class="block text-xs text-slate-500">{{ config('app.name', 'AWAD CW2') }} · BUC3131</span>
                    </span>
                </a>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800">
                                Open your desk
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 transition hover:text-blue-800">
                                Sign in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800">
                                    Create an account
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-5 py-16 sm:px-8 sm:py-20">
            <section class="grid gap-10 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,.9fr)] lg:gap-14">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-700">
                        Makerere University Business School · Coursework Two 2026/2027
                    </p>

                    <h1 class="font-display mt-5 text-4xl font-semibold leading-tight text-slate-900 sm:text-5xl">
                        One register, three roles, no overlap.
                    </h1>

                    <p class="mt-5 max-w-xl text-lg leading-relaxed text-slate-600">
                        Students, courses and the registrations between them, in one Laravel application that
                        shows each signed-in person only the rows their role may read. The registrar writes the
                        register, a lecturer reads the courses assigned to them, and a student registers for a
                        course and sees only their own place on it.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">
                                Go to your desk
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">
                                Sign in to the system
                            </a>
                        @endauth
                        <a href="https://github.com/2400714921/AWAD-CW2/blob/develop/README.md" class="rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-blue-700 hover:text-blue-800">
                            Read the coursework report
                        </a>
                    </div>

                    <p class="mt-8 flex flex-wrap gap-x-6 gap-y-2 border-t border-slate-200 pt-6 text-sm text-slate-500">
                        <span>Laravel 12 on PHP 8.2</span>
                        <span>3 roles · 9 permissions</span>
                        <span>112 tests passing</span>
                    </p>
                </div>

                <aside class="rounded-2xl border border-blue-100 bg-blue-50/70 p-6 sm:p-7">
                    <h2 class="font-display text-xl font-semibold text-slate-900">Sign in as any role</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        Seeded by
                        <span class="font-mono text-xs text-slate-800">php artisan migrate:fresh --seed</span>.
                        Running the seeders twice adds no duplicates.
                    </p>

                    <ul class="mt-5 divide-y divide-blue-100 border-y border-blue-100 bg-white/70">
                        @foreach ([
                            ['Administrator', 'System Registrar', 'registrar@makerere.ac.ug'],
                            ['Lecturer', 'Godfrey Asingya', 'g.asingya@makerere.ac.ug'],
                            ['Student', 'Nansikombi Kirsten', '2400709637@student.makerere.ac.ug'],
                        ] as [$role, $name, $email])
                            <li class="px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">{{ $role }}</p>
                                <p class="mt-0.5 text-sm font-medium text-slate-900">{{ $name }}</p>
                                <p class="mt-0.5 break-all font-mono text-xs text-slate-500">{{ $email }}</p>
                            </li>
                        @endforeach
                    </ul>

                    <p class="mt-5 text-sm text-slate-600">
                        Password for every account
                        <span class="ml-1 font-mono font-semibold text-blue-800">password</span>
                    </p>
                </aside>
            </section>

            <section class="mt-20 grid gap-8 border-t border-slate-200 pt-12 lg:grid-cols-[minmax(0,.8fr)_minmax(0,1.2fr)]">
                <div>
                    <h2 class="font-display text-3xl font-semibold text-slate-900">What each role may do</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        A route states the permission it needs, and the navigation is built from the permissions
                        the account holds — so a link to a page the role cannot open is never rendered, and typing
                        the address anyway is answered with a refusal instead of an empty table.
                    </p>
                </div>

                <dl class="divide-y divide-slate-200 border-y border-slate-200">
                    @foreach ([
                        'administrator' => [
                            'question' => 'Writes the register, the catalogue and every registration.',
                            'answer' => 'Sees every student, course and registration, may register a course on behalf of any student, and is the only role that may delete. A delete is refused while other records still depend on it.',
                            'chips' => ['manage students', 'manage courses', 'manage registrations'],
                        ],
                        'lecturer' => [
                            'question' => 'Reads the courses assigned to them.',
                            'answer' => 'Sees the courses they teach, the students registered on those courses and the registrations against them. Every list arrives already narrowed by the course lecturer, and nothing may be written.',
                            'chips' => ['view assigned courses', 'view student records'],
                        ],
                        'student' => [
                            'question' => 'Registers for courses and keeps to their own record.',
                            'answer' => 'Sees the courses they have not registered for yet, adds themselves to one and cancels only their own registrations. Another student is refused even when its address is typed in.',
                            'chips' => ['view available courses', 'register for courses', 'view own registrations'],
                        ],
                    ] as $role => $panel)
                        <div class="grid gap-4 py-6 sm:grid-cols-[9rem_minmax(0,1fr)] sm:gap-6">
                            <dt class="text-sm font-semibold uppercase tracking-wider text-blue-800">{{ ucfirst($role) }}</dt>
                            <dd>
                                <p class="text-base font-medium text-slate-900">{{ $panel['question'] }}</p>
                                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $panel['answer'] }}</p>
                                <ul class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($panel['chips'] as $chip)
                                        <li class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 font-mono text-xs text-blue-800">{{ $chip }}</li>
                                    @endforeach
                                </ul>
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <section class="mt-20 grid gap-8 border-t border-slate-200 pt-12 lg:grid-cols-[minmax(0,.8fr)_minmax(0,1.2fr)]">
                <div>
                    <h2 class="font-display text-3xl font-semibold text-slate-900">Four tables, one unique pair</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        A student takes many courses and a course is taken by many students, so the registration
                        sits between them as a row of its own carrying the status and the date of enrolment.
                    </p>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        The unique pair on
                        <span class="font-mono text-xs text-slate-800">(student_id, course_id)</span>
                        stops a duplicate registration even when the form is submitted twice from two tabs, and the
                        foreign keys cascade so removing a student leaves no orphaned registrations behind.
                    </p>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Table</th>
                                <th class="px-5 py-3 font-semibold">Holds</th>
                                <th class="px-5 py-3 text-right font-semibold">Seeded</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ([
                                ['students', 'admission number, names, programme, year of study, date of birth', '8'],
                                ['lecturers', 'staff number, names, department, the account behind it', '3'],
                                ['courses', 'course code, title, credit units, semester, teaching lecturer', '6'],
                                ['course_registrations', 'one student against one course, with status and registered_at', '20'],
                            ] as [$table, $holds, $count])
                                <tr>
                                    <td class="whitespace-nowrap px-5 py-3 font-mono text-xs text-blue-800">{{ $table }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ $holds }}</td>
                                    <td class="px-5 py-3 text-right font-semibold text-slate-900">{{ $count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl flex-col gap-3 px-5 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                <p class="text-sm text-slate-500">
                    Student Management System — built for
                    <span class="text-slate-700">BUC3131 Coursework Two, 2026/2027</span>,
                    Makerere University Business School.
                </p>
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
                    <a href="https://github.com/2400714921/AWAD-CW2" class="text-slate-600 transition hover:text-blue-800">Repository</a>
                    <a href="https://github.com/2400714921/AWAD-CW2/blob/develop/CONTRIBUTIONS.md" class="text-slate-600 transition hover:text-blue-800">Contributions</a>
                    @auth
                        <span class="text-slate-500">
                            Signed in as
                            <span class="text-slate-700">{{ Auth::user()->name }}</span>
                            with the
                            <span class="text-slate-700">{{ Auth::user()->getRoleNames()->implode(', ') ?: 'no role' }}</span>
                            role
                        </span>
                    @else
                        <span class="text-slate-400">Laravel 12 · PHP 8.2 · MariaDB</span>
                    @endauth
                </div>
            </div>
        </footer>
    </body>
</html>
