<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();

if (isset($_GET['id'])) {
    $id = validateInteger($_GET['id'] ?? null, 1, PHP_INT_MAX);
    
    if ($id === null) {
        echo '<div class="alert alert-danger">Geçersiz eğitim ID\'si.</div>';
        include 'trainings.php';
        exit;
    }
    
    $training = $dataManager->getTraining($id);

    if ($training) {
        $csrfToken = getCsrfToken();
        ?>
        <div class="form-container">
            <h4 class="form-title">Eğitimi Düzenle</h4>
            <form id="editTrainingForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <input type="hidden" name="id" value="<?php echo $training['id']; ?>">
                <input type="hidden" name="existing_photo" value="<?php echo htmlspecialchars($training['photo']); ?>">
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
        <label for="photo" class="form-label">Fotoğraf (Değiştirmek isterseniz yükleyin)</label>
        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
        <div class="mt-2">
            <img src="<?php echo htmlspecialchars($training['photo']); ?>" alt="Mevcut Fotoğraf"
                style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Güncelle</button>
        <button type="button" class="btn btn-secondary" onclick="loadContent('trainings')"><i class="bi bi-x-circle me-1"></i>İptal</button>
    </div>
    </form>
</div>
<script>
    document.getElementById('editTrainingForm').onsubmit = function (event) {
        event.preventDefault();
        var formData = new FormData(this);

        $('#content-area').append('<div id="loading" class="text-center mt-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Güncelleniyor...</span></div><p>Güncelleniyor...</p></div>');

        $.ajax({
            url: 'update_training.php', // Separate update handler
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#content-area').html(response);
            },
            error: function () {
                $('#content-area').html('<div class="alert alert-danger">Güncelleme işlemi gerçekleştirilemedi.</div>');
            }
        });
    };
</script>
<?php
    } else {
        echo '<div class="alert alert-danger">Eğitim bulunamadı.</div>';
    }
}
?>
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