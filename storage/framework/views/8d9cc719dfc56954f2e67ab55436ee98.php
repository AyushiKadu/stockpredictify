<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StockPredictify – Analyze Stocks</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white flex flex-col items-center justify-center min-h-screen">
  <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-96">
    <h1 class="text-2xl font-bold mb-4 text-center">📈 StockPredictify</h1>
    <p class="text-sm text-gray-400 mb-6 text-center">Enter a stock symbol to predict its next price</p>

    <form action="<?php echo e(route('stocks.fetch')); ?>" method="POST" class="space-y-4">
      <?php echo csrf_field(); ?>
      <input type="text" name="symbol" placeholder="e.g., INFY, TCS, GOOG"
             class="w-full px-3 py-2 rounded bg-gray-700 border border-gray-600 focus:outline-none focus:ring focus:ring-green-400 text-white" required>

      <button type="submit"
              class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 rounded transition">
        Predict Stock Price
      </button>
    </form>

    <?php if(session('error')): ?>
      <p class="text-red-400 mt-4 text-center"><?php echo e(session('error')); ?></p>
    <?php endif; ?>
  </div>
</body>
</html>
<?php /**PATH C:\Users\ayush\stockpredictify\resources\views/stocks/index.blade.php ENDPATH**/ ?>