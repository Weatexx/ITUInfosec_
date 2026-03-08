<?php
require_once 'data_manager.php';

$title = $_POST['title'];
$description = $_POST['description'];
$date = $_POST['date'];
$time = isset($_POST['time']) ? $_POST['time'] : '';
$header = isset($_POST['header']) ? $_POST['header'] : '';

$targetDir = "uploads/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Dosya yükleme
$targetFile = $targetDir . basename($_FILES["photo"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

// Dosya zaten varsa timestamp ekle
if (file_exists($targetFile)) {
    $targetFile = $targetDir . time() . "_" . basename($_FILES["photo"]["name"]);
}

if ($_FILES["photo"]["size"] > 5000000) {
    echo "<h4 class='text-center text-danger'>Üzgünüm, dosya çok büyük.</h4>";
    $uploadOk = 0;
}

if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    echo "<h4 class='text-center text-danger'>Üzgünüm, yalnızca JPG, JPEG, PNG ve GIF dosyalarına izin verilmektedir.</h4>";
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    echo "<h4 class='text-center text-danger'>Üzgünüm, dosya yüklenemedi.</h4>";
} else {
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
        $photoPath = $targetFile;

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
            echo "<h4 class='text-center text-success'>Eğitim başarıyla eklendi.</h4>";
            include 'trainings.php';
        } else {
            echo "<h4 class='text-center text-danger'>Eğitim ekleme işlemi başarısız.</h4>";
        }
    } else {
        echo "<h4 class='text-center text-danger'>Üzgünüm, dosya yüklenemedi.</h4>";
    }
}
?>