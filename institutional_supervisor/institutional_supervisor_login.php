<?php
include '../database_connection/database_connection.php';

// Login from merged table: visiting_lecturers (with staff_id + password = institutional supervisor)
$error_message = "";

if (isset($_POST['btn_login'])) {
  $staff_id = trim($_POST['staff_id'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($staff_id !== '' && $password !== '') {
    $staff_id_safe = mysqli_real_escape_string($conn, $staff_id);
    $password_safe = mysqli_real_escape_string($conn, $password);

    $query = "SELECT id, lecturer_name, staff_id FROM visiting_lecturers 
              WHERE staff_id='$staff_id_safe' AND password='$password_safe' AND staff_id IS NOT NULL LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
      $row = mysqli_fetch_assoc($result);

      setcookie("inst_supervisor_id", $row['id'], time() + (86400 * 30), "/");
      setcookie("inst_supervisor_name", $row['lecturer_name'], time() + (86400 * 30), "/");
      setcookie("inst_supervisor_staff_id", $row['staff_id'], time() + (86400 * 30), "/");

      header("Location: dashboard.php");
      exit();
    } else {
      $error_message = "Invalid Staff ID or Password";
    }
  } else {
    $error_message = "Please enter both Staff ID and Password";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IASMS - Institutional Supervisor Login</title>

  <link rel="stylesheet" href="../css/bootstrap-theme.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap-select.css"/>
  <link rel="stylesheet" href="../css/main_page_style.css"/>

  <script type="text/javascript" src="../js/jquery-3.1.1.min.js"></script>
  <script type="text/javascript" src="../js/bootstrap.min.js"></script>

  <style>
    .login-panel {
      max-width: 450px;
      margin: 60px auto;
    }
  </style>
</head>
<body>

<?php $topbar_display_name = 'Institutional Supervisor'; $topbar_logo_src = '../img/header_log.png'; include '../includes/topbar.php'; ?>

<div class="container-fluid">
  <div class="panel login-panel">
    <div class="panel-heading phead">
      <h2 class="panel-title ptitle">Login - Institutional Supervisor</h2>
    </div>
    <form method="post" action="">
      <div class="panel-body pbody">
        <?php if ($error_message !== ""): ?>
          <div class="alert alert-danger">
            <?php echo $error_message; ?>
          </div>
        <?php endif; ?>

        <div class="form-group">
          <label for="staff_id">Staff ID</label>
          <input type="text" class="form-control" id="staff_id" name="staff_id"
                 placeholder="Enter Staff ID">
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password"
                 placeholder="Enter Password">
        </div>

        <div class="form-group" style="margin-top:20px;">
          <button type="submit" name="btn_login" class="btn btn-primary pull-right">
            Sign In
          </button>
        </div>

        <div style="clear:both;"></div>
        <hr>
        <p>
          New institutional supervisor?
          <a href="institutional_supervisor_signup.php">Sign up here</a>
        </p>
        <p>
          <a href="../index.php">&laquo; Back to Student Login</a>
        </p>
      </div>
    </form>
  </div>
</div>

</body>
</html>

