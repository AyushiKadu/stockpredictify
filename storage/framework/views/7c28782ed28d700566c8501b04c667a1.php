<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($portfolio->name); ?> - Analytics Dashboard</title>

    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-900 text-white p-10">
<div class="max-w-6xl mx-auto">

    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">📊 <?php echo e($portfolio->name); ?> Portfolio</h1>

        <div class="flex gap-3">
            <a href="<?php echo e(route('portfolios.exportPdf', $portfolio->id)); ?>"
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                📦 Download PDF
            </a>

            <a href="<?php echo e(route('portfolios.index')); ?>"
               class="text-blue-400 hover:underline">
                ⬅ Back
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-gray-800 p-6 rounded">
            <p class="text-gray-400">💰 Total Investment</p>
            <p id="card-total-investment" class="text-2xl font-bold text-yellow-400">
                ₹<?php echo e(number_format($totalInvestment, 2)); ?>

            </p>
        </div>

        <div class="bg-gray-800 p-6 rounded">
            <p class="text-gray-400">📈 Current Value</p>
            <p id="card-current-value" class="text-2xl font-bold text-green-400">
                ₹<?php echo e(number_format($totalCurrentValue, 2)); ?>

            </p>
        </div>

        <div class="bg-gray-800 p-6 rounded">
            <p class="text-gray-400">📊 Profit / Loss</p>
            <p id="card-profit-loss"
               class="text-2xl font-bold <?php echo e($profitLoss >= 0 ? 'text-green-400' : 'text-red-400'); ?>">
                ₹<?php echo e(number_format($profitLoss, 2)); ?>

                (<?php echo e(number_format($profitLossPercent, 2)); ?>%)
            </p>
        </div>
    </div>

    
    <div class="bg-gray-800 p-4 rounded-lg">
        <table id="holdingsTable" class="w-full display">
            <thead class="bg-gray-700">
            <tr>
                <th class="p-3 text-left">Symbol</th>
                <th class="p-3 text-left">Quantity</th>
                <th class="p-3 text-left">Buy Price</th>
                <th class="p-3 text-left">Current Price</th>
                <th class="p-3 text-left">Sector</th>
                <th class="p-3 text-left">AI Risk</th>
                <th class="p-3 text-left">Gain / Loss</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $holdings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $investment = $h->buy_price * $h->quantity;
                    $current    = $h->current_price * $h->quantity;
                    $gainLoss   = $current - $investment;
                    $gainPct    = $investment > 0 ? ($gainLoss / $investment) * 100 : 0;

                    $risk = ['Low','Medium','High'][rand(0,2)];
                    $riskColor = $risk === 'High' ? 'bg-red-500' : ($risk === 'Medium' ? 'bg-yellow-500' : 'bg-green-500');
                ?>
                <tr data-holding-id="<?php echo e($h->id); ?>" class="border-b border-gray-700">
                    <td class="p-3"><?php echo e(strtoupper($h->symbol)); ?></td>
                    <td class="p-3"><?php echo e($h->quantity); ?></td>
                    <td class="p-3">₹<?php echo e(number_format($h->buy_price, 2)); ?></td>
                    <td class="p-3 text-blue-300 live-price">₹<?php echo e(number_format($h->current_price, 2)); ?></td>
                    <td class="p-3"><?php echo e($h->sector ?? 'N/A'); ?></td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-black <?php echo e($riskColor); ?>">
                            🤖 <?php echo e($risk); ?>

                        </span>
                    </td>
                    <td class="p-3 gain-loss <?php echo e($gainLoss >= 0 ? 'text-green-400' : 'text-red-400'); ?>">
                        ₹<?php echo e(number_format($gainLoss, 2)); ?>

                        (<?php echo e(number_format($gainPct, 2)); ?>%)
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    
    <div class="bg-gray-800 p-6 rounded-lg mt-10">
        <h2 class="text-xl font-semibold mb-4">🥧 Portfolio Diversification</h2>
        <canvas id="pieChart" height="150"></canvas>
    </div>

    
    <div class="bg-gray-800 p-6 rounded-lg mt-10">
        <h2 class="text-xl font-semibold mb-4">📉 Portfolio Value Trend</h2>
        <canvas id="lineChart" height="140"></canvas>
    </div>
</div>


<script>
    // 1) DataTable
    $(document).ready(function () {
        $('#holdingsTable').DataTable({
            pageLength: 10
        });
    });

    // 2) Pie chart data
    const pieData = <?php echo json_encode($holdings->map(fn($h) => [
        'symbol' => strtoupper($h->symbol),
        'value'  => $h->current_price * $h->quantity
    ])); ?>;

    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: pieData.map(x => x.symbol),
            datasets: [{
                data: pieData.map(x => x.value),
                backgroundColor: [
                    '#FF6384','#36A2EB','#FFCE56','#9966FF',
                    '#4BC0C0','#F87171','#A78BFA','#FB923C'
                ]
            }]
        },
        options: {
            plugins: { legend: { labels: { color: 'white' } } }
        }
    });

    // 3) Line chart
    const lineLabels = <?php echo json_encode($chartLabels); ?>;
    const lineData   = <?php echo json_encode($chartData); ?>;

    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: lineLabels,
            datasets: [{
                label: "Portfolio Value",
                data: lineData,
                borderColor: '#4ADE80',
                borderWidth: 2,
                fill: false,
            }]
        },
        options: {
            plugins: { legend: { labels: { color: 'white' } } },
            scales: {
                y: { ticks: { color: 'white' } },
                x: { ticks: { color: 'white' } }
            }
        }
    });

    // 4) AJAX auto-refresh every 20s
    function refreshPrices() {
        $.getJSON("<?php echo e(route('portfolios.prices', $portfolio->id)); ?>", function (response) {

            // Update summary cards
            const s = response.summary;
            $('#card-total-investment').text('₹' + s.totalInvestment.toFixed(2));
            $('#card-current-value').text('₹' + s.totalCurrentValue.toFixed(2));

            const profitText = '₹' + s.profitLoss.toFixed(2) +
                ' (' + s.profitLossPercent.toFixed(2) + '%)';
            $('#card-profit-loss').text(profitText);

            // Update rows
            response.rows.forEach(function (row) {
                const tr = $('tr[data-holding-id="' + row.id + '"]');

                // current price
                tr.find('.live-price').text('₹' + row.current_price.toFixed(2));

                // gain / loss
                const glCell = tr.find('.gain-loss');
                const text = '₹' + row.gain_loss.toFixed(2) +
                    ' (' + row.gain_percent.toFixed(2) + '%)';

                glCell.text(text);
                glCell.removeClass('text-green-400 text-red-400');

                if (row.gain_loss >= 0) {
                    glCell.addClass('text-green-400');
                } else {
                    glCell.addClass('text-red-400');
                }
            });
        });
    }

    // call once + every 20 seconds
    refreshPrices();
    setInterval(refreshPrices, 20000);
</script>

</body>
</html>
<?php /**PATH C:\Users\ayush\stockpredictify\resources\views/portfolios/show.blade.php ENDPATH**/ ?>