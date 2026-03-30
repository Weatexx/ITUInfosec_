<?php
session_start();
require_once 'config.php';
require_once 'security_utils.php';

// Require secure session
requireSecureSession();

// CSRF kontrol (logout sayfasına erişimde CSRF tokeni kontrol et)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
}

// Session'ı sonlandır
$_SESSION = [];
session_destroy();

// Login sayfasına yönlendir
header('Location: login.php');
exit;
?> 