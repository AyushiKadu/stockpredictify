<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Holding</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white flex justify-center items-center min-h-screen">
  <div class="bg-gray-800 p-6 rounded-lg w-96">
    <h1 class="text-2xl font-bold mb-4">✏️ Edit Holding</h1>

    <form action="{{ route('holdings.update', $holding->id) }}" method="POST">
      @csrf @method('PUT')
      <label class="block mb-2">Symbol</label>
      <input type="text" name="symbol" value="{{ $holding->symbol }}" class="w-full p-2 mb-4 text-black rounded" required>

      <label class="block mb-2">Quantity</label>
      <input type="number" name="quantity" value="{{ $holding->quantity }}" class="w-full p-2 mb-4 text-black rounded" required>

      <label class="block mb-2">Buy Price (₹)</label>
      <input type="number" name="buy_price" step="0.01" value="{{ $holding->buy_price }}" class="w-full p-2 mb-4 text-black rounded" required>

      <button class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600 w-full">Update</button>
    </form>

    <a href="{{ route('holdings.index', $portfolio->id) }}" class="block text-center mt-4 text-blue-400 hover:underline">⬅ Back</a>
  </div>
</body>
</html>
