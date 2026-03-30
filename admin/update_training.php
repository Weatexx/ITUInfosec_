<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();
requireCsrfToken();

$id = validateInteger($_POST['id'] ?? null, 1, PHP_INT_MAX);
$title = validateText($_POST['title'] ?? '', 255, true);
$description = validateText($_POST['description'] ?? '', 1000, true);
$date = validateText($_POST['date'] ?? '', 10, true);
$time = validateText($_POST['time'] ?? '', 5, false);
$header = validateText($_POST['header'] ?? '', 255, false);
$existingPhoto = $_POST['existing_photo'] ?? '';

$errors = [];

if ($id === null) {
    $errors['id'] = 'Geçersiz eğitim ID\'si';
}

if ($title === null || $title === '') {
    $errors['title'] = 'Eğitim başlığı gerekli';
}

if ($description === null || $description === '') {
    $errors['description'] = 'Açıklama gerekli';
}

if ($date === null || $date === '') {
    $errors['date'] = 'Tarih gerekli';
}

$photoPath = $existingPhoto;

// Process new photo if uploaded
if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] !== UPLOAD_ERR_NO_FILE) {
    $photoPath = processImageUpload($_FILES["photo"], 'uploads/', 5242880);
    if ($photoPath === null) {
        $errors['photo'] = 'Fotoğraf yüklenemedi. Dosya bir resim olmalı (JPG, PNG, GIF, WebP), 5MB\'dan küçük olmalıdır.';
    }
}

if (empty($errors)) {
    $updateData = [
        'title' => $title,
        'description' => $description,
        'date' => $date,
        'time' => $time,
        'header' => $header,
        'photo' => $photoPath,
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($dataManager->updateTraining($id, $updateData)) {
        echo "<h4 class='text-center text-success'><i class='bi bi-check-circle me-2'></i>Eğitim başarıyla güncellendi.</h4>";
        include 'trainings.php';
    } else {
        echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Güncelleme işlemi başarısız.</h4>";
        include 'trainings.php';
    }
} else {
    echo "<div class='alert alert-danger'>";
    echo "<h5>Lütfen hataları düzeltiniz:</h5>";
    echo "<ul>";
    foreach ($errors as $field => $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
    echo "</div>";
    include 'trainings.php';
}
?>