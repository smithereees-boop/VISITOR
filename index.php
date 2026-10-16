
<?php

require __DIR__ . '/includes/bootstrap.php';

require_login();

$title = 'Dashboard';

require __DIR__ . '/includes/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow">

    <h1 class="text-2xl font-bold text-blue-900">
        Visitors Book Dashboard
    </h1>

    <p class="mt-4">
        Welcome,
        <?= e($_SESSION['user_name']) ?>!
    </p>

    <p class="mt-2 text-gray-600">
        You have logged in successfully.
    </p>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
