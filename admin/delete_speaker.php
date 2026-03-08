<?php
require_once 'data_manager.php';

$id = $_POST['id'];

if ($dataManager->deleteSpeaker($id)) {
    echo "<h4 class='text-center text-success'>Konuşmacı başarıyla silindi.</h4>";
    // Fotoğrafı da silebiliriz (opsiyonel, şimdilik dataManager sadece veriyi siliyor)
    include 'speakers.php';
} else {
    echo "<h4 class='text-center text-danger'>Silme işlemi başarısız.</h4>";
}
?>