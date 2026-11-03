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
                        Students, courses and the registrations between them, in one Laravel application that shows
                        each signed-in person only the rows their role may read.
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
                        <a href="https://github.com/2400714921/AWAD-CW2/blob/develop/README.md" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-blue-700 hover:text-blue-800">
                            Read the coursework report
                        </a>
                    </div>

                    <div class="mt-8 grid gap-px overflow-hidden rounded-xl border border-slate-200 bg-slate-200 sm:grid-cols-3">
                        @foreach ([
                            'Administrator' => ['Writes the register, the catalogue and every registration, and is the only role that may delete.', ['manage students', 'manage courses', 'manage registrations']],
                            'Lecturer' => ['Reads the courses assigned to them and the students registered on those courses.', ['view assigned courses', 'view student records']],
                            'Student' => ['Registers for courses and sees only their own place on them.', ['view available courses', 'register for courses', 'view own registrations']],
                        ] as $role => $panel)
                            <div class="bg-white p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-blue-800">{{ $role }}</p>
                                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $panel[0] }}</p>
                                <ul class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach ($panel[1] as $chip)
                                        <li class="rounded-full border border-blue-100 bg-blue-50 px-2 py-0.5 font-mono text-[11px] text-blue-800">{{ $chip }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>

                    <p class="mt-6 flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500">
                        <span>Laravel 12 on PHP 8.2</span>
                        <span>4 core tables</span>
                        <span>3 roles · 9 permissions</span>
                        <span>112 tests passing</span>
                    </p>
                </div>

                <aside class="self-center rounded-2xl border border-blue-100 bg-blue-50/70 p-5 sm:p-6">
                    <h2 class="font-display text-lg font-semibold text-slate-900">Sign in as any role</h2>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600">
                        Seeded by
                        <span class="font-mono text-xs text-slate-800">php artisan migrate:fresh --seed</span>.
                        Running the seeders twice adds no duplicates.
                    </p>

                    <ul class="mt-4 divide-y divide-blue-100 border-y border-blue-100 bg-white/70">
                        @foreach ([
                            ['Administrator', 'System Registrar', 'registrar@makerere.ac.ug'],
                            ['Lecturer', 'Godfrey Asingya', 'g.asingya@makerere.ac.ug'],
                            ['Student', 'Nansikombi Kirsten', '2400709637@student.makerere.ac.ug'],
                        ] as [$role, $name, $email])
                            <li class="px-4 py-2.5">
                                <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">{{ $role }}</p>
                                <p class="mt-0.5 text-sm font-medium text-slate-900">{{ $name }}</p>
                                <p class="mt-0.5 break-all font-mono text-xs text-slate-500">{{ $email }}</p>
                            </li>
                        @endforeach
                    </ul>

                    <p class="mt-4 text-sm text-slate-600">
                        Password for every account
                        <span class="ml-1 font-mono font-semibold text-blue-800">password</span>
                    </p>
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
                <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-sm">
                    <a href="https://github.com/2400714921/AWAD-CW2" class="text-slate-600 transition hover:text-blue-800">Repository</a>
                    <a href="https://github.com/2400714921/AWAD-CW2/blob/develop/CONTRIBUTIONS.md" class="text-slate-600 transition hover:text-blue-800">Contributions</a>
                    @auth
                        <span class="text-slate-500">
                            Signed in as
                            <span class="text-slate-700">{{ Auth::user()->getRoleNames()->implode(', ') ?: 'no role' }}</span>
                        </span>
                    @else
                        <span class="text-slate-400">Laravel 12 · PHP 8.2 · MariaDB</span>
                    @endauth
                </div>
            </div>
        </footer>
    </body>
</html>
