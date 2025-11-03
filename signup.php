<?php
session_start();
include('dbconnection.php');
require 'phpqrcode/qrlib.php';

if (isset($_POST['btnSignup'])) {

    $id_number = trim($_POST['id_number']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $department_id = trim($_POST['department_id']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($id_number) || empty($fullname) || empty($email) || empty($department_id) || empty($password) || empty($confirm_password)) {
        $_SESSION['msg'] = "All fields are required!";
        header("Location: signup.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['msg'] = "Passwords do not match!";
        header("Location: signup.php");
        exit();
    }

    try {
        $check = $pdo->prepare("SELECT * FROM users WHERE email = ? OR id_number = ?");
        $check->execute([$email, $id_number]);

        if ($check->rowCount() > 0) {
            $_SESSION['msg'] = "Email or ID number already exists!";
            header("Location: signup.php");
            exit();
        }

        $deptStmt = $pdo->prepare("SELECT department_name FROM departments WHERE id = ?");
        $deptStmt->execute([$department_id]);
        $deptRow = $deptStmt->fetch(PDO::FETCH_ASSOC);
        $department_name = $deptRow ? $deptRow['department_name'] : 'N/A';

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (id_number, username, email, department, department_id, password, role)
            VALUES (?, ?, ?, ?, ?, ?, 'student')
        ");
        $stmt->execute([$id_number, $fullname, $email, $department_name, $department_id, $hashedPassword]);

        $user_id = $pdo->lastInsertId();

        // pang Generate ng QR Code
        $qrDir = "qrcodes/";
        if (!file_exists($qrDir)) mkdir($qrDir, 0777, true);

        $qrData = "$id_number | $fullname | $department_name";
        $qrFile = $qrDir . "student_" . $user_id . ".png";

        QRcode::png($qrData, $qrFile, QR_ECLEVEL_L, 5);

        $update = $pdo->prepare("UPDATE users SET qr_code = ? WHERE id = ?");
        $update->execute([$qrFile, $user_id]);

        // ito ay para magrekta log in na
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $fullname;
        $_SESSION['role'] = 'student';
        $_SESSION['department_id'] = $department_id;

        header("Location: user-dashboard.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['msg'] = "Database error: " . $e->getMessage();
        header("Location: signup.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Sign Up</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php
if (!empty($_SESSION['msg'])) {
   echo '<div class="alert alert-warning text-center">'.$_SESSION['msg'].'</div>';
   unset($_SESSION['msg']);
}
?>

<div class="container mt-5">
   <div class="card mx-auto shadow" style="max-width: 450px;">
      <div class="card-body">
         <h4 class="card-title text-center mb-4">Sign Up</h4>
         <form action="signup.php" method="post">
            <div class="mb-3">
               <label class="form-label">ID Number</label>
               <input type="text" class="form-control" name="id_number" placeholder="e.g. 18-01933" required>
            </div>
            <div class="mb-3">
               <label class="form-label">Fullname</label>
               <input type="text" class="form-control" name="fullname" required>
            </div>
            <div class="mb-3">
               <label class="form-label">Email</label>
               <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
               <label class="form-label">Department</label>
               <select class="form-select" name="department_id" required>
                  <option value="">Select Department</option>
                  <?php
                     $dept = $pdo->query("SELECT id, department_name FROM departments");
                     foreach ($dept as $d) {
                        echo "<option value='{$d['id']}'>{$d['department_name']}</option>";
                     }
                  ?>
               </select>
            </div>
            <div class="mb-3">
               <label class="form-label">Password</label>
               <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
               <label class="form-label">Confirm Password</label>
               <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" name="btnSignup" class="btn btn-primary w-100">Create Account</button>
         </form>
         <div class="text-center mt-3">
            <small>Already have an account? <a href="login.php">Login here</a></small>
         </div>
      </div>
   </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
