<?php
require_once 'security_utils.php';
require_once 'config.php';

requireSecureSession();

// GET requests just show the security page, POST requests must have CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    
    $ip = sanitizeText($_POST['ip'] ?? '', 45, true);
    
    if ($ip === null || empty($ip)) {
        echo '<div class="alert alert-danger">Geçersiz IP adresi.</div>';
        require 'security.php';
        exit;
    }
    
    $file = '../data/blacklist.json';

    if (file_exists($file)) {
        $blacklist = json_decode(file_get_contents($file), true) ?? [];
        $newBlacklist = [];
        $found = false;

        foreach ($blacklist as $entry) {
            if ($entry['ip'] !== $ip) {
                $newBlacklist[] = $entry;
            } else {
                $found = true;
            }
        }

        if ($found) {
            file_put_contents($file, json_encode($newBlacklist, JSON_PRETTY_PRINT), LOCK_EX);
            chmod($file, 0644);
            // Return updated view
            require 'security.php';
            exit;
        }
    }
}

// Fallback if failed
require 'security.php';
?>
