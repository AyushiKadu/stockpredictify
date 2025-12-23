<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Portfolio</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white flex flex-col items-center justify-center min-h-screen">

  <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-[400px]">
    <h1 class="text-2xl font-bold mb-6 text-center">🆕 Create New Portfolio</h1>

    @if ($errors->any())
      <div class="bg-red-500 text-white p-3 rounded mb-4">
        <ul class="list-disc pl-5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('portfolios.store') }}" class="space-y-4">
      @csrf

      <div>
        <label class="block mb-2 font-semibold">Portfolio Name</label>
        <input type="text" name="name" placeholder="e.g. My Tech Portfolio" class="w-full px-3 py-2 rounded text-black" required>
      </div>

      <button type="submit" class="bg-green-600 hover:bg-green-700 w-full py-2 rounded text-white font-semibold">
        ✅ Create Portfolio
      </button>
    </form>

    <a href="{{ route('portfolios.index') }}" class="block mt-4 text-blue-400 hover:underline text-center">⬅ Back to All Portfolios</a>
  </div>

</body>
</html>
