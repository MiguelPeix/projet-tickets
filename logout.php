<?php
// logout.php
require_once __DIR__ . '/includes/auth.php';

session_destroy();
header('Location: /projet-tickets/login.php');
exit;
