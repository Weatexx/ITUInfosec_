<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hero'])) {
    requireCsrfToken();
    
    $top_title = validateText($_POST['top_title'] ?? '', 255, true);
    $main_title = validateText($_POST['main_title'] ?? '', 255, true);
    $description = validateText($_POST['description'] ?? '', 500, true);
    $top_title_size = validateInteger($_POST['top_title_size'] ?? 14, 8, 72);
    $main_title_size = validateInteger($_POST['main_title_size'] ?? 36, 12, 96);
    $description_size = validateInteger($_POST['description_size'] ?? 16, 10, 48);
    
    $errors = [];
    if ($top_title === null || $top_title === '') $errors[] = 'Üst başlık gerekli';
    if ($main_title === null || $main_title === '') $errors[] = 'Ana başlık gerekli';
    if ($description === null || $description === '') $errors[] = 'Açıklama gerekli';
    if ($top_title_size === null) $errors[] = 'Geçersiz üst başlık boyutu';
    if ($main_title_size === null) $errors[] = 'Geçersiz ana başlık boyutu';
    if ($description_size === null) $errors[] = 'Geçersiz açıklama boyutu';
    
    if (empty($errors)) {
        $data = [
            'top_title' => $top_title,
            'main_title' => $main_title,
            'description' => $description,
            'top_title_size' => $top_title_size,
            'main_title_size' => $main_title_size,
            'description_size' => $description_size,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $dataManager->saveHero($data);

        if ($isAjax) {
            echo json_encode(['success' => true, 'message' => 'Hero içeriği güncellendi!']);
            exit;
        }
    } else {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            exit;
        }
    }
}

$hero = $dataManager->getHero();
$csrfToken = getCsrfToken();
// Defaults
if (!$hero) {
    $hero = [
        'top_title' => 'İ s t a n b u l   T e k n i k   Ü n i v e r s i t e s i',
        'main_title' => 'ITU Infosec <br>ve Eğitimleri',
        'description' => 'Siber güvenlik alanında öncü eğitimler.',
        'top_title_size' => 14,
        'main_title_size' => 36,
        'description_size' => 16
    ];
}

if ($isAjax && $_SERVER['REQUEST_METHOD'] === 'POST')
    exit;
?>

