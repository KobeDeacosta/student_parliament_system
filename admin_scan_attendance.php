<?php
session_start();
include('dbconnection.php');

// Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['qrData']) && isset($_POST['attendance_type'])) {
    $qrData = trim($_POST['qrData']);
    $attendance_type = trim($_POST['attendance_type']);

    // Split QR data: "ID&Fullname&Department"
    $parts = explode('&', $qrData);
    $id_number = $parts[0] ?? '';
    $fullname  = $parts[1] ?? '';
    $department = $parts[2] ?? '';

    if (!empty($id_number) && !empty($fullname)) {
        // Check kung pumasok ang student
        $check = $pdo->prepare("SELECT id FROM users WHERE id_number = ?");
        $check->execute([$id_number]);
        $user = $check->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_id = $user['id'];

            $eventQuery = $pdo->query("
                SELECT id, event_name FROM institutional_events
                WHERE status = 'active'
                ORDER BY id DESC LIMIT 1
            ");
            $event = $eventQuery->fetch(PDO::FETCH_ASSOC);

            if ($event) {
                $event_id = $event['id'];
                $now = date('Y-m-d H:i:s');
                $today = date('Y-m-d');

                $checkAttendance = $pdo->prepare("
                    SELECT * FROM attendance
                    WHERE user_id = ? AND event_id = ? AND date = ?
                ");
                $checkAttendance->execute([$user_id, $event_id, $today]);
                $record = $checkAttendance->fetch(PDO::FETCH_ASSOC);

                if ($record) {
                    if (!empty($record[$attendance_type])) {
                        $_SESSION['msg_error'] = "⚠ {$attendance_type} already recorded for $fullname!";
                    } else {
                        $update = $pdo->prepare("
                            UPDATE attendance 
                            SET $attendance_type = ?, scan_time = ?, status = 'Present'
                            WHERE id = ?
                        ");
                        $update->execute([$now, $now, $record['id']]);
                        $_SESSION['msg_success'] = "✅ Updated $attendance_type for $fullname!";
                    }
                } else {
                    $insert = $pdo->prepare("
                        INSERT INTO attendance (user_id, name, department, event_id, scan_time, status, date, $attendance_type)
                        VALUES (?, ?, ?, ?, ?, 'Present', ?, ?)
                    ");
                    $insert->execute([$user_id, $fullname, $department, $event_id, $now, $today, $now]);
                    $_SESSION['msg_success'] = "✅ First attendance record saved for $fullname!";
                }

                $stmt = $pdo->prepare("
                    SELECT * FROM attendance WHERE user_id = ? AND event_id = ? AND date = ?
                ");
                $stmt->execute([$user_id, $event_id, $today]);
                $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

                $missing = 0;
                if (empty($attendance['am_in']))  $missing++;
                if (empty($attendance['am_out'])) $missing++;
                if (empty($attendance['pm_in']))  $missing++;
                if (empty($attendance['pm_out'])) $missing++;

                $fineAmount = $missing * 50;

                $checkFine = $pdo->prepare("
                    SELECT * FROM fines WHERE user_id = ? AND event_id = ? AND date = ?
                ");
                $checkFine->execute([$user_id, $event_id, $today]);
                $existingFine = $checkFine->fetch(PDO::FETCH_ASSOC);

                if ($missing > 0) {
                    if ($existingFine) {
                        // Update fine
                        $updateFine = $pdo->prepare("
                            UPDATE fines
                            SET missing_scans = ?, total_fine = ?, created_at = NOW()
                            WHERE id = ?
                        ");
                        $updateFine->execute([$missing, $fineAmount, $existingFine['id']]);
                    } else {
                        // Insert fine
                        $insertFine = $pdo->prepare("
                            INSERT INTO fines (user_id, event_id, date, missing_scans, total_fine, created_at)
                            VALUES (?, ?, ?, ?, ?, NOW())
                        ");
                        $insertFine->execute([$user_id, $event_id, $today, $missing, $fineAmount]);
                    }
                } else {
                    if ($existingFine) {
                        $deleteFine = $pdo->prepare("DELETE FROM fines WHERE id = ?");
                        $deleteFine->execute([$existingFine['id']]);
                    }
                }

            } else {
                $_SESSION['msg_error'] = "⚠ No active event found.";
            }
        } else {
            $_SESSION['msg_error'] = "Student not found.";
        }
    } else {
        $_SESSION['msg_error'] = "Invalid or incomplete QR data.";
    }

    header("Location: admin_scan_attendance.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | QR Attendance Scanner</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/html5-qrcode"></script>
  <style>
      body { background: #f8f9fa; }
      .scanner-container {
          max-width: 550px;
          margin: 50px auto;
          background: white;
          padding: 25px;
          border-radius: 10px;
          box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      }
      #reader { width: 100%; border: 2px solid #0d6efd; border-radius: 10px; }
  </style>
</head>
<body>

<div class="container">
  <div class="scanner-container">
      <h3 class="text-center mb-4">📷 QR Code Attendance Scanner (Admin)</h3>

      <?php
      if (!empty($_SESSION['msg_success'])) {
          echo '<div class="alert alert-success text-center">'.$_SESSION['msg_success'].'</div>';
          unset($_SESSION['msg_success']);
      }
      if (!empty($_SESSION['msg_error'])) {
          echo '<div class="alert alert-danger text-center">'.$_SESSION['msg_error'].'</div>';
          unset($_SESSION['msg_error']);
      }
      ?>

      <form id="qrForm" action="admin_scan_attendance.php" method="post">
          <div class="mb-3">
              <label class="form-label fw-bold">Select Attendance Type:</label>
              <select name="attendance_type" id="attendance_type" class="form-select" required>
                  <option value="">-- Select Type --</option>
                  <option value="am_in">AM IN</option>
                  <option value="am_out">AM OUT</option>
                  <option value="pm_in">PM IN</option>
                  <option value="pm_out">PM OUT</option>
              </select>
          </div>

          <input type="hidden" name="qrData" id="qrData">
      </form>

      <div id="reader"></div>

      <div class="text-center mt-3">
          <a href="student-dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
      </div>
  </div>
</div>

<script>
const html5QrCode = new Html5Qrcode("reader");

function onScanSuccess(decodedText) {
    const type = document.getElementById('attendance_type').value;
    if (!type) {
        alert("⚠ Please select attendance type before scanning!");
        return;
    }

    html5QrCode.stop().then(() => {
        document.getElementById('qrData').value = decodedText;
        document.getElementById('qrForm').submit();
    }).catch(err => console.error("Stop error:", err));
}

function onScanFailure(error) {
    
}

html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 250 },
    onScanSuccess,
    onScanFailure
);
</script>

</body>
</html>
