<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isAjax) {
        requireCsrfToken();
        
        header('Content-Type: application/json; charset=utf-8');
        
        $title = validateText($_POST['title'] ?? '', 255, true);
        $description = validateText($_POST['description'] ?? '', 1000, true);
        
        if ($title === null || $title === '') {
            echo json_encode(['success' => false, 'message' => 'Başlık gerekli']);
            exit;
        }
        
        if ($description === null || $description === '') {
            echo json_encode(['success' => false, 'message' => 'Açıklama gerekli']);
            exit;
        }
        
        $data = [
            'title' => $title,
            'description' => $description,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($dataManager->saveSponsorsSection($data)) {
            echo json_encode(['success' => true, 'message' => 'Başarıyla güncellendi!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Güncelleme başarısız']);
        }
        exit;
    }
}

$section = $dataManager->getSponsorsSection();
$csrfToken = getCsrfToken();
// Defaults
if (!$section) {
    $section = ['title' => 'Sponsorlarımız', 'description' => 'Değerli sponsorlarımız...'];
}
?>

<style>
    .section-info-header {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 15px;
    }

    .section-info-header .info-section h5 {
        margin-bottom: 5px;
        font-weight: 600;
    }

    .form-container-section {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        color: #fff;
    }

    .form-container-section .form-title {
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .preview-card-section {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 30px 20px;
        margin-bottom: 20px;
        text-align: center;
        min-height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .preview-heading-section {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(90deg, #ffffff, #00f2ea);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 15px;
        line-height: 1.3;
    }

    .preview-text-section {
        color: rgba(255, 255, 255, 0.7);
        font-size: 1rem;
        line-height: 1.6;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    @media (max-width: 992px) {
        .section-info-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .preview-card-section {
            margin-top: 20px;
        }
    }

    @media (max-width: 576px) {
        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }

        .preview-heading-section {
            font-size: 1.8rem;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 style="margin: 0;">Sponsorlar Bölümü Ayarları</h4>
</div>

<div class="section-info-header">
    <div class="info-section">
        <h5>Sponsorlar Bölümü</h5>
        <small class="text-muted">
            <i class="bi bi-calendar-event me-1"></i>
            Son Güncelleme: <?php echo isset($section['updated_at']) ? date('d.m.Y H:i', strtotime($section['updated_at'])) : 'Bilinmiyor'; ?>
        </small>
    </div>
    <button class="btn btn-primary btn-sm" onclick="scrollToSponsorsForm()">
        <i class="bi bi-pencil-square me-1"></i> Düzenle
    </button>
</div>

<div class="row">
    <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="form-container-section">
            <div class="form-title">Düzenle</div>
            <form id="sponsorsSectionForm" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Bölüm Başlığı <span style="color: #ff6b6b;">*</span></label>
                    <input type="text" class="form-control" id="title" name="title"
                        value="<?php echo htmlspecialchars($section['title'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama <span style="color: #ff6b6b;">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="6" required><?php echo htmlspecialchars($section['description'] ?? ''); ?></textarea>
                    <small class="text-muted d-block mt-1">Sayfada gösterilecek açıklama metni</small>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success" id="saveBtn">
                        <i class="bi bi-check-circle me-1"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="preview-card-section">
            <h2 class="preview-heading-section" id="preview_title"><?php echo htmlspecialchars($section['title'] ?? ''); ?></h2>
            <p class="preview-text-section" id="preview_description"><?php echo nl2br(htmlspecialchars($section['description'] ?? '')); ?></p>
        </div>
        <small class="text-muted d-block text-center">Canlı Önizleme</small>
    </div>
</div>

<script>
    function scrollToSponsorsForm() {
        document.querySelector('.form-container-section').scrollIntoView({ behavior: 'smooth' });
    }

    $(document).ready(function () {
        function updatePreview() {
            let title = $('#title').val();
            let desc = $('#description').val();
            $('#preview_title').text(title);
            $('#preview_description').html(desc.replace(/\n/g, '<br>'));
        }

        $('#title, #description').on('input', updatePreview);
        updatePreview();

        $('#sponsorsSectionForm').on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#saveBtn');
            $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Kaydediliyor...');

            $.ajax({
                url: 'sponsors_section.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Kaydet');
                    if (res.success) {
                        alert(res.message);
                        loadContent('sponsors_section');
                    } else {
                        alert(res.message);
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Kaydet');
                    alert('Hata oluştu.');
                }
            });
        });
    });
</script>