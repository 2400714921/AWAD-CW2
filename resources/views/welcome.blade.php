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
                body { display: flex; flex-direction: column; min-height: 100vh; font-family: Figtree, ui-sans-serif, system-ui, sans-serif; background: #f5f8fc; color: #0f172a; margin: 0; line-height: 1.6; }
                main { flex: 1; }
                header, main, footer { width: 100%; max-width: 68rem; margin-inline: auto; padding: 1.5rem; }
                a { color: #1d4ed8; text-decoration: none; }
                h1 { font-size: 2.5rem; }
            </style>
        @endif

        <style>
            .font-display { font-family: Fraunces, ui-serif, Georgia, serif; }
        </style>
    </head>
    <body class="flex min-h-[100dvh] flex-col bg-slate-50 font-sans text-slate-700 antialiased">
        <header class="shrink-0 border-b border-slate-200 bg-white">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
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

        <main class="flex flex-1 items-center">
            <div class="mx-auto grid w-full max-w-6xl gap-8 px-5 py-8 sm:px-8 lg:gap-12 xl:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)] xl:py-10">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-700">
                        Makerere University Business School · Coursework Two 2026/2027
                    </p>

                    <h1 class="font-display mt-4 text-4xl font-semibold leading-[1.1] text-slate-900 sm:text-5xl">
                        One register, three roles, no overlap.
                    </h1>

                    <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                        A place to keep the student register, run the course catalogue and take the registrations for a
                        semester — where each signed-in person is shown only the rows their role may read and the
                        buttons their role may press.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">
                                Go to your desk
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">
                                Sign in to the system
                            </a>
                        @endauth
                    </div>

                    <h2 class="font-display mt-8 text-base font-semibold text-slate-900">What each role does here</h2>

                    <div class="mt-3 grid gap-px overflow-hidden rounded-xl border border-slate-200 bg-slate-200 sm:grid-cols-3">
                        @foreach ([
                            'Administrator' => 'Adds and edits students and courses, registers anyone on any course, corrects a status and removes a record once nothing depends on it.',
                            'Lecturer' => 'Opens the courses assigned to them, reads who is registered on each one and the registrations standing against them.',
                            'Student' => 'Browses the courses still open to them, registers for one, keeps their programme and admission number straight, and cancels only their own registrations.',
                        ] as $role => $summary)
                            <div class="bg-white p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-blue-800">{{ $role }}</p>
                                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $summary }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <aside class="self-center rounded-2xl border border-blue-100 bg-blue-50/70 p-5 sm:p-6">
                    <h2 class="font-display text-lg font-semibold text-slate-900">What you can do in it</h2>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600">
                        Every screen reads the signed-in account first, so the rows, the links and the buttons that
                        appear are only the ones that role is allowed to touch.
                    </p>

                    <ul class="mt-4 space-y-3 border-t border-blue-100 pt-4">
                        @foreach ([
                            'Keep the register: admission number, names, programme, year of study and date of birth for every student.',
                            'Run the catalogue: course code, title, credit units, semester and the lecturer assigned to teach it.',
                            'Register a student on a course with a status and the date of enrolment, and cancel it again.',
                            'Register a student on behalf of that student, or let them do it themselves from their own desk.',
                            'Refuse a duplicate registration, a malformed form, and any delete another record still depends on.',
                        ] as $task)
                            <li class="flex gap-3 text-sm leading-relaxed text-slate-700">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                    <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>{{ $task }}</span>
                            </li>
                        @endforeach
                    </ul>
                </aside>
            </div>
        </main>

        <footer class="shrink-0 border-t border-slate-200 bg-white">
            <div class="mx-auto flex w-full max-w-6xl flex-col gap-2 px-5 py-4 text-sm sm:flex-row sm:items-center sm:justify-between sm:px-8">
                <p class="text-slate-500">
                    Built for
                    <span class="text-slate-700">BUC3131 Coursework Two, 2026/2027</span>,
                    Makerere University Business School.
                </p>
                @auth
                    <p class="text-slate-500">
                        Signed in as
                        <span class="text-slate-700">{{ Auth::user()->getRoleNames()->implode(', ') ?: 'no role' }}</span>
                    </p>
                @endauth
            </div>
        </footer>
    </body>
</html>
