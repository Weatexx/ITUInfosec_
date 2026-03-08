<?php
require_once 'data_manager.php';

$id = $_POST['id'];

$training = $dataManager->getTraining($id);

if ($training) {
    if (!empty($training['photo']) && file_exists($training['photo'])) {
        unlink($training['photo']);
    }

    if ($dataManager->deleteTraining($id)) {
        echo "<h4 class='text-center text-success'>Eğitim başarıyla silindi.</h4>";
        include 'trainings.php';
    } else {
        echo "<h4 class='text-center text-danger'>Silme işlemi başarısız.</h4>";
    }
} else {
    echo "<h4 class='text-center text-danger'>Eğitim bulunamadı.</h4>";
}
?>