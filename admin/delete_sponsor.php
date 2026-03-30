<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();

$response = ['success' => false, 'message' => ''];

// Debug logging
error_log('Delete Sponsor Request - POST data: ' . json_encode($_POST));

// Check CSRF token
if (!validateCsrfToken()) {
    error_log('CSRF Token validation failed');
    http_response_code(403);
    $response['message'] = 'CSRF token kontrolü başarısız.';
    echo json_encode($response);
    exit;
}

$id = validateInteger($_POST['id'] ?? null, 1, PHP_INT_MAX);
error_log('Validated ID: ' . ($id === null ? 'NULL' : $id));

if ($id === null) {
    $response['message'] = 'Geçersiz sponsor ID\'si.';
    echo json_encode($response);
    exit;
}

error_log('Deleting sponsor with ID: ' . $id);
if ($dataManager->deleteSponsor($id)) {
    error_log('Sponsor deleted successfully');
    $response['success'] = true;
    $response['message'] = 'Sponsor başarıyla silindi.';
    echo json_encode($response);
} else {
    error_log('Failed to delete sponsor');
    $response['message'] = 'Silme işlemi başarısız.';
    echo json_encode($response);
}
?>