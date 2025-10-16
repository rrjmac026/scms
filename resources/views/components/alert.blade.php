@if (session('error'))
    <div class="mb-4 rounded-lg bg-red-100 text-red-700 p-4">
        <strong>Error:</strong> {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="mb-4 rounded-lg bg-green-100 text-green-700 p-4">
        <strong>Success:</strong> {{ session('success') }}
    </div>
@endif