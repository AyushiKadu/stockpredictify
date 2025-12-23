<x-app-layout>
    <div class="container mx-auto text-center py-10">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">StockPredictify</h1>

        <form method="POST" action="{{ route('stocks.fetch') }}" class="flex justify-center gap-2">
            @csrf
            <input type="text" name="symbol" placeholder="Enter Stock Symbol (e.g. INFY)"
                class="border border-gray-300 px-4 py-2 rounded w-64" required>
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Search
            </button>
        </form>

        @if(session('error'))
            <p class="text-red-500 mt-4">{{ session('error') }}</p>
        @endif
    </div>
</x-app-layout>
