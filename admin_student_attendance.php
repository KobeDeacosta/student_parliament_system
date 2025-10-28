<?php
session_start();
include('dbconnection.php');

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance by Department</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body { background-color: #f8f9fa; }
    .container { max-width: 1000px; }
    table th, table td { vertical-align: middle; }
  </style>
</head>
<body>

<div class="container py-5">
  <h2 class="text-center mb-4">📋 Department Attendance Records For Admin</h2>

  <!-- Department Dropdown -->
  <div class="mb-4 text-center">
    <label for="departmentSelect" class="form-label fw-bold">Select Department:</label>
    <select id="departmentSelect" class="form-select w-50 mx-auto">
      <option value="">-- Select Department --</option>
      <?php
      try {
          // Fetch departments from 'departments' table
          $query = $pdo->query("SELECT department_name FROM departments ORDER BY department_name ASC");
          $departments = $query->fetchAll(PDO::FETCH_ASSOC);

          if (count($departments) > 0) {
              foreach ($departments as $row) {
                  $dept = htmlspecialchars($row['department_name']);
                  echo "<option value='{$dept}'>{$dept}</option>";
              }
          } else {
              echo "<option disabled>No departments found</option>";
          }
      } catch (PDOException $e) {
          echo "<option disabled>Error: " . htmlspecialchars($e->getMessage()) . "</option>";
      }
      ?>
    </select>
  </div>  

  <!--  Attendance Table -->
  <div id="attendanceSection" style="display:none;">
    <table class="table table-bordered table-striped align-middle text-center shadow-sm">
      <thead class="table-dark">
        <tr>
          <th>Name</th>
          <th>Department</th>
          <th>Event</th>
          <th>AM IN</th>
          <th>AM OUT</th>
          <th>PM IN</th>
          <th>PM OUT</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody id="attendanceTableBody">
        <tr><td colspan="8">Select a department to view records</td></tr>
      </tbody>
    </table>
  </div>

  <div class="text-center mt-4">
    <a href="student-dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#departmentSelect').change(function() {
    let dept = $(this).val();

    if (dept === "") {
      $('#attendanceSection').hide();
      return;
    }

    $('#attendanceSection').show();
    $('#attendanceTableBody').html("<tr><td colspan='8'>Loading...</td></tr>");

    $.ajax({
      url: "api/get_attendance_today.php",
      method: "GET",
      data: { department: dept },
      success: function(response) {
        $('#attendanceTableBody').html(response);
      },
      error: function() {
        $('#attendanceTableBody').html("<tr><td colspan='8' class='text-danger'>Error loading data</td></tr>");
      }
    });
  });
});
</script>

</body>
</html>
