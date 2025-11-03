<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login | Student Parliament</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
   <?php
      session_start();

      if (!empty($_SESSION['msg'])) {
         echo "
            <div class='alert alert-warning alert-dismissible fade show m-3' role='alert'>
               <strong>{$_SESSION['msg']}</strong>
               <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
         unset($_SESSION['msg']);
      }

      if (!empty($_SESSION['msg_success'])) {
         echo "
            <div class='alert alert-success alert-dismissible fade show m-3' role='alert'>
               <strong>{$_SESSION['msg_success']}</strong>
               <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
         unset($_SESSION['msg_success']);
      }
   ?>

   <div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
      <form class="border p-4 rounded shadow bg-white w-50" action="loginDemo.php" method="POST">
         <h3 class="text-center mb-4">Student Parliament Login</h3>

         <div class="mb-3">
            <label for="username" class="form-label">Email</label>
            <input type="email" class="form-control" id="username" name="email" required>
         </div>

         <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
         </div>

         <button type="submit" name="btnSubmit" class="btn btn-primary w-100">Login</button>
         <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign up</a></p>
      </form>
   </div>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>