<?php
session_start();
require_once 'data_manager.php';

if (!isset($_SESSION['loggedin'])) {
    echo '<div class="alert alert-danger">Giriş yapmalısınız!</div>';
    exit;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description']
        ];

        if ($dataManager->saveTrainingsSection($data)) {
            echo json_encode(['success' => true, 'message' => 'Başarıyla güncellendi!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Güncelleme başarısız']);
        }
        exit;
    }
}

$section = $dataManager->getTrainingsSection();
// Defaults
if (!$section) {
    $section = ['title' => 'Eğitimlerimiz', 'description' => 'Siber güvenlik eğitimlerimiz.'];
}
?>

<style>
    .preview-card {
        background-color: #212529;
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .preview-title {
        color: white !important;
        margin-bottom: 0.75rem;
        font-size: 2rem;
    }

    .preview-description {
        color: rgba(255, 255, 255, 0.61) !important;
        font-size: 1.1rem;
    }
</style>

<div class="form-container">
    <h4 class="form-title">Eğitimler Bölümü Başlık ve Açıklama Düzenle</h4>

    <form id="trainingsSectionForm" autocomplete="off">
        <div class="mb-3">
            <label for="title" class="form-label">Başlık</label>
            <input type="text" class="form-control" id="title" name="title"
                value="<?php echo htmlspecialchars($section['title'] ?? ''); ?>" required>
            <small class="text-muted">Orta tire (-) satır başı yapar.</small>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Açıklama</label>
            <textarea class="form-control" id="description" name="description" rows="4"
                required><?php echo htmlspecialchars($section['description'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary" id="saveBtn">Kaydet</button>
    </form>

    <div class="mt-4">
        <h5>Önizleme</h5>
        <div class="preview-card">
            <h3 class="preview-title mb-2" id="preview_title"></h3>
            <p class="preview-description mb-0" id="preview_description"></p>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        function updatePreview() {
            let title = $('#title').val();
            let desc = $('#description').val();
            $('#preview_title').html(title.replace(/ - /g, '<br>'));
            $('#preview_description').html(desc.replace(/ - /g, '<br class="d-none d-md-inline">'));
        }

        $('#title, #description').on('input', updatePreview);
        updatePreview();

        $('#trainingsSectionForm').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#saveBtn');
            $btn.prop('disabled', true).html('Kaydediliyor...');

            $.ajax({
                url: 'trainings_section.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).html('Kaydet');
                    if (res.success) {
                        alert(res.message);
                    } else {
                        alert(res.message);
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html('Kaydet');
                    alert('Hata oluştu.');
                }
            });
        });
    });
</script>