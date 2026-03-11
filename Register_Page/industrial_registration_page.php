<?php

include '../database_connection/database_connection.php';

$student_fname = $_COOKIE["student_first_name"];
$student_lname = $_COOKIE["student_last_name"];
$index_number_holder = $_COOKIE["student_index_number"];
$student_index = "";

$programmes = array("-","Accountancy","Applied Mathematics","Building Technology","Civil Engineering","Computer Science","Computer Networking",
"Electrical/Electronic Engineering","Hospitality","Liberal Studies","Marketing","Purchasing & Supply","Secretaryship");

$sessions = array("-","Morning","Evening","Weekend");

$faculties = array("-","AGR","ARTS","COM","CIE","EDU","ENG","LAW","MED","SCI","SOC","VET");

$levels = array("-","100","200","300");

$registration_row = null;

if(isset($_POST["btn_submit"])){
  if($_POST["student_programme"]!="" && $_POST["student_level"]!="" && $_POST["student_session"]!=""){

    $other_name = $_POST["student_other_name"];
    $student_programme_selected = $_POST["student_programme"];
    $student_level_selected = $_POST["student_level"];
    $student_session_selected = $_POST["student_session"];
    $student_index = $_POST["txt_index_number"];
	$student_faculty = $_POST["student_faculty"];

    $check_user_existence = "SELECT * FROM industrial_registration WHERE index_number='$student_index' LIMIT 1";
    $execute_check_existence = mysqli_query($conn,$check_user_existence);
    $check_user = mysqli_num_rows($execute_check_existence);
    if($check_user==1){
      echo "<script>alert('You have already registered.')</script>";
      $registration_row = mysqli_fetch_assoc($execute_check_existence);
    }else{
      $insert_details_command = "INSERT INTO `industrial_registration` (`id`, `first_name`, `last_name`,`other_name`,`level`, `programme`, `session`,`faculty`,`date`, `index_number`) VALUES (NULL, '$student_fname', '$student_lname','$other_name', '$student_level_selected', '$student_programme_selected', '$student_session_selected','$student_faculty', CURRENT_TIMESTAMP, '$student_index')";
      if($run_insert_query = mysqli_query($conn,$insert_details_command)){
        echo "<script>alert('Details Have Been Sent Successfully')</script>";
        $select_inserted = "SELECT * FROM industrial_registration WHERE index_number='$student_index' LIMIT 1";
        $result_inserted = mysqli_query($conn,$select_inserted);
        if ($result_inserted && mysqli_num_rows($result_inserted) == 1) {
          $registration_row = mysqli_fetch_assoc($result_inserted);
        }
      }else{
        echo "<script>alert('Details Not Submitted')</script>";
      }
    }
  }
}

// Load existing registration on initial page load (or after refresh)
if ($registration_row === null && !empty($index_number_holder)) {
  $existing_q = "SELECT * FROM industrial_registration WHERE index_number='$index_number_holder' LIMIT 1";
  $existing_res = mysqli_query($conn, $existing_q);
  if ($existing_res && mysqli_num_rows($existing_res) == 1) {
    $registration_row = mysqli_fetch_assoc($existing_res);
  }
}

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
  <link rel="stylesheet" href="vira_and_industrial_registration.css"/>

  <script type="text/javascript" src="../js/jquery-3.1.1.min.js"/></script>
  <script type="text/javascript" src="../js/bootstrap.min.js"/></script>

</head>
<body>

<?php $topbar_logo_src = '../img/header_log.png'; include '../includes/student_topbar.php'; ?>

