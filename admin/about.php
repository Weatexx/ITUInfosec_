<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();

// POST isteği ile güncelleme
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    requireCsrfToken();
    
    $title = validateText($_POST['title'] ?? '', 255, true);
    $subtitle = validateText($_POST['subtitle'] ?? '', 255, true);
    $content = validateText($_POST['content'] ?? '', 5000, true);
    
    $errors = [];
    if ($title === null || $title === '') {
        $errors[] = 'Başlık gerekli';
    }
    if ($subtitle === null || $subtitle === '') {
        $errors[] = 'Alt başlık gerekli';
    }
    if ($content === null || $content === '') {
        $errors[] = 'İçerik gerekli';
    }
    
    if (empty($errors)) {
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
                    window.location.href = 'dashboard.php?page=about';
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
    } else {
        echo "<div class='alert alert-danger'><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul></div>";
    }
}

$about = $dataManager->getAboutUs();
$csrfToken = getCsrfToken();
?>

<style>
    .about-info-header {
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

    .about-info-header .info-section h5 {
        margin-bottom: 5px;
        font-weight: 600;
    }

    .about-info-header .info-section .text-muted {
        font-size: 0.85rem;
    }

    .form-container-about {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        color: #fff;
    }

    .form-container-about .form-title {
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .preview-card-about {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .preview-content-about {
        text-align: center;
        min-height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .preview-heading-about {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
        background: linear-gradient(90deg, #ffffff, #00f2ea);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .preview-subtitle-about {
        font-size: 1.2rem;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .preview-text-about {
        color: rgba(255,255,255,0.7);
        line-height: 1.6;
        word-wrap: break-word;
    }

    .alert-custom {
        background: rgba(255, 193, 7, 0.1);
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    @media (max-width: 992px) {
        .about-info-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .preview-card-about {
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

        .about-info-header {
            padding: 15px;
        }

        .form-container-about {
            padding: 15px;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 style="margin: 0;">Hakkımızda İçeriği</h4>
</div>

<?php if ($about) { ?>
    <div class="about-info-header">
        <div class="info-section">
            <h5><?php echo htmlspecialchars($about['title']); ?></h5>
            <small class="text-muted">
                <i class="bi bi-calendar-event me-1"></i>
                Güncelleme: <?php echo date('d.m.Y H:i', strtotime($about['updated_at'] ?? 'now')); ?>
            </small>
        </div>
        <button class="btn btn-primary btn-sm" onclick="showAboutEditForm()">
            <i class="bi bi-pencil-square me-1"></i> Düzenle
        </button>
    </div>

    <div class="row" style="display: none;" id="aboutEditContainer">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="form-container-about">
                <div class="form-title">Düzenle</div>
                <form method="POST" id="aboutEditFormElement">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <div class="mb-3">
                        <label for="about_title_edit" class="form-label">Başlık <span style="color: #ff6b6b;">*</span></label>
                        <input type="text" class="form-control" id="about_title_edit" name="title"
                            value="<?php echo htmlspecialchars($about['title'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="about_subtitle_edit" class="form-label">Alt Başlık</label>
                        <input type="text" class="form-control" id="about_subtitle_edit" name="subtitle"
                            value="<?php echo htmlspecialchars($about['subtitle'] ?? ''); ?>" 
                            placeholder="Örn: İstanbul Teknik Üniversitesi - ITU Infosec">
                        <small class="text-muted d-block mt-1">İsteğe bağlı</small>
                    </div>
                    <div class="mb-3">
                        <label for="about_content_edit" class="form-label">İçerik <span style="color: #ff6b6b;">*</span></label>
                        <textarea class="form-control" id="about_content_edit" name="content" rows="10"
                            required><?php echo htmlspecialchars($about['content'] ?? ''); ?></textarea>
                        <small class="text-muted d-block mt-1">Sayfada gösterilecek metin içeriği</small>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Kaydet
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="hideAboutEditForm()">
                            <i class="bi bi-x-circle me-1"></i> Kapat
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="preview-card-about">
                <div class="preview-content-about">
                    <h3 class="preview-heading-about" id="about_preview_title">
                        <?php echo htmlspecialchars($about['title']); ?>
                    </h3>
                    <div id="about_preview_subtitle_container">
                        <p class="preview-subtitle-about" id="about_preview_subtitle">
                            <?php echo htmlspecialchars($about['subtitle']); ?>
                        </p>
                    </div>
                    <p class="preview-text-about" id="about_preview_content">
                        <?php echo nl2br(htmlspecialchars($about['content'])); ?>
                    </p>
                </div>
            </div>
            <small class="text-muted d-block text-center">Canlı Önizleme</small>
        </div>
    </div>

<?php } else { ?>
    <div class="alert-custom">
        <h5 class="mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Veri Bulunamadı</h5>
        <p class="mb-0">Henüz "Hakkımızda" bölümü için bir içerik oluşturulmamış. Aşağıda yeni bir giriş oluşturabilirsiniz.</p>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="form-container-about">
                <div class="form-title">Yeni Giriş Oluştur</div>
                <form method="POST" id="aboutCreateFormElement">
                    <div class="mb-3">
                        <label for="about_title_create" class="form-label">Başlık <span style="color: #ff6b6b;">*</span></label>
                        <input type="text" class="form-control" id="about_title_create" name="title"
                            placeholder="Örn: Hakkımızda" required>
                    </div>
                    <div class="mb-3">
                        <label for="about_subtitle_create" class="form-label">Alt Başlık</label>
                        <input type="text" class="form-control" id="about_subtitle_create" name="subtitle"
                            placeholder="Örn: İstanbul Teknik Üniversitesi - ITU Infosec">
                        <small class="text-muted d-block mt-1">İsteğe bağlı</small>
                    </div>
                    <div class="mb-3">
                        <label for="about_content_create" class="form-label">İçerik <span style="color: #ff6b6b;">*</span></label>
                        <textarea class="form-control" id="about_content_create" name="content" rows="10"
                            placeholder="Hakkımızda bölümü için metni buraya yazın..." required></textarea>
                        <small class="text-muted d-block mt-1">Sayfada gösterilecek metin içeriği</small>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Oluştur
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i> Temizle
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="preview-card-about">
                <div class="preview-content-about">
                    <h3 class="preview-heading-about" id="about_preview_title_create">
                        Başlık Buraya Gelecek
                    </h3>
                    <div id="about_preview_subtitle_container_create" style="display: none;">
                        <p class="preview-subtitle-about" id="about_preview_subtitle_create"></p>
                    </div>
                    <p class="preview-text-about" id="about_preview_content_create">
                        İçerik buraya gelecek...
                    </p>
                </div>
            </div>
            <small class="text-muted d-block text-center">Canlı Önizleme</small>
        </div>
    </div>
<?php } ?>

<script>
    function showAboutEditForm() {
        $('#aboutEditContainer').fadeIn();
        document.getElementById('aboutEditContainer').scrollIntoView({ behavior: 'smooth' });
    }

    function hideAboutEditForm() {
        $('#aboutEditContainer').fadeOut();
    }

    $(document).ready(function () {
        // Handle Edit Form
        const editForm = $('#aboutEditFormElement');
        if (editForm.length) {
            const editTitleInput = $('#about_title_edit');
            const editSubtitleInput = $('#about_subtitle_edit');
            const editContentInput = $('#about_content_edit');
            const editPreviewTitle = $('#about_preview_title');
            const editPreviewSubtitle = $('#about_preview_subtitle');
            const editPreviewSubtitleContainer = $('#about_preview_subtitle_container');
            const editPreviewContent = $('#about_preview_content');

            function updateEditPreview() {
                const titleValue = editTitleInput.val() || 'Başlık Buraya Gelecek';
                const subtitleValue = editSubtitleInput.val();
                const contentValue = editContentInput.val() || 'İçerik buraya gelecek...';

                editPreviewTitle.text(titleValue);
                
                if (subtitleValue.trim()) {
                    editPreviewSubtitle.text(subtitleValue);
                    editPreviewSubtitleContainer.show();
                } else {
                    editPreviewSubtitleContainer.hide();
                }
                
                editPreviewContent.html(contentValue.replace(/\n/g, '<br>'));
            }

            editTitleInput.on('input', updateEditPreview);
            editSubtitleInput.on('input', updateEditPreview);
            editContentInput.on('input', updateEditPreview);

            editForm.on('submit', function (e) {
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
                            loadContent('about');
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
        }

        // Handle Create Form
        const createForm = $('#aboutCreateFormElement');
        if (createForm.length) {
            const createTitleInput = $('#about_title_create');
            const createSubtitleInput = $('#about_subtitle_create');
            const createContentInput = $('#about_content_create');
            const createPreviewTitle = $('#about_preview_title_create');
            const createPreviewSubtitle = $('#about_preview_subtitle_create');
            const createPreviewSubtitleContainer = $('#about_preview_subtitle_container_create');
            const createPreviewContent = $('#about_preview_content_create');

            function updateCreatePreview() {
                const titleValue = createTitleInput.val() || 'Başlık Buraya Gelecek';
                const subtitleValue = createSubtitleInput.val();
                const contentValue = createContentInput.val() || 'İçerik buraya gelecek...';

                createPreviewTitle.text(titleValue);
                
                if (subtitleValue.trim()) {
                    createPreviewSubtitle.text(subtitleValue);
                    createPreviewSubtitleContainer.show();
                } else {
                    createPreviewSubtitleContainer.hide();
                }
                
                createPreviewContent.html(contentValue.replace(/\n/g, '<br>'));
            }

            createTitleInput.on('input', updateCreatePreview);
            createSubtitleInput.on('input', updateCreatePreview);
            createContentInput.on('input', updateCreatePreview);

            createForm.on('submit', function (e) {
                e.preventDefault();
                const btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).html('Oluşturuluyor...');

                $.ajax({
                    type: 'POST',
                    url: 'about.php',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (res) {
                        btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i> Oluştur');
                        if (res.success) {
                            alert(res.message);
                            loadContent('about');
                        } else {
                            alert('Hata: ' + res.message);
                        }
                    },
                    error: function () {
                        btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i> Oluştur');
                        alert('Bir hata oluştu.');
                    }
                });
            });
        }
    });
</script>