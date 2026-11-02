<footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 space-y-1">
        <p class="text-sm text-gray-500">
            {{ config('app.name') }} — Student Management System, built for BUC3131 Coursework Two, 2026/2027.
        </p>
        <p class="text-sm text-gray-500">
            Signed in as
            <span class="font-medium text-gray-700">{{ Auth::user()->name }}</span>
            with the
            <span class="font-medium text-gray-700">{{ Auth::user()->getRoleNames()->implode(', ') ?: 'no role' }}</span>
            role.
        </p>
    </div>
</footer>
