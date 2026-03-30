<?php
require_once 'security_utils.php';
require_once 'data_manager.php';

requireSecureSession();

$response = ['success' => false, 'message' => ''];

// Check CSRF token
if (!validateCsrfToken()) {
    http_response_code(403);
    $response['message'] = 'CSRF token kontrolü başarısız.';
    echo json_encode($response);
    exit;
}

$id = validateInteger($_POST['id'] ?? null, 1, PHP_INT_MAX);

if ($id === null) {
    $response['message'] = 'Geçersiz konuşmacı ID\'si.';
    echo json_encode($response);
    exit;
}

if ($dataManager->deleteSpeaker($id)) {
    $response['success'] = true;
    $response['message'] = 'Konuşmacı başarıyla silindi.';
    echo json_encode($response);
} else {
    $response['message'] = 'Silme işlemi başarısız.';
    echo json_encode($response);
}
?>
