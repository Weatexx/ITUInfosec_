<?php
/**
 * Security Utilities for Admin Panel
 * Features: CSRF protection, input validation, output encoding, file handling
 */

declare(strict_types=1);

// ============================================================================
// CSRF TOKEN MANAGEMENT
// ============================================================================

/**
 * Initialize CSRF token for session
 */
function initializeCsrfToken(): void
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

/**
 * Get CSRF token for forms
 */
function getCsrfToken(): string
{
    initializeCsrfToken();
    return $_SESSION['csrf_token'];
}

/**
 * Generate HTML for CSRF token input field
 */
function getCsrfTokenField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(getCsrfToken()) . '">';
}

/**
 * Validate CSRF token from POST request
 */
function validateCsrfToken(): bool
{
    initializeCsrfToken();
    
    if (!isset($_POST['csrf_token'])) {
        return false;
    }
    
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

/**
 * Die with error if CSRF token is invalid
 */
function requireCsrfToken(): void
{
    if (!validateCsrfToken()) {
        http_response_code(403);
        die('CSRF token validation failed. Request rejected.');
    }
}

// ============================================================================
// INPUT VALIDATION & SANITIZATION
// ============================================================================

/**
 * Validate and sanitize text input
 */
function validateText(string $input, int $maxLength = 255, bool $required = true): ?string
{
    $input = trim($input);
    
    if ($required && empty($input)) {
        return null;
    }
    
    if (strlen($input) > $maxLength) {
        return null;
    }
    
    // Remove null bytes
    $input = str_replace("\0", '', $input);
    
    // Reject patterns with HTML/XML tags
    if (preg_match('/<[^>]*>/', $input)) {
        return null;
    }
    
    // Reject common XSS event handlers (case-insensitive checks)
    $lower_input = strtolower($input);
    $dangerousKeywords = [
        'javascript:',
        'data:text/html',
        'vbscript:',
        'file://',
    ];
    
    foreach ($dangerousKeywords as $keyword) {
        if (strpos($lower_input, $keyword) !== false) {
            return null;
        }
    }
    
    // Reject event handlers like ondclick=, onerror=, etc.
    if (preg_match('/\bon\w+\s*=/i', $input)) {
        return null;
    }
    
    // Reject directory traversal patterns
    if (preg_match('/\.\.[\\/\\\\]|\.\.%2[fF]/', $input)) {
        return null;
    }
    
    return $input;
}

/**
 * Validate and sanitize email
 */
function validateEmail(string $email): ?string
{
    $email = trim($email);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }
    
    return null;
}

/**
 * Validate and sanitize URL
 */
function validateUrl(string $url, bool $required = true): ?string
{
    $url = trim($url);
    
    if (!$required && empty($url)) {
        return '';
    }
    
    // Reject dangerous protocols
    $dangerousProtocols = ['javascript:', 'data:', 'vbscript:', 'file://'];
    foreach ($dangerousProtocols as $protocol) {
        if (stripos($url, $protocol) === 0) {
            return null;
        }
    }
    
    // For HTTP/HTTPS URLs, validate as URL
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Additional check: ensure it's http or https
        if (preg_match('#^https?://#i', $url)) {
            return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        }
    }
    
    return null;
}

/**
 * Validate integer
 */
function validateInteger($input, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): ?int
{
    $filtered = filter_var($input, FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => $min,
            'max_range' => $max
        ]
    ]);
    
    return $filtered !== false ? $filtered : null;
}

/**
 * Validate that ID exists and user has access
 */
function validateId($id): ?int
{
    $validated = validateInteger($id, 1, PHP_INT_MAX);
    return $validated !== null ? $validated : null;
}

/**
 * Sanitize array of POST data (basic)
 */
function sanitizePostArray(array $allowedKeys): array
{
    $result = [];
    
    foreach ($allowedKeys as $key) {
        if (isset($_POST[$key])) {
            $value = $_POST[$key];
            if (is_string($value)) {
                $result[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            } else {
                $result[$key] = $value;
            }
        }
    }
    
    return $result;
}

// ============================================================================
// OUTPUT ENCODING
// ============================================================================

/**
 * Safe HTML output for user-controlled content
 */
function safeHtml(string $content): string
{
    return htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Safe attribute output
 */
function safeAttr(string $content): string
{
    return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
}

/**
 * Safe JavaScript string output
 */
function safeJs(string $content): string
{
    return json_encode($content, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APO | JSON_HEX_QUOT);
}

// ============================================================================
// FILE UPLOAD SECURITY
// ============================================================================

/**
 * Validate and process uploaded image
 * 
 * @param array $file $_FILES['fieldname']
 * @param string $uploadDir Directory to save file (relative to admin/)
 * @param int $maxSize Max file size in bytes
 * @return string|null Path to uploaded file or null on failure
 */
function processImageUpload(array $file, string $uploadDir = 'uploads/', int $maxSize = 5242880): ?string
{
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Ensure upload directory exists
    $uploadDir = realpath(dirname(__FILE__)) . '/' . $uploadDir;
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }
    
    // Verify file is image
    $check = @getimagesize($file['tmp_name']);
    if ($check === false) {
        return null;
    }
    
    // Validate file size
    if ($file['size'] > $maxSize) {
        return null;
    }
    
    // Validate MIME type (whitelist approach)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes, true)) {
        return null;
    }
    
    // Generate secure filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $ext = strtolower(preg_replace('/[^a-z0-9]/', '', $ext));
    
    // Whitelist extensions
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowedExts, true)) {
        return null;
    }
    
    // Create unique filename with timestamp
    $newFilename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $uploadPath = $uploadDir . '/' . $newFilename;
    
    // Move file
    if (!@move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return null;
    }
    
    // Set secure permissions
    @chmod($uploadPath, 0644);
    
    // Return relative path for storage
    return 'uploads/' . $newFilename;
}

