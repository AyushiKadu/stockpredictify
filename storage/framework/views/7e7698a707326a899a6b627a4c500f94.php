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

    <?php if(session('success')): ?>
        <p class="bg-green-600 p-3 rounded mb-4"><?php echo e(session('success')); ?></p>
    <?php endif; ?>

    <a href="<?php echo e(route('portfolios.create')); ?>" 
       class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600 mb-6 inline-block">
        ➕ Create New Portfolio
    </a>

    <!-- ============================= -->
    <!-- 🔎 FILTERS SECTION -->
    <!-- ============================= -->
    <form method="GET" action="<?php echo e(route('portfolios.index')); ?>" 
          class="bg-gray-800 p-4 rounded-lg mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">

        <!-- Sector Filter -->
        <select name="sector" class="p-2 rounded bg-gray-900 border border-gray-700 text-white">
            <option value="">All Sectors</option>
            <option value="IT" <?php echo e(request('sector')=='IT' ? 'selected' : ''); ?>>IT</option>
            <option value="Finance" <?php echo e(request('sector')=='Finance' ? 'selected' : ''); ?>>Finance</option>
            <option value="Pharma" <?php echo e(request('sector')=='Pharma' ? 'selected' : ''); ?>>Pharma</option>
            <option value="Auto" <?php echo e(request('sector')=='Auto' ? 'selected' : ''); ?>>Auto</option>
            <option value="Energy" <?php echo e(request('sector')=='Energy' ? 'selected' : ''); ?>>Energy</option>
        </select>

        <!-- Risk Score Filter -->
        <select name="risk" class="p-2 rounded bg-gray-900 border border-gray-700 text-white">
            <option value="">All Risk Levels</option>
            <option value="Low" <?php echo e(request('risk')=='Low' ? 'selected' : ''); ?>>Low</option>
            <option value="Medium" <?php echo e(request('risk')=='Medium' ? 'selected' : ''); ?>>Medium</option>
            <option value="High" <?php echo e(request('risk')=='High' ? 'selected' : ''); ?>>High</option>
        </select>

        <!-- From Date -->
        <input type="date" name="from" value="<?php echo e(request('from')); ?>"
               class="p-2 rounded bg-gray-900 border border-gray-700 text-white">

        <!-- To Date -->
        <input type="date" name="to" value="<?php echo e(request('to')); ?>"
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
            <?php $__currentLoopData = $portfolios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portfolio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="border-b border-gray-700">
                <td class="p-3 font-semibold"><?php echo e($portfolio->name); ?></td>
                <td class="p-3"><?php echo e($portfolio->created_at->format('d M Y')); ?></td>
                <td class="p-3 flex gap-3">

                    <a href="<?php echo e(route('portfolios.show', $portfolio->id)); ?>"
                        class="text-green-400 hover:underline">📊 View</a>

                    <a href="<?php echo e(route('holdings.index', $portfolio->id)); ?>"
                        class="text-blue-400 hover:underline">💹 View Holdings</a>

                    <form action="<?php echo e(route('portfolios.destroy', $portfolio->id)); ?>" 
                          method="POST" onsubmit="return confirm('Are you sure?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button class="text-red-500 hover:underline">🗑 Delete</button>
                    </form>

                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>

    </table>
</div>

</body>
</html>
<?php /**PATH C:\Users\ayush\stockpredictify\resources\views/portfolios/index.blade.php ENDPATH**/ ?>