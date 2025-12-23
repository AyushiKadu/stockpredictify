<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $portfolio->name }} - Holdings</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white p-8">
  <div class="max-w-5xl mx-auto">

    <!-- Heading -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold">💼 {{ strtoupper($portfolio->name) }} - Holdings</h1>
      <a href="{{ route('holdings.create', $portfolio->id) }}" 
         class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all">
         ➕ Add Holding
      </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
      <p class="bg-green-600 p-3 rounded mb-6 text-center">{{ session('success') }}</p>
    @endif

    <!-- Portfolio Summary -->
    @php
      $totalValue = $holdings->sum(fn($h) => $h->quantity * ($h->current_price ?? $h->buy_price));
      $avgBuyPrice = $holdings->count() ? round($holdings->avg('buy_price'), 2) : 0;
    @endphp

    <div class="grid md:grid-cols-3 gap-4 mb-8">
      <div class="bg-gray-800 p-4 rounded-lg shadow-md">
        <h3 class="text-gray-400 text-sm mb-1">Total Holdings</h3>
        <p class="text-2xl font-semibold">{{ $holdings->count() }}</p>
      </div>
      <div class="bg-gray-800 p-4 rounded-lg shadow-md">
        <h3 class="text-gray-400 text-sm mb-1">Portfolio Value</h3>
        <p class="text-2xl font-semibold">₹{{ number_format($totalValue, 2) }}</p>
      </div>
      <div class="bg-gray-800 p-4 rounded-lg shadow-md">
        <h3 class="text-gray-400 text-sm mb-1">Avg Buy Price</h3>
        <p class="text-2xl font-semibold">₹{{ number_format($avgBuyPrice, 2) }}</p>
      </div>
    </div>

    <!-- Holdings Table -->
    <div class="overflow-x-auto rounded-lg shadow-md">
      <table class="w-full bg-gray-800">
        <thead class="bg-gray-700">
          <tr>
            <th class="p-3 text-left">Symbol</th>
            <th class="p-3 text-left">Quantity</th>
            <th class="p-3 text-left">Buy Price (₹)</th>
            <th class="p-3 text-left">Current Price (₹)</th>
            <th class="p-3 text-left">Sector</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($holdings as $holding)
          <tr class="border-b border-gray-700 hover:bg-gray-700 transition-all">
            <td class="p-3 font-semibold">{{ strtoupper($holding->symbol) }}</td>
            <td class="p-3">{{ $holding->quantity }}</td>
            <td class="p-3">₹{{ number_format($holding->buy_price, 2) }}</td>
            <td class="p-3">
              @if($holding->current_price)
                ₹{{ number_format($holding->current_price, 2) }}
              @else
                <span class="text-gray-400">—</span>
              @endif
            </td>
            <td class="p-3">{{ $holding->sector ?? 'N/A' }}</td>
            <td class="p-3 flex gap-2">
              <a href="{{ route('holdings.edit', $holding->id) }}" 
                 class="text-blue-400 hover:underline">Edit</a>
              <form action="{{ route('holdings.destroy', $holding->id) }}" 
                    method="POST" class="inline">
                @csrf @method('DELETE')
                <button class="text-red-500 hover:underline">Delete</button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-4 text-gray-400">
              No holdings yet. Add your first stock above! 📈
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <a href="{{ route('portfolios.index') }}" 
       class="block mt-8 text-blue-400 hover:underline text-center">⬅ Back to Portfolios</a>
  </div>
</body>
</html>