<style>
    .hero-info-header {
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

    .hero-info-header .info-section h5 {
        margin-bottom: 5px;
        font-weight: 600;
    }

    .form-container-hero {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        color: #fff;
    }

    .form-container-hero .form-title {
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .font-size-control {
        display: flex;
        align-items: center;
        margin-top: 10px;
        gap: 10px;
    }

    .font-size-control input[type=range] {
        flex-grow: 1;
    }

    .font-size-control .font-size-value {
        min-width: 50px;
        text-align: center;
        background-color: rgba(0, 0, 0, 0.2);
        padding: 5px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .font-size-label {
        min-width: 70px;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .preview-card-hero {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 30px 20px;
        margin-bottom: 20px;
        min-height: 400px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .preview-content-hero {
        text-align: center;
    }

    .preview-heading-hero {
        background: linear-gradient(90deg, #ffffff, #00f2ea);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .preview-subtitle-hero {
        color: rgba(255, 255, 255, 0.6);
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .preview-text-hero {
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.6;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    @media (max-width: 992px) {
        .hero-info-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .preview-card-hero {
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

        .hero-info-header {
            padding: 15px;
        }

        .form-container-hero {
            padding: 15px;
        }

        .font-size-control {
            flex-direction: column;
            align-items: flex-start;
        }

        .font-size-control input[type=range] {
            width: 100%;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 style="margin: 0;">Hero (Giriş) Alanı</h4>
</div>

<div class="hero-info-header">
    <div class="info-section">
        <h5>Ana Sayfa Giriş Bölümü</h5>
        <small class="text-muted">
            <i class="bi bi-calendar-event me-1"></i>
            Son Güncelleme: <?php echo isset($hero['updated_at']) ? date('d.m.Y H:i', strtotime($hero['updated_at'])) : 'Bilinmiyor'; ?>
        </small>
    </div>
    <button class="btn btn-primary btn-sm" onclick="scrollToForm()">
        <i class="bi bi-pencil-square me-1"></i> Düzenle
    </button>
</div>

<div class="row">
    <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="form-container-hero">
            <div class="form-title">Düzenle</div>
            <form id="heroForm">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <div class="mb-3">
                    <label for="top_title" class="form-label">Üst Başlık <span style="color: #ff6b6b;">*</span></label>
                    <input type="text" class="form-control" id="top_title" name="top_title"
                        value="<?php echo htmlspecialchars($hero['top_title']); ?>" required>
                    <div class="font-size-control">
                        <span class="font-size-label">Punto:</span>
                        <input type="range" class="form-range" id="top_title_size" name="top_title_size" min="10" max="36" step="1" value="<?php echo $hero['top_title_size']; ?>">
                        <span class="font-size-value" id="top_title_size_value"><?php echo $hero['top_title_size']; ?>px</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="main_title" class="form-label">Ana Başlık <span style="color: #ff6b6b;">*</span></label>
                    <textarea class="form-control" id="main_title" name="main_title" rows="3" required><?php echo htmlspecialchars($hero['main_title'] ?? ''); ?></textarea>
                    <small class="text-muted d-block mt-1">Satır kesmesi için tire (-) kullanın.</small>
                    <div class="font-size-control">
                        <span class="font-size-label">Punto:</span>
                        <input type="range" class="form-range" id="main_title_size" name="main_title_size" min="18" max="72" step="1" value="<?php echo $hero['main_title_size']; ?>">
                        <span class="font-size-value" id="main_title_size_value"><?php echo $hero['main_title_size']; ?>px</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama <span style="color: #ff6b6b;">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($hero['description'] ?? ''); ?></textarea>
                    <small class="text-muted d-block mt-1">Satır kesmesi için tire (-) kullanın.</small>
                    <div class="font-size-control">
                        <span class="font-size-label">Punto:</span>
                        <input type="range" class="form-range" id="description_size" name="description_size" min="10" max="24" step="1" value="<?php echo $hero['description_size']; ?>">
                        <span class="font-size-value" id="description_size_value"><?php echo $hero['description_size']; ?>px</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success" name="update_hero">
                        <i class="bi bi-check-circle me-1"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="preview-card-hero">
            <div class="preview-content-hero">
                <p class="preview-subtitle-hero" id="preview_top_title" style="font-size: <?php echo $hero['top_title_size']; ?>px; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($hero['top_title']); ?>
                </p>
                <h2 class="preview-heading-hero" id="preview_main_title" style="font-size: <?php echo $hero['main_title_size']; ?>px;">
                    <?php echo nl2br(htmlspecialchars($hero['main_title'])); ?>
                </h2>
                <p class="preview-text-hero" id="preview_description" style="font-size: <?php echo $hero['description_size']; ?>px; margin-top: 15px;">
                    <?php echo nl2br(htmlspecialchars($hero['description'])); ?>
                </p>
            </div>
        </div>
        <small class="text-muted d-block text-center">Canlı Önizleme</small>
    </div>
</div>

<script>
    function scrollToForm() {
        document.querySelector('.form-container-hero').scrollIntoView({ behavior: 'smooth' });
    }

    $(document).ready(function () {
        const topTitleInput = $('#top_title');
        const mainTitleInput = $('#main_title');
        const descInput = $('#description');
        const topTitleSizeInput = $('#top_title_size');
        const mainTitleSizeInput = $('#main_title_size');
        const descSizeInput = $('#description_size');

        function updatePreview() {
            // Text updates
            $('#preview_top_title').text(topTitleInput.val());
            $('#preview_main_title').html(mainTitleInput.val().replace(/ - /g, '<br>'));
            $('#preview_description').html(descInput.val().replace(/ - /g, '<br class="d-none d-md-inline">'));

            // Size updates
            $('#preview_top_title').css('font-size', topTitleSizeInput.val() + 'px');
            $('#preview_main_title').css('font-size', mainTitleSizeInput.val() + 'px');
            $('#preview_description').css('font-size', descSizeInput.val() + 'px');

            // Value display updates
            $('#top_title_size_value').text(topTitleSizeInput.val() + 'px');
            $('#main_title_size_value').text(mainTitleSizeInput.val() + 'px');
            $('#description_size_value').text(descSizeInput.val() + 'px');
        }

        $('input, textarea').on('input change', updatePreview);

        $('#heroForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Güncelleniyor...');

            // Prepare data replacing hyphens with HTML for storage
            let mainTitle = mainTitleInput.val().replace(/ - /g, '<br>');
            let description = descInput.val().replace(/ - /g, '<br class="d-none d-md-inline">');

            const formData = {
                csrf_token: $('input[name="csrf_token"]').val(),
                top_title: topTitleInput.val(),
                main_title: mainTitle,
                description: description,
                top_title_size: topTitleSizeInput.val(),
                main_title_size: mainTitleSizeInput.val(),
                description_size: descSizeInput.val(),
                update_hero: true
            };

            $.ajax({
                url: 'hero.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (res) {
                    btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Güncelle');
                    if (res.success) {
                        alert(res.message);
                        loadContent('hero');
                    } else {
                        alert('Hata: ' + res.message);
                    }
                },
                error: function () {
                    btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Güncelle');
                    alert('Bir hata oluştu.');
                }
            });
        });
    });
</script>