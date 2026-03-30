<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();
requireCsrfToken();

$name = validateText($_POST['name'] ?? '', 255, true);
$expertise = validateText($_POST['expertise'] ?? '', 255, true);
$profile_url = validateUrl($_POST['profile_url'] ?? '', false);

$errors = [];

// Validate required fields
if ($name === null || $name === '') {
    $errors['name'] = 'İsim gerekli';
}

if ($expertise === null || $expertise === '') {
    $errors['expertise'] = 'Uzmanlık gerekli';
}

if (!empty($_POST['profile_url']) && $profile_url === null) {
    $errors['profile_url'] = 'Geçersiz URL';
}

$photoPath = null;

if (isset($_FILES["photo"])) {
    if ($_FILES["photo"]["error"] == UPLOAD_ERR_NO_FILE) {
        $errors['photo'] = 'Fotoğraf yüklenmelidir.';
    } else {
        $photoPath = processImageUpload($_FILES["photo"], 'uploads/', 5242880);
        if ($photoPath === null) {
            $errors['photo'] = 'Fotoğraf yüklenemedi. Dosya bir resim olmalı (JPG, PNG, GIF, WebP), 5MB\'dan küçük olmalıdır.';
        }
    }
} else {
    $errors['photo'] = 'Fotoğraf yüklenmelidir.';
}

if (empty($errors)) {
    $data = [
        'name' => $name,
        'expertise' => $expertise,
        'profile_url' => $profile_url ?? '',
        'photo' => $photoPath
    ];

    if ($dataManager->addInstructor($data)) {
        echo "<h4 class='text-center text-success'><i class='bi bi-check-circle me-2'></i>Eğitmen başarıyla eklendi.</h4>";
        include 'instructors.php';
    } else {
        echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Eğitmen ekleme işlemi başarısız.</h4>";
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
    include 'instructors.php';
}
?>
