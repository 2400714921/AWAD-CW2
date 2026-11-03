<aside class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-40 lg:flex lg:w-64 lg:flex-col bg-white border-r border-slate-200 print:hidden">
    <div class="shrink-0 h-16 flex items-center px-6 border-b border-slate-200">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-logo class="block h-9 w-auto text-blue-700" />
            <span class="text-sm font-semibold text-slate-900 leading-tight">Student<br>Management</span>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-4">
        <x-nav-links />
    </nav>

    <div class="shrink-0 border-t border-slate-200 px-4 py-4">
        <div class="px-3 text-base font-medium text-slate-800">{{ Auth::user()->name }}</div>
        <div class="px-3 text-sm text-slate-500">{{ Auth::user()->email }}</div>

        <div class="mt-3 space-y-1">
            <x-nav-link :href="route('profile.edit')">
                {{ __('Profile') }}
            </x-nav-link>

            <!-- Authentication -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                    this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-nav-link>
            </form>
        </div>
    </div>
</aside>
