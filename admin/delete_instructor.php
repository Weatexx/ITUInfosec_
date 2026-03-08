<?php
require_once 'data_manager.php';

$id = $_POST['id'];

// Get instructor to check photo
$instructor = $dataManager->getInstructor($id);

if ($instructor) {
    // Delete photo if exists
    if (!empty($instructor['photo']) && file_exists($instructor['photo'])) {
        unlink($instructor['photo']);
    }

    if ($dataManager->deleteInstructor($id)) {
        echo "<h4 class='text-center text-success'>Eğitmen başarıyla silindi.</h4>";
        include 'instructors.php';
    } else {
        echo "<h4 class='text-center text-danger'>Silme işlemi başarısız.</h4>";
    }
} else {
    echo "<h4 class='text-center text-danger'>Eğitmen bulunamadı.</h4>";
}
?>