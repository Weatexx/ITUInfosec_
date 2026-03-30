<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();
requireCsrfToken();

$id = validateInteger($_POST['id'] ?? null, 1, PHP_INT_MAX);

if ($id === null) {
    echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Geçersiz eğitmen ID'si.</h4>";
    include 'instructors.php';
    exit;
}

// Get instructor to check photo
$instructor = $dataManager->getInstructor($id);

if ($instructor) {
    // Delete photo if exists
    if (!empty($instructor['photo']) && file_exists($instructor['photo'])) {
        @unlink($instructor['photo']);
    }

    if ($dataManager->deleteInstructor($id)) {
        echo "<h4 class='text-center text-success'><i class='bi bi-check-circle me-2'></i>Eğitmen başarıyla silindi.</h4>";
        include 'instructors.php';
    } else {
        echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Silme işlemi başarısız.</h4>";
        include 'instructors.php';
    }
} else {
    echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Eğitmen bulunamadı.</h4>";
    include 'instructors.php';
}
?>
