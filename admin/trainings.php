<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();

$trainings = $dataManager->getTrainings();
$csrfToken = getCsrfToken();
?>

<style>
    /* Responsive tablo stilleri */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .action-buttons .btn {
        flex: 1;
        min-width: 70px;
    }

    @media (max-width: 768px) {
        .hide-on-mobile {
            display: none;
        }

        .table-image {
            width: 40px !important;
            height: 40px !important;
            object-fit: cover;
        }

        .action-buttons {
            flex-direction: column;
        }
    }

    /* Form stilleri */
    #addTrainingForm,
    #editTrainingForm {
        max-width: 600px;
        margin: 0 auto;
    }

    .form-container {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .form-title {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    @media (max-width: 576px) {
        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            margin-bottom: 10px;
        }
    }

    .text-truncate-custom {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }
</style>

<!-- Hidden CSRF Token for AJAX requests -->
<input type="hidden" id="csrf_token_input" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Eğitimler Listesi</h4>
    <button class='btn btn-success' onclick='showAddTrainingForm()'>
        <i class="bi bi-plus-circle me-1"></i> Eğitim Ekle
    </button>
</div>

<?php
if (!empty($trainings)) {
    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead class='table-dark'><tr>
            <th>ID</th>
            <th>Başlık</th>
            <th>Açıklama</th>
            <th>Tarih</th>
            <th class='hide-on-mobile'>Header</th>
            <th>Fotoğraf</th>
            <th>İşlemler</th>
          </tr></thead>";
    echo "<tbody>";
    foreach ($trainings as $row) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td><span class='text-truncate-custom'>" . htmlspecialchars(substr($row['description'], 0, 50)) . (strlen($row['description']) > 50 ? '...' : '') . "</span></td>";
        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td class='hide-on-mobile'>" . htmlspecialchars($row['header'] ?? '') . "</td>";

        if (!empty($row['photo'])) {
            echo "<td><img src='" . htmlspecialchars($row['photo']) . "' alt='Eğitim Fotoğrafı' class='table-image' style='width: 50px; height: 50px; object-fit: cover; border-radius: 8px;'></td>";
        } else {
            echo "<td><div class='text-center text-muted'><i class='bi bi-image' style='font-size: 24px;'></i></div></td>";
        }

        echo "<td>
                <div class='action-buttons'>
                    <button class='btn btn-warning btn-sm' onclick='editTraining(" . $row['id'] . ")'><i class='bi bi-pencil me-1'></i>Düzenle</button>
                    <button class='btn btn-danger btn-sm' onclick='deleteTraining(" . $row['id'] . ")'><i class='bi bi-trash me-1'></i>Sil</button>
                </div>
              </td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    echo "</div>";
} else {
    echo "<div class='alert alert-info'>Henüz eğitim bulunmamaktadır. Yukarıdaki 'Eğitim Ekle' butonunu kullanarak yeni eğitim ekleyebilirsiniz.</div>";
}
?>

<script>
    function editTraining(id) {
        $.ajax({
            url: 'edit_training.php',
            method: 'GET',
            data: { id: id },
            success: function (data) {
                $('#content-area').html(data);
            },
            error: function () {
                $('#content-area').html('<div class="alert alert-danger">Düzenleme formu yüklenemedi.</div>');
            }
        });
    }

    function deleteTraining(id) {
        if (confirm('Bu eğitimi silmek istediğinize emin misiniz?')) {
            var csrfToken = '<?php echo htmlspecialchars($csrfToken); ?>';
            console.log('CSRF Token:', csrfToken);
            console.log('Training ID:', id);
            
            $.ajax({
                url: 'delete_training.php',
                method: 'POST',
                data: { 
                    id: id,
                    csrf_token: csrfToken
                },
                dataType: 'json',
                success: function (response) {
                    console.log('Response:', response);
                    if (response.success) {
                        alert(response.message);
                        loadContent('trainings');
                    } else {
                        alert('Hata: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Silme işlemi gerçekleştirilemedi: ' + error);
                }
            });
        }
    }

    function showAddTrainingForm() {
        const formHtml = `
        <div class="form-container">
            <h4 class="form-title">Eğitim Ekle</h4>
            <form id="addTrainingForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Başlık</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Tarih</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="mb-3">
                    <label for="time" class="form-label">Saat</label>
                    <input type="text" class="form-control" id="time" name="time" placeholder="Örn: 14:00 veya boş bırakabilirsiniz">
                </div>
                <div class="mb-3">
                    <label for="header" class="form-label">Header</label>
                    <input type="text" class="form-control" id="header" name="header">
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Fotoğraf Yükle</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                    <small class="form-text text-muted">Lütfen JPG, JPEG, PNG veya GIF formatında bir resim seçin.</small>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Ekle</button>
                    <button type="button" class="btn btn-secondary" onclick="hideAddTrainingForm()"><i class="bi bi-x-circle me-1"></i>İptal</button>
                </div>
            </form>
        </div>
    `;
        $('#content-area').html(formHtml);

        document.getElementById('addTrainingForm').onsubmit = function (event) {
            event.preventDefault();
            var formData = new FormData(this);

            if (document.getElementById('photo').files.length === 0) {
                alert('Lütfen bir fotoğraf seçin.');
                return;
            }

            $('#content-area').append('<div id="loading" class="text-center mt-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div><p>Yükleniyor...</p></div>');

            $.ajax({
                url: 'add_training.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#content-area').html(response);
                },
                error: function () {
                    $('#content-area').html('<div class="alert alert-danger">Eğitim ekleme işlemi gerçekleştirilemedi.</div>');
                }
            });
        };
    }

    function hideAddTrainingForm() {
        loadContent('trainings');
    }
</script>