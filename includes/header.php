
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title><?= e($title ?? 'Visitors Book') ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

<?php if (!empty($_SESSION['user_id'])): ?>
<nav class="bg-blue-900 text-white">
    <div class="max-w-6xl mx-auto px-4 py-3
                flex flex-wrap gap-4 items-center">

        <span class="font-semibold">Visitors Book</span>

        <a href="index.php" class="hover:underline">Today</a>

        <a href="checkin.php" class="hover:underline">Check in</a>

        <a href="search.php" class="hover:underline">Search</a>

        <span class="ml-auto text-sm">
            <?= e($_SESSION['user_name']) ?>
        </span>

        <a href="logout.php" class="text-sm hover:underline">
            Log out
        </a>
    </div>
</nav>
<?php endif; ?>

<main class="max-w-6xl mx-auto px-4 py-6">

<?php if ($msg = get_flash()): ?>
    <div class="mb-4 rounded bg-green-100
                text-green-800 px-4 py-3">
        <?= e($msg) ?>
    </div>
<?php endif; ?>
