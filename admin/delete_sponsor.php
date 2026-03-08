<?php
require_once 'data_manager.php';

$id = $_POST['id'];

if ($dataManager->deleteSponsor($id)) {
    echo "<h4 class='text-center text-success'>Sponsor başarıyla silindi.</h4>";
    include 'sponsors.php';
} else {
    echo "<h4 class='text-center text-danger'>Silme işlemi başarısız.</h4>";
}
?>