/**
 * Delete uploaded file safely
 */
function deleteUploadedFile(string $filePath): bool
{
    // Prevent directory traversal
    if (strpos($filePath, '..') !== false || strpos($filePath, '/') !== false) {
        return false;
    }
    
    $fullPath = realpath(dirname(__FILE__)) . '/uploads/' . basename($filePath);
    
    // Ensure file is in uploads directory
    $uploadsDir = realpath(dirname(__FILE__)) . '/uploads';
    if (strpos($fullPath, $uploadsDir) !== 0) {
        return false;
    }
    
    if (file_exists($fullPath) && is_file($fullPath)) {
        return @unlink($fullPath);
    }
    
    return false;
}

// ============================================================================
// SECURITY HEADERS
// ============================================================================

/**
 * Set security headers for admin panel
 */
function setSecurityHeaders(): void
{
    // Prevent clickjacking
    header('X-Frame-Options: DENY', true);
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff', true);
    
    // Enable XSS protection
    header('X-XSS-Protection: 1; mode=block', true);
    
    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://code.jquery.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net;", true);
    
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin', true);
    
    // Disable caching for sensitive pages
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0', true);
    header('Pragma: no-cache', true);
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT', true);
    
    // HSTS (Strict-Transport-Security) - enable in production with HTTPS
    // header('Strict-Transport-Security: max-age=31536000; includeSubDomains', true);
}

// ============================================================================
// SESSION SECURITY
// ============================================================================

/**
 * Configure secure session settings
 */
function secureSessionConfiguration(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        $cookieOptions = [
            'lifetime' => 3600, // 1 hour
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'] ?? '',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', // HTTPS only
            'httponly' => true, // No JavaScript access
            'samesite' => 'Strict', // CSRF protection
        ];
        
        session_set_cookie_params($cookieOptions);
        session_start();
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['_session_created'])) {
            $_SESSION['_session_created'] = time();
        } elseif (time() - $_SESSION['_session_created'] > 1800) { // Every 30 minutes
            session_regenerate_id(true);
            $_SESSION['_session_created'] = time();
        }
    }
}

/**
 * Require secure session before executing admin functions
 */
function requireSecureSession(): void
{
    secureSessionConfiguration();
    
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit;
    }
    
    initializeCsrfToken();
}

// ============================================================================
// RATE LIMITING (Simple in-memory approach)
// ============================================================================

/**
 * Check if action is rate limited for this IP
 * Note: This is filesystem-based, not ideal for high traffic
 */
function isRateLimited(string $identifier, int $maxAttempts = 5, int $windowSeconds = 300): bool
{
    $dataDir = dirname(__FILE__) . '/../data';
    if (!is_dir($dataDir)) {
        @mkdir($dataDir, 0755, true);
    }
    
    $rateLimitFile = $dataDir . '/.ratelimit_' . hash('sha256', $identifier) . '.json';
    
    $attempts = [];
    if (file_exists($rateLimitFile)) {
        $attempts = json_decode(file_get_contents($rateLimitFile), true) ?? [];
    }
    
    // Clean old attempts
    $now = time();
    $attempts = array_filter($attempts, function($timestamp) use ($now, $windowSeconds) {
        return $timestamp > ($now - $windowSeconds);
    });
    
    if (count($attempts) >= $maxAttempts) {
        return true;
    }
    
    // Record new attempt
    $attempts[] = $now;
    file_put_contents($rateLimitFile, json_encode($attempts), LOCK_EX);
    @chmod($rateLimitFile, 0644);
    
    return false;
}

/**
 * Reset rate limit for identifier
 */
function resetRateLimit(string $identifier): void
{
    $dataDir = dirname(__FILE__) . '/../data';
    $rateLimitFile = $dataDir . '/.ratelimit_' . hash('sha256', $identifier) . '.json';
    @unlink($rateLimitFile);
}

// ============================================================================
// VALIDATION HELPERS FOR SPECIFIC DOMAINS
// ============================================================================

/**
 * Validate speaker data
 */
function validateSpeakerData(array $data): array
{
    $errors = [];
    
    if (empty($data['name'])) {
        $errors['name'] = 'İsim gerekli';
    } elseif (strlen($data['name']) > 255) {
        $errors['name'] = 'İsim çok uzun';
    }
    
    if (empty($data['expertise'])) {
        $errors['expertise'] = 'Uzmanlık gerekli';
    } elseif (strlen($data['expertise']) > 255) {
        $errors['expertise'] = 'Uzmanlık çok uzun';
    }
    
    if (!empty($data['profile_url'])) {
        if (!filter_var($data['profile_url'], FILTER_VALIDATE_URL)) {
            $errors['profile_url'] = 'Geçersiz URL';
        }
    }
    
    return $errors;
}

/**
 * Validate sponsor data
 */
function validateSponsorData(array $data): array
{
    $errors = [];
    
    if (empty($data['name'])) {
        $errors['name'] = 'İsim gerekli';
    } elseif (strlen($data['name']) > 255) {
        $errors['name'] = 'İsim çok uzun';
    }
    
    if (!empty($data['website'])) {
        if (!filter_var($data['website'], FILTER_VALIDATE_URL)) {
            $errors['website'] = 'Geçersiz URL';
        }
    }
    
    return $errors;
}

?>
