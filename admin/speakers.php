<?php
require_once 'data_manager.php';

$speakers = $dataManager->getSpeakers();
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
    #addSpeakerForm,
    #editSpeakerForm {
        max-width: 600px;
        margin: 0 auto;
    }

    .form-container {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(10px);
        padding: 20px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        color: #fff;
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
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Konuşmacılar Listesi</h4>
    <button class='btn btn-success' onclick='showAddSpeakerForm()'>
        <i class="bi bi-plus-circle me-1"></i> Konuşmacı Ekle
    </button>
</div>

<?php
if (!empty($speakers)) {
    echo "<div class='table-responsive'>";
    echo "<table class='table table-dark table-striped table-hover table-bordered mb-0'>";
    echo "<thead class='table-dark'><tr>
            <th>ID</th>
            <th>Konuşmacı Adı</th>
            <th>Uzmanlık</th>
            <th class='hide-on-mobile'>Profil URL</th>
            <th>Fotoğraf</th>
            <th>İşlemler</th>
          </tr></thead>";
    echo "<tbody>";
    foreach ($speakers as $row) {
        $photo = htmlspecialchars($row['photo']);
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['expertise']) . "</td>";
        echo "<td class='hide-on-mobile'>" . htmlspecialchars($row['profile_url'] ?? '#') . "</td>";
        echo "<td><img src='" . $photo . "' alt='Konuşmacı Fotoğrafı' class='table-image' style='width: 50px; height: 50px; border-radius: 50%; object-fit: cover;'></td>";
        echo "<td>
                <div class='action-buttons'>
                  <button class='btn btn-warning btn-sm' onclick='editSpeaker(" . $row['id'] . ")'><i class='bi bi-pencil me-1'></i>Düzenle</button>
                  <button class='btn btn-danger btn-sm' onclick='deleteSpeaker(" . $row['id'] . ")'><i class='bi bi-trash me-1'></i>Sil</button>
                </div>
              </td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    echo "</div>";
} else {
    echo "<div class='alert alert-info'>Henüz konuşmacı bulunmamaktadır. Eklemek için 'Konuşmacı Ekle' butonunu kullanabilirsiniz.</div>";
}
?>

<script>
    function editSpeaker(id) {
        $.ajax({
            url: 'edit_speaker.php',
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

    function deleteSpeaker(id) {
        if (confirm('Bu konuşmacıyı silmek istediğinize emin misiniz?')) {
            $.ajax({
                url: 'delete_speaker.php',
                method: 'POST',
                data: { id: id },
                success: function (response) {
                    $('#content-area').html(response);
                },
                error: function () {
                    $('#content-area').html('<div class="alert alert-danger">Silme işlemi gerçekleştirilemedi.</div>');
                }
            });
        }
    }

    function showAddSpeakerForm() {
        const formHtml = `
        <div class="form-container">
            <h4 class="form-title">Konuşmacı Ekle</h4>
            <form id="addSpeakerForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">İsim</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="expertise" class="form-label">Uzmanlık</label>
                    <input type="text" class="form-control" id="expertise" name="expertise" required>
                </div>
                <div class="mb-3">
                    <label for="profile_url" class="form-label">Profil URL'si</label>
                    <input type="url" class="form-control" id="profile_url" name="profile_url" placeholder="https://example.com/profile">
                    <small class="form-text text-muted">"Profili Görüntüle" butonuna tıklandığında açılacak URL'yi girin.</small>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Fotoğraf Yükle</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Ekle</button>
                    <button type="button" class="btn btn-secondary" onclick="hideAddSpeakerForm()"><i class="bi bi-x-circle me-1"></i>İptal</button>
                </div>
            </form>
        </div>
    `;
        $('#content-area').html(formHtml);

        document.getElementById('addSpeakerForm').onsubmit = function (event) {
            event.preventDefault();
            var formData = new FormData(this);

            $('#content-area').append('<div id="loading" class="text-center mt-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div><p>Yükleniyor...</p></div>');

            $.ajax({
                url: 'add_speaker.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#content-area').html(response);
                },
                error: function () {
                    $('#content-area').html('<div class="alert alert-danger">Konuşmacı ekleme işlemi gerçekleştirilemedi.</div>');
                }
            });
        };
    }

    function hideAddSpeakerForm() {
        loadContent('speakers');
    }
</script>