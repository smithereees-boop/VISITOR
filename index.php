
<?php

require __DIR__ . '/includes/bootstrap.php';
require_login();

// Start of today
$today = date('Y-m-d 00:00:00');

// Retrieve today's visitors
$stmt = $db->prepare(
    'SELECT * FROM visits
     WHERE time_in >= ?
     ORDER BY time_in DESC'
);

$stmt->bind_param('s', $today);
$stmt->execute();

$visits = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate totals
$total = count($visits);

$inside = count(array_filter(
    $visits,
    fn($v) => $v['time_out'] === null
));

$title = "Today's Visitors";
require __DIR__ . '/includes/header.php';

?>

<h1 class="text-2xl font-bold mb-6">
    Today's Visitors
</h1>

<!-- Summary cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">Visitors Today</p>
        <p class="text-3xl font-bold text-blue-900">
            <?= $total ?>
        </p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">Still Inside</p>
        <p class="text-3xl font-bold text-green-700">
            <?= $inside ?>
        </p>
    </div>

</div>

<a href="checkin.php"
   class="inline-block bg-blue-900 text-white
          px-4 py-2 rounded mb-6">
    + Check In Visitor
</a>

<!-- Visitors table -->
<div class="bg-white rounded-lg shadow overflow-x-auto">

    <table class="w-full text-sm text-left">

        <thead class="bg-gray-200">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Phone</th>
                <th class="px-4 py-3">Organisation</th>
                <th class="px-4 py-3">Visiting</th>
                <th class="px-4 py-3">Purpose</th>
                <th class="px-4 py-3">Time In</th>
                <th class="px-4 py-3">Time Out</th>
                <th class="px-4 py-3">Action</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!$visits): ?>

            <tr>
                <td colspan="8"
                    class="px-4 py-6 text-center text-gray-500">
                    No visitors yet today.
                </td>
            </tr>

        <?php else: ?>

            <?php foreach ($visits as $v): ?>

                <tr class="border-t">

                    <td class="px-4 py-3">
                        <?= e($v['full_name']) ?>
                    </td>

                    <td class="px-4 py-3">
                        <?= e($v['phone']) ?>
                    </td>

                    <td class="px-4 py-3">
                        <?= e($v['organisation'] ?? '') ?>
                    </td>

                    <td class="px-4 py-3">
                        <?= e($v['host_name']) ?>
                    </td>

                    <td class="px-4 py-3">
                        <?= e($v['purpose']) ?>
                    </td>

                    <td class="px-4 py-3">
                        <?= e(date('H:i', strtotime($v['time_in']))) ?>
                    </td>

                    <td class="px-4 py-3">

                        <?php if ($v['time_out'] === null): ?>

                            <span class="bg-green-100 text-green-800
                                         px-2 py-1 rounded text-xs">
                                Inside
                            </span>

                        <?php else: ?>

                            <?= e(date('H:i', strtotime($v['time_out']))) ?>

                        <?php endif; ?>

                    </td>

                    <td class="px-4 py-3">

                        <?php if ($v['time_out'] === null): ?>
                            <span class="text-gray-500 text-xs">
                                Checkout in Step 8
                            </span>
                        <?php else: ?>
                            <span class="text-gray-400">—</span>
                        <?php endif; ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
