<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($portfolio->name); ?> Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f1f1f1; }
        h2 { margin-bottom: 0; }
        .green { color: green; }
        .red { color: red; }
    </style>
</head>
<body>

<h2><?php echo e($portfolio->name); ?> – Portfolio Report</h2>
<p>Date: <?php echo e(now()->format('d M Y')); ?></p>

<h3>Summary</h3>
<ul>
    <li>Total Investment: ₹<?php echo e(number_format($totalInvestment, 2)); ?></li>
    <li>Current Value: ₹<?php echo e(number_format($totalCurrentValue, 2)); ?></li>
    <li>
        Profit / Loss:
        <span class="<?php echo e($profitLoss >= 0 ? 'green' : 'red'); ?>">
            ₹<?php echo e(number_format($profitLoss, 2)); ?>

            (<?php echo e(number_format($profitLossPercent, 2)); ?>%)
        </span>
    </li>
</ul>

<h3>Holdings</h3>
<table>
    <thead>
        <tr>
            <th>Symbol</th>
            <th>Qty</th>
            <th>Buy Price</th>
            <th>Current Price</th>
            <th>Sector</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $holdings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e(strtoupper($h->symbol)); ?></td>
                <td><?php echo e($h->quantity); ?></td>
                <td>₹<?php echo e(number_format($h->buy_price, 2)); ?></td>
                <td>₹<?php echo e(number_format($h->current_price, 2)); ?></td>
                <td><?php echo e($h->sector); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

</body>
</html>
<?php /**PATH C:\Users\ayush\stockpredictify\resources\views/portfolios/pdf.blade.php ENDPATH**/ ?>