<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white flex flex-col items-center min-h-screen p-8">
    <h1 class="text-3xl font-bold mb-6">✏️ Edit Portfolio</h1>

    <form action="{{ route('portfolios.update', $portfolio) }}" method="POST" class="bg-gray-800 p-6 rounded-lg w-96">
        @csrf @method('PUT')
        <label class="block mb-2 text-sm font-medium">Portfolio Name</label>
        <input type="text" name="name" value="{{ $portfolio->name }}" class="w-full p-2 mb-4 text-black rounded" required>

        <button class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600">Update</button>
    </form>
</body>
</html>
