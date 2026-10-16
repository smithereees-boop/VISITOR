
<?php

require __DIR__ . '/includes/bootstrap.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare(
        'SELECT id, name, password_hash
         FROM users WHERE email = ?'
    );

    $stmt->bind_param('s', $email);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        redirect('index.php');
    }

    $error = 'Email or password is incorrect.';
}

$title = 'Log in';
require __DIR__ . '/includes/header.php';
?>

<div class="max-w-sm mx-auto mt-16 bg-white rounded-lg shadow p-6">

    <h1 class="text-xl font-semibold mb-4">
        Reception Login
    </h1>

    <?php if ($error): ?>
        <p class="mb-4 text-red-700 bg-red-100 rounded px-3 py-2">
            <?= e($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">

        <input type="hidden" name="csrf"
               value="<?= e(csrf_token()) ?>">

        <input type="email" name="email"
               placeholder="Email address"
               required
               class="w-full border rounded px-3 py-2">

        <input type="password" name="password"
               placeholder="Password"
               required
               class="w-full border rounded px-3 py-2">

        <button type="submit"
                class="w-full bg-blue-900 text-white rounded py-2">
            Log in
        </button>

    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
