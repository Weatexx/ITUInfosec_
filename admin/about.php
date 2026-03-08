<?php
session_start();
require_once 'data_manager.php';

if (!isset($_SESSION['loggedin'])) {
    exit('Yetkisiz erişim');
}

// POST isteği ile güncelleme
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $content = $_POST['content'];

    $data = [
        'title' => $title,
        'subtitle' => $subtitle,
        'content' => $content,
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($dataManager->saveAboutUs($data)) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => 'Hakkımızda bilgileri güncellendi!']);
            exit;
        } else {
            echo "<script>
                alert('Hakkımızda bilgileri güncellendi!');
                window.location.href = 'about.php';
            </script>";
            exit;
        }
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => false, 'message' => 'Kayıt hatası.']);
            exit;
        } else {
            echo "<div class='alert alert-danger'>Kayıt hatası.</div>";
        }
    }
}

$about = $dataManager->getAboutUs();
?>

<style>
    .about-card {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .about-card .card-header {
        background-color: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.125);
        padding: 15px 20px;
    }

    .about-card .card-body {
        padding: 20px;
    }

    .about-content {
        white-space: pre-line;
        line-height: 1.6;
    }

    .edit-form-container {
        /* background removed, handled by bootstrap class */
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        margin-bottom: 20px;
        display: none;
    }

    .edit-form-header {
        background-color: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.125);
        padding: 15px 20px;
    }

    .edit-form-body {
        padding: 20px;
    }

    .form-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .form-buttons {
            flex-direction: column;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Hakkımızda Bilgileri</h4>
</div>

<?php if ($about) { ?>
    <div class="card mb-4 about-card bg-dark text-white border-secondary">
        <div class="card-header">
            <div>
                <h5 class="mb-0"><?php echo htmlspecialchars($about['title']); ?></h5>
                <?php if (!empty($about['subtitle'])) { ?>
                    <small class="text-muted"><?php echo htmlspecialchars($about['subtitle']); ?></small>
                <?php } ?>
            </div>
        </div>
        <div class="card-body">
            <div class="about-content"><?php echo nl2br(htmlspecialchars($about['content'])); ?></div>
            <p class="text-muted small mt-3">Son Güncelleme:
                <?php echo date('d.m.Y H:i', strtotime($about['updated_at'] ?? 'now')); ?>
            </p>
            <div class="text-end mt-3">
                <button class="btn btn-primary" onclick="showEditForm()"><i class="bi bi-pencil-square me-1"></i>
                    Düzenle</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div id="editForm" class="edit-form-container bg-dark text-white border border-secondary"
                style="display: block; margin-top: 0;">
                <div class="edit-form-header">
                    <h5 class="mb-0">Düzenle</h5>
                </div>
                <div class="edit-form-body">
                    <form method="POST" id="aboutForm">
                        <div class="mb-3">
                            <label for="title" class="form-label">Başlık</label>
                            <input type="text" class="form-control" id="title" name="title"
                                value="<?php echo htmlspecialchars($about['title'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="subtitle" class="form-label">Alt Başlık</label>
                            <input type="text" class="form-control" id="subtitle" name="subtitle"
                                value="<?php echo htmlspecialchars($about['subtitle'] ?? ''); ?>">
                            <small class="text-muted">Örnek: "İstanbul Teknik Üniversitesi - ITU Infosec"</small>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">İçerik</label>
                            <textarea class="form-control" id="content" name="content" rows="10"
                                required><?php echo htmlspecialchars($about['content'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-buttons">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4 bg-dark text-white" style="margin-bottom: 20px;">
                <div class="card-header border-secondary">
                    <h5 class="mb-0">Canlı Önizleme</h5>
                </div>
                <div class="card-body text-center">
                    <h1 class="preview-heading" id="preview_title"
                        style="font-size: 2.5rem; background: linear-gradient(90deg, #ffffff, #000000); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.5rem;">
                        <?php echo htmlspecialchars($about['title']); ?>
                    </h1>
                    <div id="preview_subtitle_container">
                        <h1 class="preview-subtitle" id="preview_subtitle"
                            style="font-size: 1.5rem; background: linear-gradient(90deg, #fff, #fff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 1rem;">
                            <?php echo htmlspecialchars($about['subtitle']); ?>
                        </h1>
                    </div>
                    <p class="preview-content" id="preview_content"
                        style="color: rgba(255,255,255,0.61); word-wrap: break-word;">
                        <?php echo nl2br(htmlspecialchars($about['content'])); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="alert alert-info">
        <h5>Veri Bulunamadı</h5>
        <p>Lütfen başlangıç verilerini oluşturun.</p>
    </div>
<?php } ?>

<script>
    function showEditForm() {
        $('#editForm').fadeIn();
        document.getElementById('editForm').scrollIntoView({ behavior: 'smooth' });
    }
    function hideEditForm() {
        $('#editForm').fadeOut();
    }

    $(document).ready(function () {
        const titleInput = $('#title');
        const subtitleInput = $('#subtitle');
        const contentInput = $('#content');

        const previewTitle = $('#preview_title');
        const previewSubtitle = $('#preview_subtitle');
        const previewContent = $('#preview_content');

        function updatePreview() {
            previewTitle.text(titleInput.val());
            previewSubtitle.text(subtitleInput.val());
            // Preserve newlines
            previewContent.html(contentInput.val().replace(/\n/g, '<br>'));
        }

        titleInput.on('input', updatePreview);
        subtitleInput.on('input', updatePreview);
        contentInput.on('input', updatePreview);

        $('#aboutForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('Kaydediliyor...');

            $.ajax({
                type: 'POST',
                url: 'about.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Kaydet');
                    if (res.success) {
                        alert(res.message);
                        // Optional: reload to refresh timestamp, but preview handles visual feedback
                        // loadContent('about'); 
                    } else {
                        alert('Hata: ' + res.message);
                    }
                },
                error: function () {
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Kaydet');
                    alert('Bir hata oluştu.');
                }
            });
        });
    });
</script>