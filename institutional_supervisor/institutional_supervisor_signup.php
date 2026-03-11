<?php
include '../database_connection/database_connection.php';

// Ensure assignment table exists (supervisor_id references visiting_lecturers.id)
$sql_students = "CREATE TABLE IF NOT EXISTS institutional_supervisor_students (
  id INT(11) NOT NULL AUTO_INCREMENT,
  supervisor_id INT(11) NOT NULL,
  student_index VARCHAR(100) NOT NULL,
  visit_number TINYINT(1) NOT NULL,
  PRIMARY KEY (id),
  KEY supervisor_id (supervisor_id),
  KEY student_index (student_index)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";
mysqli_query($conn, $sql_students);

$error_message = "";
$success_message = "";

if (isset($_POST['btn_signup'])) {
  $full_name = trim($_POST['full_name'] ?? '');
  $staff_id = trim($_POST['staff_id'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $confirm_password = trim($_POST['confirm_password'] ?? '');

  if ($full_name === '' || $staff_id === '' || $email === '' || $phone === '' || $password === '' || $confirm_password === '') {
    $error_message = "Please fill in all fields.";
  } elseif ($password !== $confirm_password) {
    $error_message = "Password and Confirm Password do not match.";
  } else {
    $staff_id_safe = mysqli_real_escape_string($conn, $staff_id);

    $check_query = "SELECT id FROM visiting_lecturers WHERE staff_id='$staff_id_safe' LIMIT 1";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
      $error_message = "An account with this Staff ID already exists.";
    } else {
      $full_name_safe = mysqli_real_escape_string($conn, $full_name);
      $email_safe = mysqli_real_escape_string($conn, $email);
      $phone_safe = mysqli_real_escape_string($conn, $phone);
      $password_safe = mysqli_real_escape_string($conn, $password);

      $insert_query = "INSERT INTO visiting_lecturers 
        (lecturer_name, lecturer_faculty, lecturer_phone_number, lecturer_region_residence, lecturer_department, lecturer_email, staff_id, password) 
        VALUES ('$full_name_safe', '', '$phone_safe', '', '', '$email_safe', '$staff_id_safe', '$password_safe')";

      if (mysqli_query($conn, $insert_query)) {
        $new_id = mysqli_insert_id($conn);

        setcookie("inst_supervisor_id", $new_id, time() + (86400 * 30), "/");
        setcookie("inst_supervisor_name", $full_name, time() + (86400 * 30), "/");
        setcookie("inst_supervisor_staff_id", $staff_id, time() + (86400 * 30), "/");

        header("Location: dashboard.php");
        exit();
      } else {
        $error_message = "Unable to create account. Please try again.";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IASMS - Institutional Supervisor Signup</title>

  <link rel="stylesheet" href="../css/bootstrap-theme.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap-select.css"/>
  <link rel="stylesheet" href="../css/main_page_style.css"/>

  <script type="text/javascript" src="../js/jquery-3.1.1.min.js"></script>
  <script type="text/javascript" src="../js/bootstrap.min.js"></script>

  <style>
    .signup-panel {
      max-width: 550px;
      margin: 40px auto;
    }
  </style>
</head>
<body>

<?php $topbar_display_name = 'Institutional Supervisor'; $topbar_logo_src = '../img/header_log.png'; include '../includes/topbar.php'; ?>

<div class="container-fluid">
  <div class="panel signup-panel">
    <div class="panel-heading phead">
      <h2 class="panel-title ptitle">Sign Up - Institutional Supervisor</h2>
    </div>
    <form method="post" action="">
      <div class="panel-body pbody">

        <?php if ($error_message !== ""): ?>
          <div class="alert alert-danger">
            <?php echo $error_message; ?>
          </div>
        <?php endif; ?>

        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" class="form-control" id="full_name" name="full_name"
                 placeholder="Enter Full Name"
                 value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
        </div>

        <div class="form-group">
          <label for="staff_id">Staff ID</label>
          <input type="text" class="form-control" id="staff_id" name="staff_id"
                 placeholder="Enter Staff ID"
                 value="<?php echo isset($_POST['staff_id']) ? htmlspecialchars($_POST['staff_id']) : ''; ?>">
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" class="form-control" id="email" name="email"
                 placeholder="Enter Email"
                 value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="text" class="form-control" id="phone" name="phone"
                 placeholder="Enter Phone Number"
                 value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" name="password"
                 placeholder="Enter Password">
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                 placeholder="Confirm Password">
        </div>

        <div class="form-group" style="margin-top:20px;">
          <button type="submit" name="btn_signup" class="btn btn-primary pull-right">
            Sign Up
          </button>
        </div>

        <div style="clear:both;"></div>
        <hr>
        <p>
          Already have an account?
          <a href="institutional_supervisor_login.php">Sign in here</a>
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

