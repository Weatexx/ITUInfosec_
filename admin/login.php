<?php
session_start();
require_once 'config.php';
require_once __DIR__ . '/flatfile_auth.php';

// Oturum kontrolü
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: dashboard.php');
    exit;
}

$blacklistFile = '../data/blacklist.json';
$logsFile = '../data/security_logs.json';
$userIP = $_SERVER['REMOTE_ADDR'];

// 1. Check Blacklist
if (file_exists($blacklistFile)) {
    $blacklist = json_decode(file_get_contents($blacklistFile), true) ?? [];
    foreach ($blacklist as $entry) {
        if ($entry['ip'] === $userIP) {
            header('Location: ../access_denied.php');
            exit;
        }
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Use flat-file auth module for verification
    if (authenticate_admin($username, $password)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
        exit;
    } else {
        // 2. Log Failed Attempt
        $logs = file_exists($logsFile) ? json_decode(file_get_contents($logsFile), true) : [];
        if (!is_array($logs))
            $logs = [];

        $newLog = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $userIP,
            'username' => $username,
            'note' => 'failed_login'
        ];

        // Prepend new log
        array_unshift($logs, $newLog);
        file_put_contents($logsFile, json_encode($logs, JSON_PRETTY_PRINT));

        // 3. Check Brute Force Threshold (5 attempts in 15 mins)
        $recentFailures = 0;
        $thresholdTime = time() - (15 * 60); // 15 minutes ago

        foreach ($logs as $log) {
            if ($log['ip'] === $userIP && strtotime($log['timestamp']) > $thresholdTime) {
                $recentFailures++;
            }
        }

        if ($recentFailures >= 5) {
            // Add to Blacklist
            $blacklist = file_exists($blacklistFile) ? json_decode(file_get_contents($blacklistFile), true) : [];
            if (!is_array($blacklist))
                $blacklist = [];

            $blacklist[] = [
                'ip' => $userIP,
                'blocked_at' => date('Y-m-d H:i:s'),
                'reason' => 'Brute Force Detection (5+ Failed Logins)'
            ];

            file_put_contents($blacklistFile, json_encode($blacklist, JSON_PRETTY_PRINT));

            header('Location: ../access_denied.php');
            exit;
        }

        $error = "Kullanıcı adı veya şifre hatalı.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Girişi | ITU CTF'26</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Cyber Theme -->
    <link rel="stylesheet" href="../root/css/modern.css">

    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            text-align: center;
        }

        .form-control-glass {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--glass-border);
            color: #fff;
            padding: 1rem 1rem 1rem 3rem;
            border-radius: 10px;
            width: 100%;
            transition: var(--transition);
        }

        .form-control-glass:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(0, 242, 234, 0.2);
            background: rgba(0, 0, 0, 0.5);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            transition: var(--transition);
        }

        .form-group:focus-within .input-icon {
            color: var(--primary-color);
        }
    </style>
</head>

<body>
    <div class="cyber-bg"></div>
    <div class="grid-overlay"></div>

    <div class="glass-panel login-card rounded-4">
        <div class="mb-4">
            <i class="fas fa-shield-halved fa-3x mb-3"
                style="color: var(--primary-color); filter: drop-shadow(0 0 10px var(--primary-color));"></i>
            <h2 class="mb-0" style="font-weight: 700;">ITU CTF'26 <span
                    style="color: var(--primary-color); font-weight: 300;">ADMIN</span></h2>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger mb-4"
                style="background: rgba(255, 0, 85, 0.1); border: 1px solid rgba(255, 0, 85, 0.3); color: #ff0055; border-radius: 8px; padding: 10px;">
                <i class="fas fa-circle-exclamation me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group mb-3 position-relative">
                <i class="fas fa-user-astronaut input-icon"></i>
                <input type="text" name="username" class="form-control-glass" placeholder="Kullanıcı Adı" required
                    autocomplete="off">
            </div>

            <div class="form-group mb-4 position-relative">
                <i class="fas fa-key input-icon"></i>
                <input type="password" name="password" class="form-control-glass" placeholder="Şifre" required>
            </div>

            <button type="submit" class="btn-glow w-100" style="border-radius: 10px; text-transform: uppercase;">
                Sisteme Giriş Yap
            </button>
        </form>
    </div>
</body>

</html>