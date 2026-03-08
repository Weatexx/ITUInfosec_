<?php
// unblock_ip.php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo '<div class="alert alert-danger">Yetkisiz erişim.</div>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ip'])) {
    $ipToUnblock = $_POST['ip'];
    $file = '../data/blacklist.json';

    if (file_exists($file)) {
        $blacklist = json_decode(file_get_contents($file), true) ?? [];
        $newBlacklist = [];
        $found = false;

        foreach ($blacklist as $entry) {
            if ($entry['ip'] !== $ipToUnblock) {
                $newBlacklist[] = $entry;
            } else {
                $found = true;
            }
        }

        if ($found) {
            file_put_contents($file, json_encode($newBlacklist, JSON_PRETTY_PRINT));
            // Return updated view
            require 'security.php';
            exit;
        }
    }
}

// Fallback if failed
require 'security.php';
?>