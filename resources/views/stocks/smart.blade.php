<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>StockPredictify · Smart Analysis</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen">
  <div class="max-w-6xl mx-auto p-6">
    <header class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">🔮 StockPredictify — Smart Terminal</h1>
      <a href="{{ route('stocks.index') }}" class="text-sm text-gray-300 hover:underline">← Back to Stocks</a>
    </header>

    <!-- input -->
    <div class="bg-gray-800 rounded-lg p-5 mb-6 shadow-lg">
      <form id="smartForm" class="flex gap-3 items-center">
        <div class="flex items-center gap-3">
          <label class="text-sm text-gray-300">Symbol</label>
          <input id="symbol" name="symbol" type="text" placeholder="e.g. TCS or INFY or GOOG" required
                 class="bg-gray-900 text-white px-3 py-2 rounded outline-none border border-gray-700"/>
        </div>
        <button id="runBtn" type="submit" class="ml-4 bg-green-500 hover:bg-green-600 text-black px-4 py-2 rounded font-semibold">
          Run Smart Analysis
        </button>
        <div id="status" class="ml-4 text-sm text-gray-400"></div>
      </form>
    </div>

    <!-- results -->
    <div id="results" class="hidden flex flex-col gap-6">

      <!-- top cards -->
      <div class="grid grid-cols-4 gap-4">
        <div class="bg-gray-850 p-4 rounded-lg border border-gray-700">
          <div class="text-sm text-gray-400">Symbol</div>
          <div id="cardSymbol" class="text-xl font-bold">-</div>
        </div>

        <div class="bg-gray-850 p-4 rounded-lg border border-gray-700">
          <div class="text-sm text-gray-400">Last Price</div>
          <div id="cardLast" class="text-xl font-bold">-</div>
        </div>

        <div class="bg-gray-850 p-4 rounded-lg border border-gray-700">
          <div class="text-sm text-gray-400">Predicted</div>
          <div id="cardPred" class="text-xl font-bold">-</div>
        </div>

        <div class="bg-gray-850 p-4 rounded-lg border border-gray-700 flex flex-col">
          <div class="text-sm text-gray-400">Sentiment</div>
          <div id="cardSent" class="text-lg font-semibold">-</div>
          <div id="cardRisk" class="text-xs text-gray-400 mt-1">Risk: -</div>
        </div>
      </div>

      <!-- visual area -->
      <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
        <div class="flex items-start justify-between mb-3">
          <div>
            <h2 class="text-lg font-bold" id="chartTitle">Price history & prediction</h2>
            <div id="newsMeta" class="text-sm text-gray-400 mt-1">News: <span id="newsCount">0</span></div>
          </div>
          <div>
            <button id="refreshBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Refresh</button>
          </div>
        </div>

        <canvas id="priceChart" height="120"></canvas>
      </div>

      <!-- analysis text -->
      <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
        <h3 class="font-semibold mb-2">AI Analyst Summary</h3>
        <pre id="analysisText" class="whitespace-pre-wrap text-sm text-gray-300">-</pre>
      </div>
    </div>

    <!-- error -->
    <div id="errorBox" class="hidden mt-4 bg-red-600 text-black p-3 rounded"></div>
  </div>

<script>
  const form = document.getElementById('smartForm');
  const statusEl = document.getElementById('status');
  const resultsEl = document.getElementById('results');
  const errorBox = document.getElementById('errorBox');
  const refreshBtn = document.getElementById('refreshBtn');

  let currentSymbol = null;
  let currentData = null;
  let chart = null;

  function showError(msg) {
    errorBox.textContent = msg;
    errorBox.classList.remove('hidden');
    setTimeout(()=> errorBox.classList.add('hidden'), 8000);
  }

  async function callPredict(symbol) {
    statusEl.textContent = 'Running analysis...';
    errorBox.classList.add('hidden');

    try {
      const res = await fetch("{{ route('stocks.predict-smart') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ symbol })
      });

      const json = await res.json();

      if (!res.ok) {
        console.error('Server error', json);
        showError(json.error || json.message || 'Server error');
        statusEl.textContent = '';
        return null;
      }

      statusEl.textContent = '';
      return json;
    } catch (err) {
      console.error(err);
      showError('Could not connect to ML service. Make sure Flask is running.');
      statusEl.textContent = '';
      return null;
    }
  }

  function renderResult(data) {
    currentData = data;
    currentSymbol = data.symbol || document.getElementById('symbol').value.toUpperCase();

    document.getElementById('cardSymbol').textContent = currentSymbol;
    document.getElementById('cardLast').textContent = data.last_price ? '₹' + Number(data.last_price).toFixed(2) : '-';
    document.getElementById('cardPred').textContent = data.predicted_price ? '₹' + Number(data.predicted_price).toFixed(2) : '-';
    document.getElementById('cardSent').textContent = (data.sentiment || 'Neutral') + (data.avg_sentiment ? ' (' + Number(data.avg_sentiment).toFixed(2) + ')' : '');
    document.getElementById('cardRisk').textContent = 'Risk: ' + (data.risk_score || 'Unknown');
    document.getElementById('newsCount').textContent = data.news_count ?? 0;
    document.getElementById('analysisText').textContent = data.analysis ?? "No textual analysis returned.";

    resultsEl.classList.remove('hidden');

    // prepare chart: we attempt to show a simple line: [historic placeholder] + predicted point
    // If backend returned last_price, predicted_price we create a small dataset to visualize a trend.
    const last = data.last_price ? Number(data.last_price) : null;
    const pred = data.predicted_price ? Number(data.predicted_price) : null;
    const labels = [];
    const values = [];

    if (last !== null) {
      // make 7 placeholder past points slightly decreasing/increasing to show trend
      for (let i = 6; i >= 0; i--) {
        labels.push(`${-i}d`);
        // synthetic history points around last price
        values.push(+(last * (1 - (Math.random()*0.02))).toFixed(2));
      }
    }

    // append today's price as last
    if (last !== null) {
      labels.push('Today');
      values.push(last);
    }

    // predicted as next
    if (pred !== null) {
      labels.push('Predicted');
      values.push(pred);
    }

    // destroy old chart
    if (chart) { chart.destroy(); chart = null; }

    const ctx = document.getElementById('priceChart').getContext('2d');
    chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: currentSymbol + ' price',
          data: values,
          borderColor: '#60A5FA',
          backgroundColor: 'rgba(96,165,250,0.06)',
          tension: 0.25,
          pointRadius: 3,
          pointBackgroundColor: function(ctx) {
            return ctx.dataIndex === (values.length-1) ? '#f97316' : '#60A5FA';
          },
          borderWidth: 2
        }]
      },
      options: {
        scales: {
          x: { ticks: { color: '#94a3b8' } },
          y: { ticks: { color: '#94a3b8' } }
        },
        plugins: {
          legend: { display: false },
        }
      }
    });
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const symbol = document.getElementById('symbol').value.trim();
    if (!symbol) return;
    statusEl.textContent = 'Contacting ML engine...';
    const data = await callPredict(symbol);
    if (data) {
      renderResult(data);
    }
  });

  refreshBtn.addEventListener('click', async () => {
    if (!currentSymbol) return;
    statusEl.textContent = 'Refreshing...';
    const data = await callPredict(currentSymbol);
    if (data) renderResult(data);
  });
</script>
</body>
</html>
