<?php

require __DIR__ . '/includes/bootstrap.php';

require_login();

$_SESSION = [];

session_destroy();

redirect('login.php');