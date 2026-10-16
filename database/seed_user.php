<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Forbidden');
}
require __DIR__ . '/../includes/bootstrap.php';
$name = 'Reception Admin';
$email = 'admin@gmail.com';
$password = getenv('VISITOR_ADMIN_PASSWORD');
if (!$password || strlen($password) < 12) {
    exit("Set an initial password of at least 12 characters.\n");
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $db->prepare(
    'INSERT INTO users (name, email, password_hash)
     VALUES (?, ?, ?)'
);
$stmt->bind_param('sss', $name, $email, $hash);
$stmt->execute();
echo "Staff account created successfully!\n";
