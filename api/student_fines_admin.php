<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Students Fines by Department</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">

<div class="container py-5">
  <h2 class="text-center mb-4 text-primary">🏫 Students Fines (By Department)</h2>

  <!-- Department Filter -->
  <div class="row mb-4">
    <div class="col-md-4 mx-auto">
      <select id="departmentFilter" class="form-select">
        <option value="">All Departments</option>
        <option value="BSIT">BSIT</option>
        <option value="BSIS">BSIS</option>
        <option value="BSHM">BSHM</option>
        <option value="BSED">BSED</option>
      </select>
    </div>
  </div>

  <!-- Fines Table -->
  <table class="table table-bordered table-striped">
    <thead class="table-dark text-center">
      <tr>
        <th>Student ID</th>
        <th>Reason</th>
        <th>Amount</th>
        <th>Date</th>
        <th>Department</th>
      </tr>
    </thead>
    <tbody id="finesTableBody" class="text-center">
      <tr><td colspan="5">Loading...</td></tr>
    </tbody>
  </table>
</div>

<script>
function loadFines(department = "") {
  const url = "api/student_fines_admin.php" + (department ? `?department=${department}` : "");
  fetch(url)
    .then(response => response.text())
    .then(html => {
      document.getElementById("finesTableBody").innerHTML = html;
    })
    .catch(err => {
      document.getElementById("finesTableBody").innerHTML =
        `<tr><td colspan='5' class='text-danger'>Error loading fines</td></tr>`;
    });
}


loadFines();

document.getElementById("departmentFilter").addEventListener("change", e => {
  loadFines(e.target.value);
});

setInterval(() => loadFines(document.getElementById("departmentFilter").value), 10000);
</script>

</body>
</html>
