<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();
requireCsrfToken();

$id = validateInteger($_POST['id'] ?? null, 1, PHP_INT_MAX);

if ($id === null) {
    echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Geçersiz eğitim ID\'si.</h4>";
    include 'trainings.php';
    exit;
}

$training = $dataManager->getTraining($id);

if ($training) {
    if (!empty($training['photo']) && file_exists($training['photo'])) {
        unlink($training['photo']);
    }

    if ($dataManager->deleteTraining($id)) {
        echo "<h4 class='text-center text-success'><i class='bi bi-check-circle me-2'></i>Eğitim başarıyla silindi.</h4>";
        include 'trainings.php';
    } else {
        echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Silme işlemi başarısız.</h4>";
        include 'trainings.php';
    }
} else {
    echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Eğitim bulunamadı.</h4>";
    include 'trainings.php';
}
?>