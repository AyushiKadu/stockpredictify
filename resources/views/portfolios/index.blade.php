<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📊 My Portfolios</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-900 text-white p-10">

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">💼 My Portfolios</h1>

    @if (session('success'))
        <p class="bg-green-600 p-3 rounded mb-4">{{ session('success') }}</p>
    @endif

    <a href="{{ route('portfolios.create') }}" 
       class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600 mb-6 inline-block">
        ➕ Create New Portfolio
    </a>

    <!-- ============================= -->
    <!-- 🔎 FILTERS SECTION -->
    <!-- ============================= -->
    <form method="GET" action="{{ route('portfolios.index') }}" 
          class="bg-gray-800 p-4 rounded-lg mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">

        <!-- Sector Filter -->
        <select name="sector" class="p-2 rounded bg-gray-900 border border-gray-700 text-white">
            <option value="">All Sectors</option>
            <option value="IT" {{ request('sector')=='IT' ? 'selected' : '' }}>IT</option>
            <option value="Finance" {{ request('sector')=='Finance' ? 'selected' : '' }}>Finance</option>
            <option value="Pharma" {{ request('sector')=='Pharma' ? 'selected' : '' }}>Pharma</option>
            <option value="Auto" {{ request('sector')=='Auto' ? 'selected' : '' }}>Auto</option>
            <option value="Energy" {{ request('sector')=='Energy' ? 'selected' : '' }}>Energy</option>
        </select>

        <!-- Risk Score Filter -->
        <select name="risk" class="p-2 rounded bg-gray-900 border border-gray-700 text-white">
            <option value="">All Risk Levels</option>
            <option value="Low" {{ request('risk')=='Low' ? 'selected' : '' }}>Low</option>
            <option value="Medium" {{ request('risk')=='Medium' ? 'selected' : '' }}>Medium</option>
            <option value="High" {{ request('risk')=='High' ? 'selected' : '' }}>High</option>
        </select>

        <!-- From Date -->
        <input type="date" name="from" value="{{ request('from') }}"
               class="p-2 rounded bg-gray-900 border border-gray-700 text-white">

        <!-- To Date -->
        <input type="date" name="to" value="{{ request('to') }}"
               class="p-2 rounded bg-gray-900 border border-gray-700 text-white">

        <!-- Filter Button -->
        <button class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-white col-span-full md:col-span-1">
            Filter
        </button>
    </form>
    <!-- END FILTERS -->

    <!-- ============================= -->
    <!-- PORTFOLIOS TABLE -->
    <!-- ============================= -->
    <table class="w-full bg-gray-800 rounded-lg mt-4">
        <thead class="bg-gray-700">
            <tr>
                <th class="p-3 text-left">Portfolio Name</th>
                <th class="p-3 text-left">Created On</th>
                <th class="p-3 text-left">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($portfolios as $portfolio)
            <tr class="border-b border-gray-700">
                <td class="p-3 font-semibold">{{ $portfolio->name }}</td>
                <td class="p-3">{{ $portfolio->created_at->format('d M Y') }}</td>
                <td class="p-3 flex gap-3">

                    <a href="{{ route('portfolios.show', $portfolio->id) }}"
                        class="text-green-400 hover:underline">📊 View</a>

                    <a href="{{ route('holdings.index', $portfolio->id) }}"
                        class="text-blue-400 hover:underline">💹 View Holdings</a>

                    <form action="{{ route('portfolios.destroy', $portfolio->id) }}" 
                          method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-500 hover:underline">🗑 Delete</button>
                    </form>

                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
</div>

</body>
</html>
