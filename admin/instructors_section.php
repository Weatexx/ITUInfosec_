<?php
session_start();
require_once 'data_manager.php';

if (!isset($_SESSION['loggedin'])) {
    exit('Yetkisiz erişim');
}

// Handle AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'updated_at' => date('Y-m-d H:i:s')
    ];

    $dataManager->saveInstructorsSection($data);

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => true, 'message' => 'Eğitmenler bölümü güncellendi!']);
        exit;
    }
}

// Get current data
$instructorsSection = $dataManager->getInstructorsSection();
// Defaults
if (!$instructorsSection) {
    $instructorsSection = [
        'title' => 'Eğitmenlerimiz',
        'description' => 'Alanında uzman eğitmenlerimiz...'
    ];
}
?>

<style>
    .preview-card {
        background-color: #212529;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
    }

    .preview-title {
        font-size: 4rem;
        background: linear-gradient(90deg, #ffffff, #000000);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1.5rem;
    }

    .preview-description {
        color: rgba(255, 255, 255, 0.61);
        font-size: 1rem;
    }

    /* Small screens preview adjustment */
    @media (max-width: 768px) {
        .preview-title {
            font-size: 2.5rem;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Eğitmenler Bölümü Ayarları</h4>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">İçerik Düzenle</h5>
            </div>
            <div class="card-body">
                <form id="instructorsSectionForm">
                    <div class="mb-3">
                        <label for="title" class="form-label">Bölüm Başlığı</label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="<?php echo htmlspecialchars($instructorsSection['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                            required><?php echo htmlspecialchars($instructorsSection['description']); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Güncelle</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4 bg-dark text-white">
            <div class="card-header border-secondary">
                <h5 class="mb-0">Canlı Önizleme</h5>
            </div>
            <div class="card-body">
                <div class="preview-card">
                    <h1 class="preview-title" id="preview_title">
                        <?php echo htmlspecialchars($instructorsSection['title']); ?>
                    </h1>
                    <p class="preview-description" id="preview_description">
                        <?php echo nl2br(htmlspecialchars($instructorsSection['description'])); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const titleInput = $('#title');
        const descInput = $('#description');
        const previewTitle = $('#preview_title');
        const previewDesc = $('#preview_description');

        function updatePreview() {
            previewTitle.text(titleInput.val());
            // Preserve newlines in description preview
            previewDesc.html(descInput.val().replace(/\n/g, '<br>'));
        }

        titleInput.on('input', updatePreview);
        descInput.on('input', updatePreview);

        $('#instructorsSectionForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('Güncelleniyor...');

            $.ajax({
                url: 'instructors_section.php', // Self
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Güncelle');
                    if (res.success) {
                        alert(res.message);
                    } else {
                        alert('Bir hata oluştu.');
                    }
                },
                error: function () {
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Güncelle');
                    alert('Bir hata oluştu.');
                }
            });
        });
    });
</script>