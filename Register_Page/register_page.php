<?php
$student_fname = $_COOKIE["student_first_name"];
$student_lname = $_COOKIE["student_last_name"];
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IASMS</title>

  <link rel="stylesheet" href="../css/bootstrap-theme.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap-select.css"/>
  <link rel="stylesheet" href="../css/main_page_style.css"/>
  <link rel="stylesheet" href="register_page.css"/>

  <script type="text/javascript" src="../js/jquery-3.1.1.min.js"/></script>
  <script type="text/javascript" src="../js/bootstrap.min.js"/></script>

</head>
<body>

<?php $topbar_logo_src = '../img/header_log.png'; include '../includes/student_topbar.php'; ?>

<div id="left_side_bar">
<ul id="menu_list">
  <a class="menu_items_link" href="../instructions_page/instructions_page.php"><li class="menu_items_list">Instructions</li></a>
  <a class="menu_items_link" href="register_page.php"><li class="menu_items_list"style="background-color:orange;padding-left:16px">Register</li></a>
  <a class="menu_items_link" href="../student_assumption/student_assumption.php"><li class="menu_items_list">Submit Assupmtion</li></a>
  <a class="menu_items_link" href="../e-logbook/elogbook_dynamic.php"><li class="menu_items_list">E-Logbook</li></a>
  <a class="menu_items_link" href="../submit_contract.php"><li class="menu_items_list">Submit Contract</li></a>
  <a class="menu_items_link" href="../company_supervisor/company_supervisor_login.php"><li class="menu_items_list">Company Supervisor</li></a>
  <a class="menu_items_link" href="../visiting_supervisor/visiting_supervisor_login.php"><li class="menu_items_list">Visiting Supervisor</li></a>
  <a class="menu_items_link" href="../submit_report/submit_report.php"><li class="menu_items_list">Submit Report</li></a>
  <a class="menu_items_link" href="../index.php"><li class="menu_items_list">Logout</li></a>
</ul>
</div>

<div id="main_content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="panel">
          <div class="panel-body pbody pbody_industrial">
            <span>Register for Industrial Attachment.<br><em style="font-weight:bold;color:#2B3775">Please click the button below to register.</em></span>
            <br><br>
            <a href="industrial_registration_page.php" class="btn btn-primary btn-medium" style="padding:10px;color:white">Register (Industrial)</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
