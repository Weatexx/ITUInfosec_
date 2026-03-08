<?php
require_once 'data_manager.php';

$sponsors = $dataManager->getSponsors();
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
    #addSponsorForm,
    #editSponsorForm {
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

    .text-truncate-custom {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Sponsorlar Listesi</h4>
    <button class='btn btn-success' onclick='showAddSponsorForm()'>
        <i class="bi bi-plus-circle me-1"></i> Sponsor Ekle
    </button>
</div>

<?php
if (!empty($sponsors)) {
    echo "<div class='table-responsive'>";
    echo "<table class='table table-dark table-striped table-hover table-bordered mb-0'>";
    echo "<thead class='table-dark'><tr>
            <th>ID</th>
            <th>Firma Adı</th>
            <th>Açıklama</th>
            <th>Uzmanlık Alanı</th>
            <th>Fotoğraf</th>
            <th>İşlemler</th>
          </tr></thead>";
    echo "<tbody>";
    foreach ($sponsors as $row) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td><span class='text-truncate-custom'>" . htmlspecialchars(substr($row['description'], 0, 50)) . (strlen($row['description']) > 50 ? '...' : '') . "</span></td>";
        echo "<td>" . htmlspecialchars($row['expertise'] ?? '-') . "</td>";

        if (!empty($row['photo'])) {
            echo "<td><img src='" . htmlspecialchars($row['photo']) . "' alt='Sponsor Fotoğrafı' class='table-image' style='width: 50px; height: 50px; object-fit: cover; border-radius: 8px;'></td>";
        } else {
            echo "<td><div class='text-center text-muted'><i class='bi bi-image' style='font-size: 24px;'></i></div></td>";
        }

        echo "<td>
                <div class='action-buttons'>
                    <button class='btn btn-warning btn-sm' onclick='editSponsor(" . $row['id'] . ")'><i class='bi bi-pencil me-1'></i>Düzenle</button>
                    <button class='btn btn-danger btn-sm' onclick='deleteSponsor(" . $row['id'] . ")'><i class='bi bi-trash me-1'></i>Sil</button>
                </div>
              </td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    echo "</div>";
} else {
    echo "<div class='alert alert-info'>Henüz sponsor bulunmamaktadır. Yukarıdaki 'Sponsor Ekle' butonunu kullanarak yeni sponsor ekleyebilirsiniz.</div>";
}
?>

<script>
    function editSponsor(id) {
        $.ajax({
            url: 'edit_sponsor.php',
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

    function deleteSponsor(id) {
        if (confirm('Bu sponsoru silmek istediğinize emin misiniz?')) {
            $.ajax({
                url: 'delete_sponsor.php',
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

    function showAddSponsorForm() {
        const formHtml = `
        <div class="form-container">
            <h4 class="form-title">Sponsor Ekle</h4>
            <form id="addSponsorForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Firma Adı</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Şirket hakkında bilgi..."></textarea>
                </div>
                <div class="mb-3">
                    <label for="expertise" class="form-label">Uzmanlık Alanı</label>
                    <input type="text" class="form-control" id="expertise" name="expertise" required placeholder="Örn: Sızma Testi, Adli Bilişim...">
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Fotoğraf Yükle</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                    <small class="form-text text-muted">Lütfen JPG, JPEG, PNG veya GIF formatında bir resim seçin.</small>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Ekle</button>
                    <button type="button" class="btn btn-secondary" onclick="hideAddSponsorForm()"><i class="bi bi-x-circle me-1"></i>İptal</button>
                </div>
            </form>
        </div>
    `;
        $('#content-area').html(formHtml);

        document.getElementById('addSponsorForm').onsubmit = function (event) {
            event.preventDefault();
            var formData = new FormData(this);

            if (document.getElementById('photo').files.length === 0) {
                alert('Lütfen bir fotoğraf seçin.');
                return;
            }

            $('#content-area').append('<div id="loading" class="text-center mt-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div><p>Yükleniyor...</p></div>');

            $.ajax({
                url: 'add_sponsor.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#content-area').html(response);
                },
                error: function () {
                    $('#content-area').html('<div class="alert alert-danger">Sponsor ekleme işlemi gerçekleştirilemedi.</div>');
                }
            });
        };
    }

    function hideAddSponsorForm() {
        loadContent('sponsors');
    }
</script>