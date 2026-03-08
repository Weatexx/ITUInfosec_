<?php
// security.php
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-shield-halved me-2 text-primary"></i>Güvenlik ve Erişim Kontrolü</h4>
    <span class="badge bg-primary">Canlı İzleme Aktif</span>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="securityTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button"
            role="tab">
            <i class="fas fa-list-ul me-2"></i>Güvenlik Logları
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-danger" id="blacklist-tab" data-bs-toggle="tab" data-bs-target="#blacklist"
            type="button" role="tab">
            <i class="fas fa-ban me-2"></i>Kara Liste (Blacklist)
        </button>
    </li>
</ul>

<div class="tab-content" id="securityTabsContent">
    <!-- LOGS TAB -->
    <div class="tab-pane fade show active" id="logs" role="tabpanel">
        <div class="card bg-dark border-0 shadow-lg">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover mb-0 align-middle">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Zaman</th>
                                <th>IP Adresi</th>
                                <th>Kullanıcı Adı Denemesi</th>
                                <th>Şifre Denemesi</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $logsFile = '../data/security_logs.json';
                            $logs = file_exists($logsFile) ? json_decode(file_get_contents($logsFile), true) : [];

                            if (!empty($logs)) {
                                $count = 0;
                                foreach ($logs as $log) {
                                    if ($count++ > 100)
                                        break; // Limit to last 100
                                    echo "<tr>";
                                    echo "<td class='text-nowrap'>" . htmlspecialchars($log['timestamp']) . "</td>";
                                    echo "<td><span class='badge bg-secondary'>" . htmlspecialchars($log['ip']) . "</span></td>";
                                    echo "<td>" . htmlspecialchars($log['username']) . "</td>";
                                    echo "<td class='font-monospace text-muted small'>" . htmlspecialchars(substr($log['password'], 0, 20)) . "</td>";
                                    echo "<td><span class='badge bg-warning text-dark'>Başarısız Giriş</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Henüz kayıtlı bir güvenlik ihlali yok.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- BLACKLIST TAB -->
    <div class="tab-pane fade" id="blacklist" role="tabpanel">
        <div class="alert alert-danger d-flex align-items-center mb-4">
            <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
            <div>
                <strong>DİKKAT!</strong> Bu listedeki IP adreslerinin sisteme erişimi tamamen engellenmiştir.
            </div>
        </div>

        <div class="card bg-dark border-0 shadow-lg">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr class="text-danger">
                                <th>Engellenen IP</th>
                                <th>Engellenme Zamanı</th>
                                <th>Sebep</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $blacklistFile = '../data/blacklist.json';
                            $blacklist = file_exists($blacklistFile) ? json_decode(file_get_contents($blacklistFile), true) : [];

                            if (!empty($blacklist)) {
                                foreach ($blacklist as $entry) {
                                    echo "<tr>";
                                    echo "<td><span class='badge bg-danger p-2'>" . htmlspecialchars($entry['ip']) . "</span></td>";
                                    echo "<td>" . htmlspecialchars($entry['blocked_at']) . "</td>";
                                    echo "<td>" . htmlspecialchars($entry['reason']) . "</td>";
                                    echo "<td>";
                                    echo "<button onclick=\"unblockIP('" . htmlspecialchars($entry['ip']) . "')\" class='btn btn-outline-success btn-sm'><i class='fas fa-unlock me-1'></i>Engeli Kaldır</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center py-4 text-success'><i class='fas fa-check-circle me-2'></i>Kara liste boş. Şu anda engellenen kimse yok.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function unblockIP(ip) {
        if (confirm(ip + ' IP adresinin engelini kaldırmak istediğinize emin misiniz?')) {
            $.ajax({
                url: 'unblock_ip.php',
                method: 'POST',
                data: { ip: ip },
                success: function (response) {
                    $('#content-area').html(response);
                },
                error: function () {
                    alert('İşlem başarısız oldu.');
                }
            });
        }
    }
</script>