<div id="left_side_bar">
<ul id="menu_list">
  <a class="menu_items_link" href="../instructions_page/instructions_page.php"><li class="menu_items_list">Instructions</li></a>
  <a class="menu_items_link" href="../Register_page/Register_page.php"><li class="menu_items_list"style="background-color:orange;padding-left:16px">Register</li></a>
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
    <div class = "panel">
       <div class = "panel-heading industrial_phead">
          <h2 class = "panel-title industrial_ptitle"> Industrial Registration</h2>
       </div>

            <div class = "panel-body industrial_pbody">

         <div class="panel panel-adjusted">
           <div class="panel-body">
             <form method="post" action="">
               <div class="form-group">
                <label for="txtfname">First Name </label>
                <input type="text" class="form-control form-control-adjusted" id="txtfname" placeholder="Enter first name" disabled <?php echo "value='$student_fname'"?>>
              </div>

              <div class="form-group">
               <label for="txtlname">Last Name </label>
               <input type="text" class="form-control form-control-adjusted" id="txtlname" placeholder="Enter last name" disabled value=<?php echo $student_lname;?>>
             </div>

             <div class="form-group">
              <label for="txtothername">Other Name(s) </label>
              <input
                type="text"
                class="form-control form-control-adjusted"
                id="txtothername"
                placeholder="Enter other name(s)"
                name="student_other_name"
                value="<?php
                  if ($registration_row) {
                    echo htmlspecialchars($registration_row['other_name']);
                  } elseif (isset($_POST['student_other_name'])) {
                    echo htmlspecialchars($_POST['student_other_name']);
                  }
                ?>"
              >
            </div>

            <div class="form-group">
             <label for="txtindexnumber">Index Number </label>
             <input
               type="text"
               class="form-control form-control-adjusted"
               id="txtindexnumber"
               placeholder="Enter index number"
               name="txt_index_number"
               value="<?php
                 if ($registration_row) {
                   echo htmlspecialchars($registration_row['index_number']);
                 } else {
                   echo htmlspecialchars($index_number_holder);
                 }
               ?>"
             >
           </div>

            <div class="form-group">
            <label for="selected_programme">Select Programme:</label>
            <select class="form-control form-control-adjusted" id="selected_programme" name="student_programme">
              <?php
                $current_prog = null;
                if ($registration_row) {
                  $current_prog = $registration_row['programme'];
                } elseif (isset($_POST['student_programme'])) {
                  $current_prog = $_POST['student_programme'];
                }
                foreach($programmes as $val) {
                  $selected = ($current_prog === $val) ? ' selected' : '';
                  echo "<option{$selected}>".$val."</option>";
                }
               ?>
            </select>
          </div>

          <div class="form-group">
          <label for="selected_level">Select Level:</label>
          <select class="form-control form-control-adjusted" id="selected_level" name="student_level">
            <?php
              $current_level = null;
              if ($registration_row) {
                $current_level = $registration_row['level'];
              } elseif (isset($_POST['student_level'])) {
                $current_level = $_POST['student_level'];
              }
              foreach($levels as $val) {
                $selected = ($current_level === $val) ? ' selected' : '';
                echo "<option{$selected}>".$val."</option>";
              }
             ?>
          </select>
        </div>

          <div class="form-group">
          <label for="selected_session">Select Session:</label>
          <select class="form-control form-control-adjusted" id="selected_session" name="student_session">
            <?php
              $current_session = null;
              if ($registration_row) {
                $current_session = $registration_row['session'];
              } elseif (isset($_POST['student_session'])) {
                $current_session = $_POST['student_session'];
              }
              foreach($sessions as $val) {
                $selected = ($current_session === $val) ? ' selected' : '';
                echo "<option{$selected}>".$val."</option>";
              }
             ?>
          </select>
        </div>
          
      <div class="form-group">
      <label for="selected_session">Select Faculty :</label>
      <select class="form-control form-control-adjusted" id="selected_faculty" name="student_faculty">
        <?php
          $current_faculty = null;
          if ($registration_row) {
            $current_faculty = $registration_row['faculty'];
          } elseif (isset($_POST['student_faculty'])) {
            $current_faculty = $_POST['student_faculty'];
          }
          foreach($faculties as $val) {
            $selected = ($current_faculty === $val) ? ' selected' : '';
            echo "<option{$selected}>".$val."</option>";
          }
         ?>
      </select>
    </div>

          <div id="btn_submit_holder">
          <input type="submit" class="btn btn-primary" value="Submit"  name="btn_submit"/>
        </div>
           </form>

     </div>
     </panel>
       </div>
     </div>
   </div>
 </div>

</body>
</html>
