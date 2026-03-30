<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();
requireCsrfToken();

$id = validateInteger($_POST['id'] ?? null, 1, PHP_INT_MAX);
$title = validateText($_POST['title'] ?? '', 255, true);
$description = validateText($_POST['description'] ?? '', 1000, true);
$expertise = validateText($_POST['expertise'] ?? '', 255, true);
$existingPhoto = $_POST['existing_photo'] ?? '';

$errors = [];

if ($id === null) {
    $errors['id'] = 'Geçersiz sponsor ID\'si';
}

if ($title === null || $title === '') {
    $errors['title'] = 'Firma adı gerekli';
}

if ($description === null || $description === '') {
    $errors['description'] = 'Açıklama gerekli';
}

if ($expertise === null || $expertise === '') {
    $errors['expertise'] = 'Uzmanlık alanı gerekli';
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
    $data = [
        'title' => $title,
        'description' => $description,
        'expertise' => $expertise,
        'photo' => $photoPath,
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($dataManager->updateSponsor($id, $data)) {
        echo "<h4 class='text-center text-success'><i class='bi bi-check-circle me-2'></i>Sponsor başarıyla güncellendi.</h4>";
        include 'sponsors.php';
    } else {
        echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Güncelleme işlemi başarısız.</h4>";
        include 'sponsors.php';
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
    include 'sponsors.php';
}
?>