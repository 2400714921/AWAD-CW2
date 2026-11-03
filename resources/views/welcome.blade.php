<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Student Management System — BUC3131</title>
        <meta name="description" content="Student Management System for Makerere University Business School, BUC3131 Advanced Web Application Development. Students, courses and registrations, guarded by three roles.">

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
                        <span class="block text-xs text-slate-500">BUC3131 · 2026/2027</span>
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
                        Makerere University Business School · 2026/2027
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
                            'Administrator' => [
                                'summary' => 'Adds and edits students and courses, registers anyone on any course, corrects a status and removes a record once nothing depends on it.',
                                'image' => '/images/role-administrator.jpg',
                                'alt' => 'The main administration block at Makerere University',
                            ],
                            'Lecturer' => [
                                'summary' => 'Opens the courses assigned to them, reads who is registered on each one and the registrations standing against them.',
                                'image' => '/images/role-lecturer.jpg',
                                'alt' => 'Two presenters at the front of a Makerere lecture room',
                            ],
                            'Student' => [
                                'summary' => 'Browses the courses still open to them, registers for one, keeps their programme and admission number straight, and cancels only their own registrations.',
                                'image' => '/images/role-student.jpg',
                                'alt' => 'Students walking up to the Main Library at Makerere University',
                            ],
                        ] as $role => $panel)
                            <div class="bg-white p-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $panel['image'] }}" alt="{{ $panel['alt'] }}" width="200" height="200"
                                         loading="lazy" decoding="async"
                                         class="h-10 w-10 shrink-0 rounded-lg object-cover ring-1 ring-slate-200">
                                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-800">{{ $role }}</p>
                                </div>
                                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $panel['summary'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <aside class="self-center rounded-2xl border border-blue-100 bg-blue-50/70 p-5 sm:p-6">
                    <img src="/images/hero-lecture-hall.jpg"
                         alt="Students at their desks in a Makerere University lecture room while two presenters work at the front"
                         width="1120" height="700" fetchpriority="high" decoding="async"
                         class="h-48 w-full rounded-xl object-cover shadow-sm ring-1 ring-slate-200">
                </aside>
            </div>
        </main>
    </body>
</html>
