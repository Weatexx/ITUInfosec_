<?php
session_start();
require_once 'data_manager.php';

if (!isset($_SESSION['loggedin'])) {
    exit('Yetkisiz erişim');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentData = $dataManager->getApply() ?? [];

    if (isset($_POST['button_url'])) {
        $currentData['button_url'] = $_POST['button_url'];
    }
    if (isset($_POST['contact_url'])) {
        $currentData['contact_url'] = $_POST['contact_url'];
    }

    // Save id = 1 just to mimic old structure if needed, or simply save the object
    $currentData['id'] = 1;

    $dataManager->saveApply($currentData);
    exit; // AJAX call ends here
}

$apply = $dataManager->getApply();
// Defaults if empty
if (!$apply) {
    $apply = ['button_url' => '', 'contact_url' => ''];
}
?>

<style>
    .settings-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .settings-card .card-header {
        background-color: #f8f9fa;
        padding: 15px 20px;
    }

    .settings-card .card-body {
        padding: 20px;
    }

    .url-preview {
        background-color: #f8f9fa;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 0.9rem;
        margin-top: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .form-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>URL Ayarları</h4>
</div>

<div class="card settings-card bg-dark text-white border-secondary">
    <div class="card-header border-secondary bg-dark">
        <h5 class="mb-0">Buton URL Ayarları</h5>
    </div>
    <div class="card-body">
        <form id="applyForm" method="POST">
            <div class="mb-4">
                <label for="button_url" class="form-label">Başvuru Butonu URL'si</label>
                <input type="url" class="form-control" id="button_url" name="button_url"
                    value="<?php echo htmlspecialchars($apply['button_url'] ?? ''); ?>" required>
                <?php if (!empty($apply['button_url'])): ?>
                    <div class="url-preview mt-2 bg-dark"><i class="bi bi-link-45deg"></i>
                        <?php echo htmlspecialchars($apply['button_url']); ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="contact_url" class="form-label">Bize Ulaşın Butonu URL'si</label>
                <input type="url" class="form-control" id="contact_url" name="contact_url"
                    value="<?php echo htmlspecialchars($apply['contact_url'] ?? ''); ?>" required>
                <?php if (!empty($apply['contact_url'])): ?>
                    <div class="url-preview mt-2 bg-dark"><i class="bi bi-link-45deg"></i>
                        <?php echo htmlspecialchars($apply['contact_url']); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Kaydet</button>
                <button type="button" class="btn btn-secondary" id="testUrlsBtn"><i class="bi bi-eye me-1"></i> Test
                    Et</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#applyForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('Kaydediliyor...');

            $.ajax({
                type: 'POST',
                url: 'apply.php',
                data: $(this).serialize(),
                success: function () {
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Kaydet');
                    alert('URL\'ler güncellendi!');
                    loadContent('apply'); // Refresh view
                },
                error: function () {
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Kaydet');
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