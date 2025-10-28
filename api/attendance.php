<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once "../dbconnection.php";

$data = json_decode(file_get_contents("php://input"), true);
$qrData = $data["qrData"] ?? "";

if (empty($qrData)) {
    echo json_encode(["message" => "Invalid QR data!"]);
    exit;
}

$parts = explode("_", $qrData);
if (count($parts) < 2) {
    echo json_encode(["message" => "QR data format invalid!"]);
    exit;
}

$name = trim($parts[0]);   
$course = trim($parts[1]);

$pdo = new PDO("mysql:host=localhost;dbname=student_parliament", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ito ay panghanap ng student ha Kobe
$stmt = $pdo->prepare("SELECT id FROM students WHERE student_name = ?");
$stmt->execute([$name]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo json_encode(["message" => "Student not found in database"]);
    exit;
}

$student_id = $student["id"];
$today = date("Y-m-d");

// Dito kobe ichecheck mo if naka scan na sya
$check = $pdo->prepare("SELECT * FROM attendance WHERE student_id = ? AND date = ?");
$check->execute([$student_id, $today]);

if ($check->rowCount() > 0) {
    echo json_encode(["message" => "$name already marked present today."]);
    exit;
}

$insert = $pdo->prepare("INSERT INTO attendance (student_id, course, date, status) VALUES (?, ?, ?, 'Present')");
$insert->execute([$student_id, $course, $today]);

echo json_encode(["message" => "Attendance marked for $name ($course)"]);
?>
