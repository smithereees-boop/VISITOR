
<?php

require __DIR__ . '/includes/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

verify_csrf();

$id = (int) ($_POST['id'] ?? 0);

$timeOut = now();

$stmt = $db->prepare(
    'UPDATE visits
     SET time_out = ?
     WHERE id = ?
     AND time_out IS NULL'
);

$stmt->bind_param('si', $timeOut, $id);

$stmt->execute();

flash(
    $stmt->affected_rows > 0
        ? 'Visitor checked out.'
        : 'That visitor was already checked out.'
);

redirect('index.php');
