<?php
require_once 'data_manager.php';

$id = $_POST['id'];
$title = $_POST['title'];
$description = $_POST['description'];
$date = $_POST['date'];
$time = isset($_POST['time']) ? $_POST['time'] : '';
$header = $_POST['header'];

$updateData = [
    'title' => $title,
    'description' => $description,
    'date' => $date,
    'time' => $time,
    'header' => $header
];

// Dosya yükleme
if (isset($_FILES['photo']) && $_FILES['photo']['name']) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES["photo"]["name"]);
    $targetFile = $targetDir . $fileName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check === false) {
        $uploadOk = 0;
        echo "<h4 class='text-center text-danger'>Dosya bir resim değil.</h4>";
    }

    if ($_FILES["photo"]["size"] > 5000000) {
        $uploadOk = 0;
        echo "<h4 class='text-center text-danger'>Dosya çok büyük.</h4>";
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $uploadOk = 0;
        echo "<h4 class='text-center text-danger'>Geçersiz dosya formatı.</h4>";
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $updateData['photo'] = $targetFile;

            // Old photo cleanup if you want
            $oldData = $dataManager->getTraining($id);
            if ($oldData && !empty($oldData['photo']) && file_exists($oldData['photo'])) {
                unlink($oldData['photo']);
            }
        } else {
            echo "<h4 class='text-center text-danger'>Dosya yüklenemedi.</h4>";
        }
    }
}

if ($dataManager->updateTraining($id, $updateData)) {
    echo "<h4 class='text-center text-success'>Eğitim başarıyla güncellendi.</h4>";
    include 'trainings.php';
} else {
    echo "<h4 class='text-center text-danger'>Güncelleme işlemi başarısız.</h4>";
}
?>