<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();
requireCsrfToken();

$id = validateInteger($_POST['id'] ?? null, 1, PHP_INT_MAX);
$name = validateText($_POST['name'] ?? '', 255, true);
$expertise = validateText($_POST['expertise'] ?? '', 255, true);
$profile_url = validateUrl($_POST['profile_url'] ?? '', false);
$existingPhoto = $_POST['existing_photo'] ?? '';

$errors = [];

if ($id === null) {
    $errors['id'] = 'Geçersiz konuşmacı ID\'si';
}

if ($name === null || $name === '') {
    $errors['name'] = 'İsim gerekli';
}

if ($expertise === null || $expertise === '') {
    $errors['expertise'] = 'Uzmanlık gerekli';
}

if (!empty($_POST['profile_url']) && $profile_url === null) {
    $errors['profile_url'] = 'Geçersiz URL';
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
        'name' => $name,
        'expertise' => $expertise,
        'profile_url' => $profile_url ?? '',
        'photo' => $photoPath
    ];

    if ($dataManager->updateSpeaker($id, $data)) {
        echo "<h4 class='text-center text-success'><i class='bi bi-check-circle me-2'></i>Konuşmacı başarıyla güncellendi.</h4>";
        include 'speakers.php';
    } else {
        echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Güncelleme işlemi başarısız.</h4>";
        include 'speakers.php';
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
    include 'speakers.php';
}
?>
