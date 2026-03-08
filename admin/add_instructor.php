<?php
require_once 'data_manager.php';

$name = $_POST['name'];
$expertise = $_POST['expertise'];
$profile_url = $_POST['profile_url'];

$targetDir = "uploads/";
$uploadOk = 1;
$photoPath = "";

if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Dosya yükleme
if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
    $targetFile = $targetDir . basename($_FILES["photo"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
        // Dosya zaten varsa timestamp ekle
        if (file_exists($targetFile)) {
            $targetFile = $targetDir . time() . "_" . basename($_FILES["photo"]["name"]);
        }

        if ($_FILES["photo"]["size"] > 5000000) { // 5MB
            echo "<h4 class='text-center text-danger'>Üzgünüm, dosya çok büyük.</h4>";
            $uploadOk = 0;
        } elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "<h4 class='text-center text-danger'>Üzgünüm, yalnızca JPG, JPEG, PNG ve GIF dosyalarına izin verilmektedir.</h4>";
            $uploadOk = 0;
        } else {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                $photoPath = $targetFile;
            } else {
                echo "<h4 class='text-center text-danger'>Üzgünüm, dosya yüklenemedi.</h4>";
                $uploadOk = 0;
            }
        }
    } else {
        echo "<h4 class='text-center text-danger'>Dosya bir resim değil.</h4>";
        $uploadOk = 0;
    }
} else {
    echo "<h4 class='text-center text-danger'>Fotoğraf yüklenmelidir.</h4>";
    $uploadOk = 0;
}


if ($uploadOk == 1 && !empty($photoPath)) {
    $data = [
        'name' => $name,
        'expertise' => $expertise,
        'profile_url' => $profile_url,
        'photo' => $photoPath
    ];

    if ($dataManager->addInstructor($data)) {
        echo "<h4 class='text-center text-success'>Eğitmen başarıyla eklendi.</h4>";
        include 'instructors.php';
    } else {
        echo "<h4 class='text-center text-danger'>Eğitmen ekleme işlemi başarısız.</h4>";
    }
}
?>