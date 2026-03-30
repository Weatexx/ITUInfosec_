<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();
requireCsrfToken();

$id = validateInteger($_POST['id'] ?? null, 1, PHP_INT_MAX);

if ($id === null) {
    echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Geçersiz sponsor ID\'si.</h4>";
    include 'sponsors.php';
    exit;
}

if ($dataManager->deleteSponsor($id)) {
    echo "<h4 class='text-center text-success'><i class='bi bi-check-circle me-2'></i>Sponsor başarıyla silindi.</h4>";
    include 'sponsors.php';
} else {
    echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Silme işlemi başarısız.</h4>";
    include 'sponsors.php';
}
?>