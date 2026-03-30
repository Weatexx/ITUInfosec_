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
    $response['message'] = 'Geçersiz eğitmen ID\'si.';
    echo json_encode($response);
    exit;
}

// Get instructor to check photo
$instructor = $dataManager->getInstructor($id);

if ($instructor) {
    // Delete photo if exists
    if (!empty($instructor['photo']) && file_exists($instructor['photo'])) {
        @unlink($instructor['photo']);
    }

    if ($dataManager->deleteInstructor($id)) {
        $response['success'] = true;
        $response['message'] = 'Eğitmen başarıyla silindi.';
        echo json_encode($response);
    } else {
        $response['message'] = 'Silme işlemi başarısız.';
        echo json_encode($response);
    }
} else {
    $response['message'] = 'Eğitmen bulunamadı.';
    echo json_encode($response);
}
?>
