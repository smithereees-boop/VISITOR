
<?php

require __DIR__ . '/includes/bootstrap.php';
require_login();

$errors = [];

$old = [
    'full_name' => '',
    'phone' => '',
    'organisation' => '',
    'host_name' => '',
    'purpose' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    foreach ($old as $field => $_) {
        $old[$field] = trim($_POST[$field] ?? '');
    }

    // Validate full name
    if ($old['full_name'] === '') {
        $errors['full_name'] = 'Full name is required.';
    } elseif (mb_strlen($old['full_name']) > 150) {
        $errors['full_name'] = 'Name is too long.';
    }

    // Validate phone number
    if (!preg_match('/^\+?[0-9]{9,15}$/', $old['phone'])) {
        $errors['phone'] = 'Enter a valid phone number.';
    }

    // Validate person visiting
    if ($old['host_name'] === '') {
        $errors['host_name'] = 'Person visiting is required.';
    }

    // Validate purpose
    if ($old['purpose'] === '') {
        $errors['purpose'] = 'Purpose of visit is required.';
    }

    if (!$errors) {

        $organisation = $old['organisation'] !== ''
            ? $old['organisation']
            : null;

        $timeIn = now();
        $userId = (int) $_SESSION['user_id'];

        $stmt = $db->prepare(
            'INSERT INTO visits
            (full_name, phone, organisation,
             host_name, purpose, time_in, recorded_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->bind_param(
            'ssssssi',
            $old['full_name'],
            $old['phone'],
            $organisation,
            $old['host_name'],
            $old['purpose'],
            $timeIn,
            $userId
        );

        $stmt->execute();

        flash($old['full_name'] . ' checked in.');
        redirect('index.php');
    }
}

$title = 'Check in visitor';
require __DIR__ . '/includes/header.php';
?>

<div class="max-w-xl bg-white rounded-lg shadow p-6">

    <h1 class="text-xl font-semibold mb-4">
        Check in Visitor
    </h1>

    <form method="POST" class="space-y-4" novalidate>

        <input type="hidden" name="csrf"
               value="<?= e(csrf_token()) ?>">

        <div>
            <label for="full_name">Full Name *</label>
            <input type="text" id="full_name"
                   name="full_name"
                   value="<?= e($old['full_name']) ?>"
                   class="w-full border rounded px-3 py-2">

            <?php if (isset($errors['full_name'])): ?>
                <p class="text-red-600 text-sm">
                    <?= e($errors['full_name']) ?>
                </p>
            <?php endif; ?>
        </div>

        <div>
            <label for="phone">Phone Number *</label>
            <input type="text" id="phone"
                   name="phone"
                   value="<?= e($old['phone']) ?>"
                   class="w-full border rounded px-3 py-2">

            <?php if (isset($errors['phone'])): ?>
                <p class="text-red-600 text-sm">
                    <?= e($errors['phone']) ?>
                </p>
            <?php endif; ?>
        </div>

        <div>
            <label for="organisation">Organisation</label>
            <input type="text" id="organisation"
                   name="organisation"
                   value="<?= e($old['organisation']) ?>"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label for="host_name">Person Visiting *</label>
            <input type="text" id="host_name"
                   name="host_name"
                   value="<?= e($old['host_name']) ?>"
                   class="w-full border rounded px-3 py-2">

            <?php if (isset($errors['host_name'])): ?>
                <p class="text-red-600 text-sm">
                    <?= e($errors['host_name']) ?>
                </p>
            <?php endif; ?>
        </div>

        <div>
            <label for="purpose">Purpose of Visit *</label>
            <textarea id="purpose" name="purpose"
                      rows="3"
                      class="w-full border rounded px-3 py-2"><?= e($old['purpose']) ?></textarea>

            <?php if (isset($errors['purpose'])): ?>
                <p class="text-red-600 text-sm">
                    <?= e($errors['purpose']) ?>
                </p>
            <?php endif; ?>
        </div>

        <button type="submit"
                class="bg-blue-900 text-white rounded px-4 py-2 hover:bg-blue-800">
            Check In
        </button>

    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
