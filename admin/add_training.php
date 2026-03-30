<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();
requireCsrfToken();

$title = validateText($_POST['title'] ?? '', 255, true);
$description = validateText($_POST['description'] ?? '', 1000, true);
$date = validateText($_POST['date'] ?? '', 10, true);
$time = validateText($_POST['time'] ?? '', 5, false);
$header = validateText($_POST['header'] ?? '', 255, false);

$errors = [];

// Validate required fields
if ($title === null || $title === '') {
    $errors['title'] = 'Eğitim başlığı gerekli';
}

if ($description === null || $description === '') {
    $errors['description'] = 'Açıklama gerekli';
}

if ($date === null || $date === '') {
    $errors['date'] = 'Tarih gerekli';
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
        'title' => $title,
        'description' => $description,
        'date' => $date,
        'time' => $time,
        'header' => $header,
        'photo' => $photoPath,
        'created_at' => date('Y-m-d H:i:s')
    ];

    if ($dataManager->addTraining($data)) {
        echo "<h4 class='text-center text-success'><i class='bi bi-check-circle me-2'></i>Eğitim başarıyla eklendi.</h4>";
        include 'trainings.php';
    } else {
        echo "<h4 class='text-center text-danger'><i class='bi bi-exclamation-circle me-2'></i>Eğitim ekleme işlemi başarısız.</h4>";
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