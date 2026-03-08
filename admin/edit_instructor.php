<?php
require_once 'data_manager.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<h4 class='text-center text-danger'>Geçersiz eğitmen ID'si.</h4>";
    exit;
}

$id = (int) $_GET['id'];
$instructor = $dataManager->getInstructor($id);

if (!$instructor) {
    echo "<h4 class='text-center text-danger'>Eğitmen bulunamadı.</h4>";
    exit;
}
?>

<h4>Eğitmen Düzenle</h4>
<form id="editInstructorForm" enctype="multipart/form-data" method="post">
    <input type="hidden" name="instructor_id" value="<?php echo $instructor['id']; ?>">

    <div class="mb-3">
        <label for="name" class="form-label">İsim</label>
        <input type="text" class="form-control" id="name" name="name"
            value="<?php echo htmlspecialchars($instructor['name']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="expertise" class="form-label">Uzmanlık</label>
        <input type="text" class="form-control" id="expertise" name="expertise"
            value="<?php echo htmlspecialchars($instructor['expertise']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="profile_url" class="form-label">Profil URL'si</label>
        <input type="url" class="form-control" id="profile_url" name="profile_url"
            value="<?php echo htmlspecialchars($instructor['profile_url'] ?? ''); ?>"
            placeholder="https://example.com/profile">
        <small class="form-text text-muted">"Profili Görüntüle" butonuna tıklandığında açılacak URL'yi girin.</small>
    </div>
    <div class="mb-3">
        <label for="photo" class="form-label">Mevcut Fotoğraf</label>
        <div class="mb-2">
            <?php if (!empty($instructor['photo'])): ?>
                <img src="<?php echo htmlspecialchars($instructor['photo']); ?>" alt="Eğitmen Fotoğrafı"
                    style="width: 100px; height: auto;">
            <?php endif; ?>
        </div>
        <label for="photo" class="form-label">Yeni Fotoğraf Yükle (Değiştirmek istemiyorsanız boş bırakın)</label>
        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary">Güncelle</button>
    <button type="button" class="btn btn-secondary" onclick="loadContent('instructors')">İptal</button>
</form>

<script>
    document.getElementById('editInstructorForm').onsubmit = function (event) {
        event.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'update_instructor.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#content-area').html(response);
            },
            error: function () {
                $('#content-area').html('<h4 class="text-center text-danger">Güncelleme işlemi gerçekleştirilemedi.</h4>');
            }
        });
    };
</script>