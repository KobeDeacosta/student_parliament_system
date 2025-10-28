<?php
include('dbconnection.php');

date_default_timezone_set('Asia/Manila');
$today = date('Y-m-d');

try {
    // ✅ Get all attendance records for today
    $stmt = $pdo->prepare("
        SELECT a.*, u.username AS name, u.id AS user_id, a.event_id
        FROM attendance a
        INNER JOIN users u ON a.user_id = u.id
        WHERE DATE(a.date) = ?
    ");
    $stmt->execute([$today]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($records)) {
        echo "⚠ No attendance records found for $today.";
        exit;
    }

    foreach ($records as $row) {
        $missing = 0;
        $fields = ['am_in', 'am_out', 'pm_in', 'pm_out'];

        // Count missing scans
        foreach ($fields as $f) {
            if (empty($row[$f]) || $row[$f] === '0000-00-00 00:00:00') {
                $missing++;
            }
        }

        // Only add fines if there are missing scans
        if ($missing > 0) {
            $fineAmount = $missing * 50; // ₱50 per missing scan

            // Check if fine already exists
            $check = $pdo->prepare("
                SELECT id FROM fines 
                WHERE user_id = ? AND event_id = ? AND date = ?
            ");
            $check->execute([$row['user_id'], $row['event_id'], $today]);
            $existing = $check->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Update existing fine
                $update = $pdo->prepare("
                    UPDATE fines 
                    SET missing_scans = ?, total_fine = ?
                    WHERE id = ?
                ");
                $update->execute([$missing, $fineAmount, $existing['id']]);
            } else {
                // Insert new fine
                $insert = $pdo->prepare("
                    INSERT INTO fines (user_id, event_id, date, missing_scans, total_fine)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $insert->execute([$row['user_id'], $row['event_id'], $today, $missing, $fineAmount]);
            }
        }
    }

    echo "✅ Fines calculated successfully for $today.";

} catch (PDOException $e) {
    echo "❌ Database Error: " . htmlspecialchars($e->getMessage());
}
?>
