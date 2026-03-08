<?php
require_once 'data_manager.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sponsor = $dataManager->getSponsor($id);

    if ($sponsor) {
        ?>
        <div class="form-container">
            <h4 class="form-title">Sponsor Düzenle</h4>
            <form id="editSponsorForm" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $sponsor['id']; ?>">
                <input type="hidden" name="existing_photo" value="<?php echo htmlspecialchars($sponsor['photo']); ?>">

                <div class="mb-3">
                    <label for="title" class="form-label">Firma Adı</label>
                    <input type="text" class="form-control" id="title" name="title"
                        value="<?php echo htmlspecialchars($sponsor['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama</label>
                    <textarea class="form-control" id="description" name="description" rows="4"
                        required><?php echo htmlspecialchars($sponsor['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="expertise" class="form-label">Uzmanlık Alanı</label>
                    <input type="text" class="form-control" id="expertise" name="expertise"
                        value="<?php echo htmlspecialchars($sponsor['expertise'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Fotoğraf (Değiştirmek isterseniz yükleyin)</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    <div class="mt-2">
                        <img src="<?php echo htmlspecialchars($sponsor['photo']); ?>" alt="Mevcut Fotoğraf"
                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Güncelle</button>
                    <button type="button" class="btn btn-secondary" onclick="loadContent('sponsors')"><i
                            class="bi bi-x-circle me-1"></i>İptal</button>
                </div>
            </form>
        </div>
        <script>
            document.getElementById('editSponsorForm').onsubmit = function (event) {
                event.preventDefault();
                var formData = new FormData(this);

                $('#content-area').append('<div id="loading" class="text-center mt-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Güncelleniyor...</span></div><p>Güncelleniyor...</p></div>');

                $.ajax({
                    url: 'update_sponsor.php', // Separate update handler
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
        echo '<div class="alert alert-danger">Sponsor bulunamadı.</div>';
    }
}
?>