<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['submission_id']) || !isset($input['status'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit;
    }

    $submission_id = (int)$input['submission_id'];
    $status = $input['status'];
    $validated_by = $input['validated_by'] ?? null;
    $validated_at = $input['validated_at'] ?? null;
    $rejection_reason = $input['rejection_reason'] ?? null;
    $user_id = isset($input['user_id']) ? (int)$input['user_id'] : null;
    $eco_points = isset($input['eco_points']) ? (int)$input['eco_points'] : 0;
    $trees_planted = isset($input['trees_planted']) ? (int)$input['trees_planted'] : 0;

    if (!in_array($status, ['approved', 'rejected'], true)) {
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        exit;
    }

    $pdo->beginTransaction();

    // Update submission status
    $stmt = $pdo->prepare("
        UPDATE submissions
        SET status = :status,
            validated_by = :validated_by,
            validated_at = :validated_at,
            rejection_reason = :rejection_reason
        WHERE submission_id = :submission_id
    ");
    $stmt->execute([
        ':status' => $status,
        ':validated_by' => $validated_by,
        ':validated_at' => $validated_at,
        ':rejection_reason' => $rejection_reason,
        ':submission_id' => $submission_id
    ]);

    // Update activity row (preferred: exact link)
    $stmt = $pdo->prepare("
        UPDATE activities
        SET status = :status,
            eco_points = :eco_points
        WHERE submission_id = :submission_id
          AND user_id = :user_id
          AND activity_type = 'submission'
        LIMIT 1
    ");
    $stmt->execute([
        ':status' => $status,
        ':eco_points' => ($status === 'approved') ? $eco_points : null,
        ':submission_id' => $submission_id,
        ':user_id' => $user_id
    ]);

    // Fallback: handle legacy rows where submission_id is NULL (match tightly)
    if ($stmt->rowCount() === 0) {
        // pull submission time + location name to reduce false matches
        $metaStmt = $pdo->prepare("
            SELECT s.submitted_at, b.name AS barangay_name
            FROM submissions s
            LEFT JOIN barangays b ON b.barangay_id = s.barangay_id
            WHERE s.submission_id = :submission_id
            LIMIT 1
        ");
        $metaStmt->execute([':submission_id' => $submission_id]);
        $meta = $metaStmt->fetch(PDO::FETCH_ASSOC);

        if ($meta && $user_id) {
            $submitted_at = $meta['submitted_at'];
            $barangay_name = $meta['barangay_name'] ?? null;

            // Update the most likely matching pending submission-activity created near submitted_at
            $legacyStmt = $pdo->prepare("
                UPDATE activities
                SET status = :status,
                    eco_points = :eco_points,
                    submission_id = :submission_id
                WHERE submission_id IS NULL
                  AND user_id = :user_id
                  AND activity_type = 'submission'
                  AND status = 'pending'
                  AND (trees_planted = :trees_planted OR :trees_planted = 0)
                  AND (:location IS NULL OR location = :location)
                  AND created_at BETWEEN DATE_SUB(:submitted_at, INTERVAL 10 MINUTE)
                                   AND DATE_ADD(:submitted_at, INTERVAL 10 MINUTE)
                ORDER BY created_at DESC
                LIMIT 1
            ");
            $legacyStmt->execute([
                ':status' => $status,
                ':eco_points' => ($status === 'approved') ? $eco_points : null,
                ':submission_id' => $submission_id,
                ':user_id' => $user_id,
                ':trees_planted' => $trees_planted,
                ':location' => $barangay_name,
                ':submitted_at' => $submitted_at
            ]);
        }
    }

    // If approved, update user's eco_points and trees_planted
    if ($status === 'approved' && $user_id && $eco_points > 0) {
        $stmt = $pdo->prepare("
            UPDATE users
            SET eco_points = eco_points + :eco_points,
                trees_planted = trees_planted + :trees_planted
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            ':eco_points' => $eco_points,
            ':trees_planted' => $trees_planted,
            ':user_id' => $user_id
        ]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("Database error in update_submission.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("Error in update_submission.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>