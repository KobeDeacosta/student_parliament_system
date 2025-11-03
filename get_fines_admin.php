<?php
require_once "../dbconnection.php";

header("Content-Type: text/html; charset=UTF-8");

$department = $_GET['department'] ?? '';

$query = "SELECT department, user_id, amount, reason, date 
          FROM students_fines";

if (!empty($department)) {
  $query .= " WHERE department = :dept";
}

$query .= " ORDER BY department, date DESC";

$stmt = $conn->prepare($query);

if (!empty($department)) {
  $stmt->bindParam(':dept', $department);
}

$stmt->execute();
$fines = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$fines) {
  echo "<tr><td colspan='5' class='text-center text-muted'>No fines found.</td></tr>";
  exit;
}

$currentDept = "";
foreach ($fines as $fine) {
  if ($currentDept != $fine['department']) {
    $currentDept = $fine['department'];
    echo "<tr class='table-primary text-center fw-bold'>
            <td colspan='5'>Department: {$currentDept}</td>
          </tr>";
  }

  echo "<tr class='text-center'>
          <td>{$fine['user_id']}</td>
          <td>{$fine['reason']}</td>
          <td>{$fine['amount']}</td>
          <td>{$fine['date']}</td>
          <td>{$fine['department']}</td>
        </tr>";
}
?>
