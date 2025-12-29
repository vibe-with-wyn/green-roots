<?php
session_start();
require_once '../includes/config.php';

// Restrict access to eco_validator role only
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'eco_validator') {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$validator_user_id = (int)$_SESSION['user_id'];
$submission_id = isset($_GET['submission_id']) ? (int)$_GET['submission_id'] : 0;

if ($submission_id <= 0) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

try {
    // Get validator barangay
    $stmt = $pdo->prepare("SELECT barangay_id FROM users WHERE user_id = :user_id LIMIT 1");
    $stmt->execute([':user_id' => $validator_user_id]);
    $validator_barangay_id = (int)($stmt->fetchColumn() ?? 0);

    if ($validator_barangay_id <= 0) {
        header('HTTP/1.1 403 Forbidden');
        exit;
    }

    // Fetch photo only if submission belongs to validator's barangay
    $stmt = $pdo->prepare(
        "SELECT photo_data FROM submissions WHERE submission_id = :submission_id AND barangay_id = :barangay_id LIMIT 1"
    );
    $stmt->execute([
        ':submission_id' => $submission_id,
        ':barangay_id' => $validator_barangay_id,
    ]);

    $photo_data = $stmt->fetchColumn();

    if (!$photo_data) {
        header('HTTP/1.1 404 Not Found');
        exit;
    }

    // Detect mime type (fallback to jpeg)
    $mime_type = 'image/jpeg';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $detected = finfo_buffer($finfo, $photo_data);
            finfo_close($finfo);
            if (is_string($detected) && $detected !== '') {
                $mime_type = $detected;
            }
        }
    }

    header('Content-Type: ' . $mime_type);
    header('Cache-Control: private, max-age=300');
    echo $photo_data;
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    exit;
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    exit;
}
