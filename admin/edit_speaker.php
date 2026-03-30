<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();

if (isset($_GET['id'])) {
    $id = validateInteger($_GET['id'] ?? null, 1, PHP_INT_MAX);
    
    if ($id === null) {
        echo '<div class="alert alert-danger">Geçersiz konuşmacı ID\'si.</div>';
        include 'speakers.php';
        exit;
    }
    
    $speaker = $dataManager->getSpeaker($id);

    if ($speaker) {
        $csrfToken = getCsrfToken();
        ?>
        <div class="form-container">
            <h4 class="form-title">Konuşmacı Düzenle</h4>
            <form id="editSpeakerForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <input type="hidden" name="id" value="<?php echo $speaker['id']; ?>">
                <input type="hidden" name="existing_photo" value="<?php echo htmlspecialchars($speaker['photo']); ?>">

                <div class="mb-3">
                    <label for="name" class="form-label">İsim</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="<?php echo htmlspecialchars($speaker['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="expertise" class="form-label">Uzmanlık</label>
                    <input type="text" class="form-control" id="expertise" name="expertise"
                        value="<?php echo htmlspecialchars($speaker['expertise']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="profile_url" class="form-label">Profil URL'si</label>
                    <input type="url" class="form-control" id="profile_url" name="profile_url"
                        value="<?php echo htmlspecialchars($speaker['profile_url'] ?? ''); ?>"
                        placeholder="https://example.com/profile">
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Fotoğraf (Değiştirmek isterseniz yükleyin)</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    <div class="mt-2">
                        <img src="<?php echo htmlspecialchars($speaker['photo']); ?>" alt="Mevcut Fotoğraf"
                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Güncelle</button>
                    <button type="button" class="btn btn-secondary" onclick="loadContent('speakers')"><i
                            class="bi bi-x-circle me-1"></i>İptal</button>
                </div>
            </form>
        </div>
        <script>
            document.getElementById('editSpeakerForm').onsubmit = function (event) {
                event.preventDefault();
                var formData = new FormData(this);

                $('#content-area').append('<div id="loading" class="text-center mt-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Güncelleniyor...</span></div><p>Güncelleniyor...</p></div>');

                $.ajax({
                    url: 'update_speaker.php', // Separate update handler
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
        echo '<div class="alert alert-danger">Konuşmacı bulunamadı.</div>';
    }
}
?>