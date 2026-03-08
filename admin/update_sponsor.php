<?php
require_once 'data_manager.php';

$id = $_POST['id'];
$title = $_POST['title'];
$description = $_POST['description'];
$expertise = $_POST['expertise'];
$photoPath = $_POST['existing_photo'];

$targetDir = "uploads/";
$uploadOk = 1;

if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Yeni fotoğraf yüklendi mi?
if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
    $targetFile = $targetDir . basename($_FILES["photo"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
        if (file_exists($targetFile)) {
            $targetFile = $targetDir . time() . "_" . basename($_FILES["photo"]["name"]);
        }

        if ($_FILES["photo"]["size"] > 5000000) {
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
}

if ($uploadOk == 1) {
    $data = [
        'title' => $title,
        'description' => $description,
        'expertise' => $expertise,
        'photo' => $photoPath,
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($dataManager->updateSponsor($id, $data)) {
        echo "<h4 class='text-center text-success'>Sponsor başarıyla güncellendi.</h4>";
        include 'sponsors.php';
    } else {
        echo "<h4 class='text-center text-danger'>Güncelleme işlemi başarısız.</h4>";
    }
}
?>