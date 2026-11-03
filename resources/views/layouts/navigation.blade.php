<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 lg:hidden print:hidden">
    <!-- Navigation Bar for Small Screens -->
    <div class="px-4 sm:px-6">
        <div class="flex justify-between h-16">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <x-application-logo class="block h-9 w-auto text-blue-700" />
                <span class="text-sm font-semibold leading-tight text-slate-900">Student<br>Management</span>
            </a>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Drawer -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-nav-links />
        </div>

        <div class="pt-4 pb-6 border-t border-slate-200">
            <div class="px-4">
                <div class="font-medium text-base text-slate-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
            </div>

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
    </div>
</nav>
