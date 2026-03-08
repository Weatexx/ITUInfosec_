<?php
require_once 'data_manager.php';

$id = $_GET['id'];
$training = $dataManager->getTraining($id);

if (!$training) {
    echo "<h4 class='text-center text-danger'>Eğitim bulunamadı.</h4>";
    exit;
}
?>

<h4>Eğitimi Düzenle</h4>
<form id="editTrainingForm" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $training['id']; ?>">
    <div class="mb-3">
        <label for="title" class="form-label">Başlık</label>
        <input type="text" class="form-control" id="title" name="title"
            value="<?php echo htmlspecialchars($training['title']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Açıklama</label>
        <textarea class="form-control" id="description" name="description"
            required><?php echo htmlspecialchars($training['description']); ?></textarea>
    </div>
    <div class="mb-3">
        <label for="date" class="form-label">Tarih</label>
        <input type="date" class="form-control" id="date" name="date" value="<?php echo $training['date']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="time" class="form-label">Saat</label>
        <input type="text" class="form-control" id="time" name="time"
            value="<?php echo isset($training['time']) ? htmlspecialchars($training['time']) : ''; ?>"
            placeholder="Örn: 14:00 veya boş bırakabilirsiniz">
    </div>
    <div class="mb-3">
        <label for="header" class="form-label">Header</label>
        <input type="text" class="form-control" id="header" name="header"
            value="<?php echo htmlspecialchars($training['header'] ?? ''); ?>">
    </div>
    <div class="mb-3">
        <label for="photo" class="form-label">Yeni Fotoğraf Yükle</label>
        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
        <?php if (!empty($training['photo'])): ?>
            <small class="form-text text-muted">Mevcut fotoğraf: <img
                    src="<?php echo htmlspecialchars($training['photo']); ?>" alt="Eğitim Fotoğrafı"
                    style="width: 50px; height: auto;"></small>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Güncelle</button>
    <button type="button" class="btn btn-secondary" onclick="loadContent('trainings')">İptal</button>
</form>

<script>
    document.getElementById('editTrainingForm').onsubmit = function (event) {
        event.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'update_training.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#content-area').html(response);
            },
            error: function () {
                $('#content-area').html('<div class="alert alert-danger">Düzenleme formu yüklenemedi.</div>');
            }
        });
    };
</script>