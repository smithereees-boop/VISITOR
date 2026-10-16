
<?php

require __DIR__ . '/includes/bootstrap.php';
require_login();

// Read search values from the URL
$q = is_string($_GET['q'] ?? null)
    ? trim($_GET['q']) : '';

$from = is_string($_GET['from'] ?? null)
    ? trim($_GET['from']) : '';

$to = is_string($_GET['to'] ?? null)
    ? trim($_GET['to']) : '';

$errors = [];
$results = [];

// Validate dates before using them
foreach (['from' => $from, 'to' => $to] as $field => $value) {
    if ($value === '') {
        continue;
    }

    $date = DateTimeImmutable::createFromFormat(
        '!Y-m-d',
        $value
    );

    if (!$date || $date->format('Y-m-d') !== $value) {
        $errors[$field] = 'Enter a valid date.';
    }
}

// Date from must not be after date to
if (!$errors && $from !== '' && $to !== '' && $from > $to) {
    $errors['to'] = 'End date must not be before start date.';
}

if (!$errors) {

    $where = [];
    $params = [];

    // Search by name or phone
    if ($q !== '') {
        $where[] = '(full_name LIKE ? OR phone LIKE ?)';
        $params[] = "%$q%";
        $params[] = "%$q%";
    }

    // Starting date
    if ($from !== '') {
        $where[] = 'time_in >= ?';
        $params[] = $from . ' 00:00:00';
    }

    // Ending date
    if ($to !== '') {
        $where[] = 'time_in <= ?';
        $params[] = $to . ' 23:59:59';
    }

    // Build SQL using fixed, trusted fragments
    $sql = 'SELECT * FROM visits'
        . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
        . ' ORDER BY time_in DESC LIMIT 100';

    $stmt = $db->prepare($sql);

    if ($params) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$title = 'Search Visits';
require __DIR__ . '/includes/header.php';

?>

<h1 class="text-2xl font-bold mb-6">
    Search Visit History
</h1>

<!-- Search form -->
<div class="bg-white rounded-lg shadow p-6 mb-6">

    <form method="GET" action="search.php"
          class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div>
            <label for="q" class="block mb-1 text-sm">
                Name or Phone
            </label>

            <input type="text" id="q" name="q"
                   value="<?= e($q) ?>"
                   placeholder="Search visitor..."
                   class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label for="from" class="block mb-1 text-sm">
                Date From
            </label>

            <input type="date" id="from" name="from"
                   value="<?= e($from) ?>"
                   class="w-full border rounded px-3 py-2">

            <?php if (isset($errors['from'])): ?>
                <p class="text-red-600 text-sm mt-1">
                    <?= e($errors['from']) ?>
                </p>
            <?php endif; ?>
        </div>

        <div>
            <label for="to" class="block mb-1 text-sm">
                Date To
            </label>

            <input type="date" id="to" name="to"
                   value="<?= e($to) ?>"
                   class="w-full border rounded px-3 py-2">

            <?php if (isset($errors['to'])): ?>
                <p class="text-red-600 text-sm mt-1">
                    <?= e($errors['to']) ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="flex items-end">
            <button type="submit"
                    class="w-full bg-blue-900 text-white
                           rounded px-4 py-2">
                Search
            </button>
        </div>

    </form>
</div>

<!-- Search results -->
<div class="bg-white rounded-lg shadow p-6">

    <?php if ($errors): ?>

        <p class="text-red-700">
            Please correct the search dates and try again.
        </p>

    <?php else: ?>

        <h2 class="text-lg font-semibold mb-4">
            Search Results:
            <?= count($results) ?> visit(s)
        </h2>

        <?php if (!$results): ?>

            <p class="text-gray-500">
                No visits match your search.
            </p>

        <?php else: ?>

            <p class="text-xs text-gray-500 mb-3">
                Showing up to 100 most recent matching visits.
            </p>

            <div class="overflow-x-auto">

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

                    <?php foreach ($results as $v): ?>

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
                                <?= e(date(
                                    'Y-m-d H:i',
                                    strtotime($v['time_in'])
                                )) ?>
                            </td>

                            <td class="px-4 py-3">

                                <?php if ($v['time_out'] === null): ?>

                                    <span class="bg-green-100 text-green-800
                                                 px-2 py-1 rounded">
                                        Inside
                                    </span>

                                <?php else: ?>

                                    <?= e(date(
                                        'Y-m-d H:i',
                                        strtotime($v['time_out'])
                                    )) ?>

                                <?php endif; ?>

                            </td>

                            <td class="px-4 py-3">

                                <?php if ($v['time_out'] === null): ?>

                                    <form method="POST"
                                          action="checkout.php"
                                          onsubmit="return confirm('Check out this visitor?')">

                                        <input type="hidden" name="csrf"
                                               value="<?= e(csrf_token()) ?>">

                                        <input type="hidden" name="id"
                                               value="<?= (int) $v['id'] ?>">

                                        <button type="submit"
                                                class="bg-gray-800 text-white
                                                       rounded px-3 py-1">
                                            Check out
                                        </button>
                                    </form>

                                <?php else: ?>

                                    <span class="text-gray-400">—</span>

                                <?php endif; ?>

                            </td>
                        </tr>

                    <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
