<?php
// Configuration File
define('ADMIN_USERNAME', 'admin');
// Hash for "123456"
define('ADMIN_PASSWORD_HASH', '$2y$12$6p1xlRl0bAqZSAN7t/4LL.zkpol0DF7fJ/kSDLXkMh.orpN7TL1dy');

// Paths
define('DATA_DIR', __DIR__ . '/../data');

// Set secure admin store path outside webroot
if (!getenv('ADMIN_STORE_PATH')) {
    putenv('ADMIN_STORE_PATH=C:\\secure_itu_admin\\admins.json');
}

// Create data directory if it doesn't exist
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}
?>