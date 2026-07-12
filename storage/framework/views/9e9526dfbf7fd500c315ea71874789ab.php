<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Status</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto space-y-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Database Status</h1>
            <a href="/ar-overview" class="text-indigo-600 hover:underline text-sm">Back to Dashboard</a>
        </div>

        <!-- Customers -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Customers (<?php echo e($customers->count()); ?>)</h2>
            <table class="w-full text-sm text-left">
                <thead><tr class="border-b bg-gray-50"><th class="p-2">ID</th><th class="p-2">Name</th><th class="p-2">Email</th><th class="p-2">Created</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b hover:bg-gray-50"><td class="p-2"><?php echo e($c->id); ?></td><td class="p-2"><?php echo e($c->name); ?></td><td class="p-2"><?php echo e($c->email); ?></td><td class="p-2"><?php echo e($c->created_at); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Invoices -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Invoices (<?php echo e($invoices->count()); ?>)</h2>
            <table class="w-full text-sm text-left">
                <thead><tr class="border-b bg-gray-50"><th class="p-2">ID</th><th class="p-2">Number</th><th class="p-2">Customer</th><th class="p-2">Total</th><th class="p-2">Status</th><th class="p-2">Due Date</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b hover:bg-gray-50"><td class="p-2"><?php echo e($inv->id); ?></td><td class="p-2"><?php echo e($inv->invoice_number); ?></td><td class="p-2"><?php echo e($inv->customer->name); ?></td><td class="p-2">₱<?php echo e(number_format($inv->total, 2)); ?></td><td class="p-2"><span class="px-2 py-0.5 rounded-full text-xs font-medium <?php if($inv->status=='cleared'): ?> bg-green-100 text-green-700 <?php elseif($inv->status=='overdue'): ?> bg-red-100 text-red-700 <?php elseif($inv->status=='draft'): ?> bg-blue-100 text-blue-700 <?php else: ?> bg-yellow-100 text-yellow-700 <?php endif; ?>"><?php echo e(ucfirst($inv->status)); ?></span></td><td class="p-2"><?php echo e($inv->due_date->format('Y-m-d')); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Payments -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Payments (<?php echo e($payments->count()); ?>)</h2>
            <table class="w-full text-sm text-left">
                <thead><tr class="border-b bg-gray-50"><th class="p-2">ID</th><th class="p-2">Reference</th><th class="p-2">Customer</th><th class="p-2">Amount</th><th class="p-2">Method</th><th class="p-2">Status</th><th class="p-2">Date</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b hover:bg-gray-50"><td class="p-2"><?php echo e($p->id); ?></td><td class="p-2"><?php echo e($p->reference_no); ?></td><td class="p-2"><?php echo e($p->customer->name); ?></td><td class="p-2">₱<?php echo e(number_format($p->amount, 2)); ?></td><td class="p-2"><?php echo e($p->method); ?></td><td class="p-2"><span class="px-2 py-0.5 rounded-full text-xs font-medium <?php if($p->status=='cleared'): ?> bg-green-100 text-green-700 <?php else: ?> bg-yellow-100 text-yellow-700 <?php endif; ?>"><?php echo e(ucfirst($p->status)); ?></span></td><td class="p-2"><?php echo e($p->payment_date->format('Y-m-d')); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Payment Applications -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Payment Applications (<?php echo e($applications->count()); ?>)</h2>
            <table class="w-full text-sm text-left">
                <thead><tr class="border-b bg-gray-50"><th class="p-2">ID</th><th class="p-2">Payment Ref</th><th class="p-2">Invoice</th><th class="p-2">Amount Applied</th></tr></thead>
                <tbody>
                    <?php $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b hover:bg-gray-50"><td class="p-2"><?php echo e($a->id); ?></td><td class="p-2"><?php echo e($a->payment->reference_no); ?></td><td class="p-2"><?php echo e($a->invoice->invoice_number); ?></td><td class="p-2">₱<?php echo e(number_format($a->amount_applied, 2)); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-400 text-center">Database file: database/database.sqlite</p>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\finance-accounting\resources\views/db-status.blade.php ENDPATH**/ ?>