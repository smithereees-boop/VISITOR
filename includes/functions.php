<?php

function e(?string $value): string {
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}
function redirect(string $url): never {
    header("Location: $url");
    exit;
}
function flash(string $message): void {
    $_SESSION['flash'] = $message;
}

function get_flash(): ?string {
    $msg = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $msg;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function verify_csrf(): void {

    $stored = $_SESSION['csrf'] ?? null;
    $submitted = $_POST['csrf'] ?? null;

    if (
        !is_string($stored) ||
        $stored === '' ||
        !is_string($submitted) ||
        !hash_equals($stored, $submitted)
    ) {
        http_response_code(419);
        exit('Invalid form submission.');
    }
}
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        redirect('login.php');
    }
}
function now(): string {
    return date('Y-m-d H:i:s');
}

function visit_duration(string $timeIn, ?string $timeOut): string {

    if ($timeOut === null) {
        return 'Ongoing';
    }

    $start = new DateTimeImmutable($timeIn);
    $end = new DateTimeImmutable($timeOut);

    if ($end < $start) {
        return 'Invalid time';
    }

    $seconds = $end->getTimestamp() - $start->getTimestamp();

    $minutes = intdiv($seconds, 60);

    $hours = intdiv($minutes, 60);
    $remainingMinutes = $minutes % 60;

    return $hours . 'h ' . $remainingMinutes . 'm';
}
