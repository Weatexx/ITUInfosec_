<?php
declare(strict_types=1);

// Configuration File
define('ADMIN_USERNAME', 'admin');
// Hash for "123456" (should be changed in production)
define('ADMIN_PASSWORD_HASH', '$2y$12$6p1xlRl0bAqZSAN7t/4LL.zkpol0DF7fJ/kSDLXkMh.orpN7TL1dy');

// Paths
define('DATA_DIR', __DIR__ . '/../data');

// Set secure admin store path outside webroot
if (!getenv('ADMIN_STORE_PATH')) {
    putenv('ADMIN_STORE_PATH=C:\\secure_itu_admin\\admins.json');
}

// Create data directory with secure permissions
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
    chmod(DATA_DIR, 0755);
}

// Ensure data directory has correct permissions
if (is_dir(DATA_DIR)) {
    chmod(DATA_DIR, 0755);
}

// Include security utilities
require_once __DIR__ . '/security_utils.php';

// Set security headers and configure session
setSecurityHeaders();
secureSessionConfiguration();

?>
