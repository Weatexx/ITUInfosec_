<?php
require_once 'data_manager.php';

$id = isset($_POST['instructor_id']) ? (int) $_POST['instructor_id'] : 0;
$name = $_POST['name'];
$expertise = $_POST['expertise'];
$profile_url = $_POST['profile_url'];

if ($id <= 0) {
    echo "<div class='alert alert-danger'>Geçersiz eğitmen ID'si.</div>";
    exit;
}

$updateData = [
    'name' => $name,
    'expertise' => $expertise,
    'profile_url' => $profile_url
];

// Dosya yükleme
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES["photo"]["name"]);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $uploadOk = 1;

    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check === false) {
        echo "<div class='alert alert-danger'>Dosya bir resim değil.</div>";
        $uploadOk = 0;
    }

    if ($_FILES["photo"]["size"] > 5000000) {
        echo "<div class='alert alert-danger'>Üzgünüm, dosya çok büyük (max: 5MB).</div>";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "<div class='alert alert-danger'>Üzgünüm, yalnızca JPG, JPEG, PNG ve GIF dosyalarına izin verilmektedir.</div>";
        $uploadOk = 0;
    }

    if ($uploadOk != 0 && move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
        $updateData['photo'] = $targetFile;

        // delete old photo maybe? Optional but good practice.
        // We'd need to fetch old data first.
        $oldData = $dataManager->getInstructor($id);
        if ($oldData && !empty($oldData['photo']) && file_exists($oldData['photo'])) {
            unlink($oldData['photo']);
        }

    } else if ($uploadOk != 0) {
        echo "<div class='alert alert-danger'>Üzgünüm, dosya yüklenemedi.</div>";
    }
}

if ($dataManager->updateInstructor($id, $updateData)) {
    echo "<div class='alert alert-success'>Eğitmen başarıyla güncellendi.</div>";
    echo "<script>setTimeout(function() { loadContent('instructors'); }, 1500);</script>";
} else {
    echo "<div class='alert alert-danger'>Güncelleme hatası.</div>";
}
?>