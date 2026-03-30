<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    
    $currentData = $dataManager->getApply() ?? [];

    if (isset($_POST['button_url'])) {
        $buttonUrl = validateUrl($_POST['button_url'] ?? '', true);
        if ($buttonUrl === null) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz URL format']);
            exit;
        }
        $currentData['button_url'] = $buttonUrl;
    }
    if (isset($_POST['contact_url'])) {
        $contactUrl = validateUrl($_POST['contact_url'] ?? '', true);
        if ($contactUrl === null) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz URL format']);
            exit;
        }
        $currentData['contact_url'] = $contactUrl;
    }

    // Save id = 1 just to mimic old structure if needed, or simply save the object
    $currentData['id'] = 1;

    $dataManager->saveApply($currentData);
    echo json_encode(['success' => true, 'message' => 'URL\'ler güncellendi!']);
    exit;
}

$apply = $dataManager->getApply();
$csrfToken = getCsrfToken();
// Defaults if empty
if (!$apply) {
    $apply = ['button_url' => '', 'contact_url' => ''];
}
?>

<style>
    .settings-info-header {
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

    .settings-info-header .info-section h5 {
        margin-bottom: 5px;
        font-weight: 600;
    }

    .form-container-settings {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        color: #fff;
    }

    .form-container-settings .form-title {
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .url-preview {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 10px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        margin-top: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: rgba(255, 255, 255, 0.7);
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .info-box {
        background: rgba(0, 242, 234, 0.05);
        border-left: 4px solid var(--primary-color);
        padding: 15px;
        border-radius: 5px;
        margin-top: 20px;
    }

    .info-box small {
        color: rgba(255, 255, 255, 0.7);
    }

    @media (max-width: 576px) {
        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }

        .settings-info-header {
            padding: 15px;
        }

        .form-container-settings {
            padding: 15px;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 style="margin: 0;">Link & Buton Ayarları</h4>
</div>

<div class="settings-info-header">
    <div class="info-section">
        <h5>Sayfa Butonları</h5>
        <small class="text-muted">
            <i class="bi bi-exclamation-circle me-1"></i>
            Başvuru ve İletişim butonlarının URL'lerini yönetin
        </small>
    </div>
    <button class="btn btn-primary btn-sm" onclick="scrollToSettingsForm()">
        <i class="bi bi-pencil-square me-1"></i> Düzenle
    </button>
</div>

<div class="form-container-settings">
    <div class="form-title">URL Ayarları</div>
    <form id="applyForm" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <div class="mb-4">
            <label for="button_url" class="form-label">Başvuru Butonu URL'si <span style="color: #ff6b6b;">*</span></label>
            <input type="url" class="form-control" id="button_url" name="button_url"
                value="<?php echo htmlspecialchars($apply['button_url'] ?? ''); ?>" 
                placeholder="https://example.com/apply" required>
            <?php if (!empty($apply['button_url'])): ?>
                <div class="url-preview">
                    <i class="bi bi-link-45deg me-1"></i><?php echo htmlspecialchars($apply['button_url']); ?>
                </div>
            <?php endif; ?>
            <small class="text-muted d-block mt-1">Başvuru butonunun yönlendirileceği URL</small>
        </div>

        <div class="mb-4">
            <label for="contact_url" class="form-label">İletişim Butonu URL'si <span style="color: #ff6b6b;">*</span></label>
            <input type="url" class="form-control" id="contact_url" name="contact_url"
                value="<?php echo htmlspecialchars($apply['contact_url'] ?? ''); ?>" 
                placeholder="https://example.com/contact" required>
            <?php if (!empty($apply['contact_url'])): ?>
                <div class="url-preview">
                    <i class="bi bi-link-45deg me-1"></i><?php echo htmlspecialchars($apply['contact_url']); ?>
                </div>
            <?php endif; ?>
            <small class="text-muted d-block mt-1">İletişim butonunun yönlendirileceği URL</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i> Kaydet
            </button>
            <button type="button" class="btn btn-outline-secondary" id="testUrlsBtn">
                <i class="bi bi-box-arrow-up-right me-1"></i> URL'leri Test Et
            </button>
        </div>
    </form>

    <div class="info-box">
        <small>
            <i class="bi bi-info-circle me-1"></i>
            <strong>Not:</strong> Butonlar yeni sekmede açılacak. Harici bir linke veya form URL'sine yönlendirebilirsiniz.
        </small>
    </div>
</div>

<script>
    function scrollToSettingsForm() {
        document.querySelector('.form-container-settings').scrollIntoView({ behavior: 'smooth' });
    }

    $(document).ready(function () {
        $('#applyForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Kaydediliyor...');

            $.ajax({
                type: 'POST',
                url: 'apply.php',
                data: $(this).serialize(),
                success: function () {
                    btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Kaydet');
                    alert('URL\'ler başarıyla güncellendi!');
                    loadContent('apply');
                },
                error: function () {
                    btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Kaydet');
                    alert('Hata oluştu.');
                }
            });
        });

        $('#testUrlsBtn').on('click', function () {
            const bUrl = $('#button_url').val();
            const cUrl = $('#contact_url').val();
            if (bUrl) window.open(bUrl, '_blank');
            if (cUrl) setTimeout(() => window.open(cUrl, '_blank'), 500);
        });
    });
</script>