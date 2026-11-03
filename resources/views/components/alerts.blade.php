@if (session('status'))
    <div class="p-4 text-sm text-green-800 bg-green-50 border border-green-200 rounded" role="status">
        {{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div class="p-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded" role="alert">
        {{ session('error') }}
    </div>
@endif
