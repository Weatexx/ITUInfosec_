<?php
session_start();
require_once 'data_manager.php';

if (!isset($_SESSION['loggedin'])) {
    echo '<div class="alert alert-danger">Giriş yapmalısınız!</div>';
    exit;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hero'])) {
    $data = [
        'top_title' => $_POST['top_title'],
        'main_title' => $_POST['main_title'],
        'description' => $_POST['description'],
        'top_title_size' => intval($_POST['top_title_size']),
        'main_title_size' => intval($_POST['main_title_size']),
        'description_size' => intval($_POST['description_size']),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Convert hyphens to BR for storage if needed, or handle on display. 
    // The previous code did replaces. Let's keep it simple and store as text, handle display in frontend/here.
    // Actually the previous code replaced hyphens with BRs BEFORE DB insert if JS didn't do it.
    // Let's rely on the form data being correct or correct it.

    // We will trust the input for now, mirroring the JS login in previous file.

    $dataManager->saveHero($data);

    if ($isAjax) {
        echo json_encode(['success' => true, 'message' => 'Hero içeriği güncellendi!']);
        exit;
    }
}

$hero = $dataManager->getHero();
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

// Convert BR to hyphens for edit mode
function brToHyphen($text)
{
    return preg_replace('/<br\s*\/?>|class="[^"]*"/', ' - ', $text);
    // Simplified regex
}

$mainTitleEdit = str_replace('<br>', ' - ', $hero['main_title']);
$descEdit = str_replace(['<br>', '<br class="d-none d-md-inline">'], ' - ', $hero['description']);

if ($isAjax && $_SERVER['REQUEST_METHOD'] === 'POST')
    exit;
?>

<style>
    .preview-card {
        background-color: #212529;
        border-radius: 8px;
        padding: 1.5rem;
    }

    .preview-title {
        color: white !important;
        margin-bottom: 0.75rem;
    }

    .preview-subtitle {
        color: rgba(255, 255, 255, 0.61) !important;
        margin-bottom: 0.25rem;
    }

    .preview-description {
        color: rgba(255, 255, 255, 0.61) !important;
    }

    .font-size-control {
        display: flex;
        align-items: center;
        margin-top: 5px;
    }

    .font-size-control input[type=range] {
        flex-grow: 1;
        margin-right: 10px;
    }

    .font-size-control .font-size-value {
        min-width: 45px;
        text-align: center;
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.875rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Hero İçeriği Düzenle</h4>
</div>

<div class="card mb-4 bg-dark text-white border-secondary">
    <div class="card-body">
        <form id="heroForm">
            <div class="mb-3">
                <label for="top_title" class="form-label">Üst Başlık</label>
                <input type="text" class="form-control" id="top_title" name="top_title"
                    value="<?php echo htmlspecialchars($hero['top_title']); ?>" required>
                <div class="font-size-control">
                    <label class="me-2 small">Punto:</label>
                    <input type="range" class="form-range" id="top_title_size" name="top_title_size" min="10" max="36"
                        step="1" value="<?php echo $hero['top_title_size']; ?>">
                    <span class="font-size-value"
                        id="top_title_size_value"><?php echo $hero['top_title_size']; ?>px</span>
                </div>
            </div>

            <div class="mb-3">
                <label for="main_title" class="form-label">Ana Başlık</label>
                <textarea class="form-control" id="main_title" name="main_title" rows="2"
                    required><?php echo htmlspecialchars($mainTitleEdit); ?></textarea>
                <small class="text-muted">Satır kesmesi için tire (-) kullanın.</small>
                <div class="font-size-control">
                    <label class="me-2 small">Punto:</label>
                    <input type="range" class="form-range" id="main_title_size" name="main_title_size" min="18" max="72"
                        step="1" value="<?php echo $hero['main_title_size']; ?>">
                    <span class="font-size-value"
                        id="main_title_size_value"><?php echo $hero['main_title_size']; ?>px</span>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Açıklama</label>
                <textarea class="form-control" id="description" name="description" rows="4"
                    required><?php echo htmlspecialchars($descEdit); ?></textarea>
                <div class="font-size-control">
                    <label class="me-2 small">Punto:</label>
                    <input type="range" class="form-range" id="description_size" name="description_size" min="10"
                        max="24" step="1" value="<?php echo $hero['description_size']; ?>">
                    <span class="font-size-value"
                        id="description_size_value"><?php echo $hero['description_size']; ?>px</span>
                </div>
            </div>

            <div class="mb-4">
                <h5>Önizleme</h5>
                <div class="preview-card">
                    <p class="preview-subtitle mb-1" id="preview_top_title"
                        style="font-size: <?php echo $hero['top_title_size']; ?>px;">
                        <?php echo htmlspecialchars($hero['top_title']); ?>
                    </p>
                    <h3 class="preview-title" id="preview_main_title"
                        style="font-size: <?php echo $hero['main_title_size']; ?>px;"><?php echo $hero['main_title']; ?>
                    </h3>
                    <p class="preview-description mb-0" id="preview_description"
                        style="font-size: <?php echo $hero['description_size']; ?>px;">
                        <?php echo $hero['description']; ?>
                    </p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" name="update_hero"><i class="bi bi-save me-1"></i>
                Güncelle</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        const topTitleInput = $('#top_title');
        const mainTitleInput = $('#main_title');
        const descInput = $('#description');

        function updatePreview() {
            $('#preview_top_title').text(topTitleInput.val());
            $('#preview_main_title').html(mainTitleInput.val().replace(/ - /g, '<br>'));
            $('#preview_description').html(descInput.val().replace(/ - /g, '<br class="d-none d-md-inline">'));

            $('#preview_top_title').css('font-size', $('#top_title_size').val() + 'px');
            $('#preview_main_title').css('font-size', $('#main_title_size').val() + 'px');
            $('#preview_description').css('font-size', $('#description_size').val() + 'px');

            $('#top_title_size_value').text($('#top_title_size').val() + 'px');
            $('#main_title_size_value').text($('#main_title_size').val() + 'px');
            $('#description_size_value').text($('#description_size').val() + 'px');
        }

        $('input, textarea').on('input', updatePreview);

        $('#heroForm').on('submit', function (e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('Güncelleniyor...');

            // Prepare data replacing hyphens with HTML for storage
            let mainTitle = mainTitleInput.val().replace(/ - /g, '<br>');
            let description = descInput.val().replace(/ - /g, '<br class="d-none d-md-inline">');

            const formData = {
                top_title: topTitleInput.val(),
                main_title: mainTitle,
                description: description,
                top_title_size: $('#top_title_size').val(),
                main_title_size: $('#main_title_size').val(),
                description_size: $('#description_size').val(),
                update_hero: true
            };

            $.ajax({
                url: 'hero.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (res) {
                    btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Güncelle');
                    if (res.success) {
                        alert(res.message);
                    } else {
                        alert('Hata: ' + res.message);
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