<?php
declare(strict_types=1);
/**
 * Secure flat-file admin authentication helper.
 *
 * Principles implemented:
 * - One-way password hashing only (Argon2id preferred, bcrypt fallback)
 * - Memory-hard Argon2id defaults (tunable)
 * - Per-password unique salts handled by password_hash()
 * - Atomic file writes and JSON format
 * - Attempts to protect store if left inside webroot (writes .htaccess)
 *
 * Usage:
 * - Configure path via env var ADMIN_STORE_PATH to a directory OUTSIDE web root.
 * - If not configured, module will create a protected folder `../data_secure/` and store `admins.json` there.
 *
 * NOTE: Tune Argon2id `memory_cost` and `time_cost` according to your server resources.
 */

const DEFAULT_STORE_DIR = __DIR__ . '/../data_secure';
const DEFAULT_STORE_FILE = DEFAULT_STORE_DIR . '/admins.json';

function get_store_path(): string
{
    $env = getenv('ADMIN_STORE_PATH');
    if ($env && is_dir(dirname($env))) {
        return $env;
    }
    if (!is_dir(DEFAULT_STORE_DIR)) {
        @mkdir(DEFAULT_STORE_DIR, 0700, true);
    }
    protect_store_dir(DEFAULT_STORE_DIR);
    return DEFAULT_STORE_FILE;
}

function protect_store_dir(string $dir): void
{
    // If directory is inside document root, add .htaccess to deny direct access (Apache)
    $ht = $dir . DIRECTORY_SEPARATOR . '.htaccess';
    if (!file_exists($ht)) {
        $content = "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n";
        @file_put_contents($ht, $content, LOCK_EX);
        @chmod($ht, 0600);
    }
}

function load_store(): array
{
    $path = get_store_path();
    if (!file_exists($path)) {
        return [];
    }
    $json = @file_get_contents($path);
    if ($json === false) {
        throw new RuntimeException('Unable to read admin store');
    }
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function save_store(array $data): void
{
    $path = get_store_path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }
    $tmp = $path . '.' . bin2hex(random_bytes(6)) . '.tmp';
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (@file_put_contents($tmp, $json, LOCK_EX) === false) {
        throw new RuntimeException('Failed to write temp admin store');
    }
    if (!@rename($tmp, $path)) {
        @unlink($tmp);
        throw new RuntimeException('Failed to atomically replace admin store');
    }
    @chmod($path, 0600);
}

function hash_password(string $password): string
{
    if (defined('PASSWORD_ARGON2ID')) {
        // Defaults tuned for memory-hardness. Adjust to your server capacity.
        $options = [
            // memory_cost is in kibibytes. 65536 => 64 MiB
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 2,
        ];
        $hash = password_hash($password, PASSWORD_ARGON2ID, $options);
        if ($hash === false) {
            throw new RuntimeException('Argon2id hashing failed');
        }
        return $hash;
    }
    // Fallback to bcrypt with cost >= 12
    $options = ['cost' => 12];
    $hash = password_hash($password, PASSWORD_BCRYPT, $options);
    if ($hash === false) {
        throw new RuntimeException('bcrypt hashing failed');
    }
    return $hash;
}

function verify_password(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

function needs_rehash(string $hash): bool
{
    if (defined('PASSWORD_ARGON2ID')) {
        $options = [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 2,
        ];
        return password_needs_rehash($hash, PASSWORD_ARGON2ID, $options);
    }
    return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
}

function create_admin(string $username, string $password): void
{
    $username = trim($username);
    if ($username === '') {
        throw new InvalidArgumentException('Username cannot be empty');
    }
    $store = load_store();
    if (isset($store[$username])) {
        throw new RuntimeException('Admin already exists');
    }
    $hash = hash_password($password);
    $store[$username] = [
        'hash' => $hash,
        'created_at' => (new DateTimeImmutable())->format(DATE_ATOM),
    ];
    save_store($store);
}

function authenticate_admin(string $username, string $password): bool
{
    $store = load_store();
    if (!isset($store[$username])) {
        // Resist username enumeration by sleeping briefly
        usleep(50000);
        return false;
    }
    $hash = $store[$username]['hash'];
    $ok = verify_password($password, $hash);
    if ($ok && needs_rehash($hash)) {
        // Re-hash with current params and update store
        $store[$username]['hash'] = hash_password($password);
        save_store($store);
    }
    return $ok;
}

function change_admin_password(string $username, string $newPassword): void
{
    $store = load_store();
    if (!isset($store[$username])) {
        throw new RuntimeException('Admin not found');
    }
    $store[$username]['hash'] = hash_password($newPassword);
    $store[$username]['updated_at'] = (new DateTimeImmutable())->format(DATE_ATOM);
    save_store($store);
}

function delete_admin(string $username): void
{
    $store = load_store();
    if (isset($store[$username])) {
        unset($store[$username]);
        save_store($store);
    }
}

function list_admins(): array
{
    $store = load_store();
    return array_keys($store);
}

// Small CLI helper when executed directly (for admin use only)
if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['argv'][0])) {
    echo "Flat-file admin helper\n";
    $cmd = $argv[1] ?? '';
    try {
        switch ($cmd) {
            case 'create':
                [$u, $p] = [$argv[2] ?? '', $argv[3] ?? ''];
                create_admin($u, $p);
                echo "Created admin: $u\n";
                break;
            case 'list':
                foreach (list_admins() as $a) {
                    echo "- $a\n";
                }
                break;
            default:
                echo "Usage: php flatfile_auth.php create <user> <pass> | list\n";
                break;
        }
    } catch (Throwable $e) {
        fwrite(STDERR, 'Error: ' . $e->getMessage() . "\n");
        exit(1);
    }
}
