<?php

  include '../database_connection/database_connection.php';

  $student_fname = $_COOKIE["student_first_name"];
  $student_lname = $_COOKIE["student_last_name"];
  $student_index = $_COOKIE["student_index_number"];

  $assumption_row = null;

  // Zimbabwe's 10 provinces (must match admin Assign Supervisors / Students Statistics)
  $regions = array("Bulawayo","Harare","Manicaland","Mashonaland Central","Mashonaland East",
    "Mashonaland West","Masvingo","Matabeleland North","Matabeleland South","Midlands");

  $programmes = array("-","Accountancy","Applied Mathematics","Building Technology","Civil Engineering","Computer Science","Computer Networking",
  "Electrical/Electronic Engineering","Hospitality","Liberal Studies","Marketing","Purchasing & Supply","Secretaryship");


  $checking_user_industrial = "SELECT * FROM industrial_registration WHERE index_number='$student_index' AND first_name='$student_fname' AND last_name='$student_lname'";
  $checking_user_query = mysqli_query($conn,$checking_user_industrial);
  $check_existence = mysqli_num_rows($checking_user_query);
  if($check_existence==1){

    $get_user_info = mysqli_fetch_assoc($checking_user_query);

    $student_session = $get_user_info["session"];
    $student_level = $get_user_info["level"];
    $student_programme = $get_user_info["programme"];
    $student_other_name = $get_user_info["other_name"];

    setcookie("student_programme_holder",$student_programme,time() + (86400 * 30));
    setcookie("student_session_holder",$student_session,time() + (86400 * 30));
    setcookie("student_level_holder",$student_level,time() + (86400 * 30));
    setcookie("student_other_name_holder",$student_other_name,time() + (86400 * 30));
    setcookie("student_registration_type","industrial registration",time() + (86400 * 30));
    $student_registration_type = "INDUSTRIAL REGISTRATION";
    $status_number = 2;

  }else{
    header("Location:../Register_Page/register_page.php");
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
  <link rel="stylesheet" href="student_assumption.css"/>

  <script type="text/javascript" src="../js/jquery-3.1.1.min.js"/></script>
  <script type="text/javascript" src="../js/bootstrap.min.js"/></script>

</head>
<body>

<?php $topbar_logo_src = '../img/header_log.png'; include '../includes/student_topbar.php'; ?>

<div id="left_side_bar">
<ul id="menu_list">
  <a class="menu_items_link" href="../instructions_page/instructions_page.php"><li class="menu_items_list">Instructions</li></a>
  <a class="menu_items_link" href="../Register_page/Register_page.php"><li class="menu_items_list">Register</li></a>
  <a class="menu_items_link" href="student_assumption.php"><li class="menu_items_list">Submit Assupmtion</li></a>
  <a class="menu_items_link" href="../e-logbook/elogbook_dynamic.php"><li class="menu_items_list">E-Logbook</li></a>
  <a class="menu_items_link" href="../orientation_checklist.php"><li class="menu_items_list">Orientation Checklist</li></a>
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
       <div class = "panel-heading phead">
          <h2 class = "panel-title ptitle"> ASSUMPTION OF DUTY FORM</h2>
       </div>
            <div class = "panel-body pbody">


          <div class="panel panel-adjusted">
           <div class="panel-body pbody_student_info">
             <br>
             <div style="float:left;font-size:.9em"><strong>Student Information</strong></div>
             <hr>
         <form method="post" action="">
           <div class="form-group">
            <label for="txtfname">First Name </label>
            <input type="text" class="form-control form-control-adjusted" id="txtfname" placeholder="Enter first name" disabled value=<?php echo $student_fname;?>>
          </div>

          <div class="form-group">
           <label for="txtlname">Last Name </label>
           <input type="text" class="form-control form-control-adjusted" id="txtlname" placeholder="Enter last name" disabled value=<?php echo $student_lname;?>>
         </div>

         <div class="form-group">
          <label for="txtothername">Other Name(s) </label>
          <input type="text" class="form-control form-control-adjusted" id="txtothername" placeholder="Enter other name(s)" disabled value=<?php echo $student_other_name;?>>
        </div>

        <div class="form-group">
         <label for="txtprogramme">Programme </label>
         <input type="text" class="form-control form-control-adjusted" id="txtprogramme" placeholder="Enter programme" disabled value="<?php echo $student_programme;?>">
       </div>


       <div class="form-group">
        <label for="txtindexnumber">Index Number </label>
        <input type="text" class="form-control form-control-adjusted" id="txtindexnumber" placeholder="Enter index number"  name="txt_index_number" value=<?php echo $student_index;?>>
      </div>

       <div class="form-group">
        <label for="txtsession">Session </label>
        <input type="text" class="form-control form-control-adjusted" id="txtsession" placeholder="Enter your session" disabled value=<?php echo $student_session;?>>
      </div>

      <div class="form-group">
       <label for="txtlevel">Level </label>
       <input type="text" class="form-control form-control-adjusted" id="txtlevel" placeholder="Enter your level" disabled value=<?php echo $student_level;?>>
     </div>

     <br>
     <div style="float:left;font-size:.9em"><strong>Company Information</strong></div>
     <hr>

   <div class="form-group">
   <label for="txtcompanyname">Company Name : </label>
   <input
     type="text"
     class="form-control form-control-adjusted"
     id="txtcompanyname"
     placeholder="Enter company name"
     name="txt_company_name"
     value="<?php
       if ($assumption_row) {
         echo htmlspecialchars($assumption_row['company_name']);
       } elseif (isset($_POST['txt_company_name'])) {
         echo htmlspecialchars($_POST['txt_company_name']);
       }
     ?>"
   >
 </div>

    <div class="form-group">
     <label for="txtsupervisorsname">Supervisors Name : </label>
     <input
       type="text"
       class="form-control form-control-adjusted"
       id="txtsupervisorsname"
       placeholder="Enter supervisors name"
       name="txt_supervisors_name"
       value="<?php
         if ($assumption_row) {
           echo htmlspecialchars($assumption_row['supervisor_name']);
         } elseif (isset($_POST['txt_supervisors_name'])) {
           echo htmlspecialchars($_POST['txt_supervisors_name']);
         }
       ?>"
     >
   </div>

   <div class="form-group">
   <label for="txtsupervisorscontact">Supervisors Contact : </label>
   <input
     type="text"
     maxlength="15"
     class="form-control form-control-adjusted"
     id="txtsupervisorscontact"
     placeholder="Enter supervisors contact"
     name="txt_supervisors_contact"
     value="<?php
       if ($assumption_row) {
         echo htmlspecialchars($assumption_row['supervisor_contact']);
       } elseif (isset($_POST['txt_supervisors_contact'])) {
         echo htmlspecialchars($_POST['txt_supervisors_contact']);
       }
     ?>"
   >
  </div>

  <div class="form-group">
  <label for="txtsupervisorsemail">Supervisors Email : </label>
  <input
    type="email"
    class="form-control form-control-adjusted"
    id="txtsupervisorsemail"
    placeholder="Enter supervisors e-mail"
    name="txt_supervisors_email"
    value="<?php
      if ($assumption_row) {
        echo htmlspecialchars($assumption_row['supervisor_email']);
      } elseif (isset($_POST['txt_supervisors_email'])) {
        echo htmlspecialchars($_POST['txt_supervisors_email']);
      }
    ?>"
  >
 </div>

    <div class="form-group">
    <label for="selected_region">Select company province :</label>
    <select class="form-control form-control-adjusted" id="selected_region" name="selected_region">
      <option value="">-- Select Province --</option>
      <?php
        $current_region = null;
        if ($assumption_row) {
          $current_region = $assumption_row['company_region'];
        } elseif (isset($_POST['selected_region'])) {
          $current_region = $_POST['selected_region'];
        }
        foreach($regions as $val) {
          $selected = ($current_region === $val) ? ' selected' : '';
          echo '<option'.$selected.'>'.htmlspecialchars($val).'</option>';
        }
      ?>
    </select>
  </div>

  <div class="form-group">
 <label for="company_address">Address :</label>
 <textarea class="form-control" id="company_address" width="100%" name="txt_address"><?php
   if ($assumption_row) {
     echo htmlspecialchars($assumption_row['company_address']);
   } elseif (isset($_POST['txt_address'])) {
     echo htmlspecialchars($_POST['txt_address']);
   }
 ?></textarea>
 </div>

  <div id="btn_submit_holder">
  <input type="submit" class="btn btn-primary" value="Submit" name="btn_submit"/>
  </div>
       </form>
     </div>
     </panel>
       </div>
     </div>
   </div>
 </div>

 <?php

 if(isset($_POST["btn_submit"])){

  if($_POST["txt_company_name"]!="" && $_POST["txt_supervisors_name"]!="" && $_POST["txt_supervisors_contact"]!="" && $_POST["txt_supervisors_email"]!="" && $_POST["selected_region"]!="" && $_POST["txt_address"]!=""){

      $student_company_name = $_POST["txt_company_name"] ?? '';
      $student_index_number = $_POST["txt_index_number"] ?? '';
      $student_supervisor_name = $_POST["txt_supervisors_name"] ?? '';
      $student_supervisor_contact = $_POST["txt_supervisors_contact"] ?? '';
      $student_supervisor_email = $_POST["txt_supervisors_email"] ?? '';
      $student_company_region = $_POST["selected_region"] ?? '';
      $student_company_address = $_POST["txt_address"] ?? '';
      $student_company_name = mysqli_real_escape_string($conn, $student_company_name);
      $student_index_number = mysqli_real_escape_string($conn, $student_index_number);
      $student_supervisor_name = mysqli_real_escape_string($conn, $student_supervisor_name);
      $student_supervisor_contact = mysqli_real_escape_string($conn, $student_supervisor_contact);
      $student_supervisor_email = mysqli_real_escape_string($conn, $student_supervisor_email);
      $student_company_region = mysqli_real_escape_string($conn, $student_company_region);
      $student_company_address = mysqli_real_escape_string($conn, $student_company_address);

      $avoid_duplicate = "SELECT * FROM students_assumption WHERE index_number='$student_index_number' LIMIT 1";
      $execute_avoid_duplicate_query = mysqli_query($conn,$avoid_duplicate);
      $check_avoidance_query = mysqli_num_rows($execute_avoid_duplicate_query);

      if($check_avoidance_query==1){
        echo "<script>alert('You have submitted details already')</script>";
        $assumption_row = mysqli_fetch_assoc($execute_avoid_duplicate_query);
      }else{
        $my_insert_query = "INSERT INTO `students_assumption` (`first_name`, `last_name`, `other_name`,`index_number`, `level`, `programme`, `session`,`company_name`, `supervisor_name`, `supervisor_contact`,`supervisor_email`, `company_region`, `company_address`,`registration_type`, `date`) VALUES ('$student_fname',
         '$student_lname', '$student_other_name', '$student_index_number',
        '$student_level', '$student_programme', '$student_session', '$student_company_name', '$student_supervisor_name','$student_supervisor_contact', '$student_supervisor_email','$student_company_region', '$student_company_address','$student_registration_type', CURRENT_TIMESTAMP)";

        if($run_query = mysqli_query($conn,$my_insert_query)){
          echo "<script>alert('Details Have Been Submitted Successfully')</script>";

          $select_assumption = "SELECT * FROM students_assumption WHERE index_number='$student_index_number' LIMIT 1";
          $res_assump = mysqli_query($conn,$select_assumption);
          if ($res_assump && mysqli_num_rows($res_assump) == 1) {
            $assumption_row = mysqli_fetch_assoc($res_assump);
          }

            $my_update_query = "UPDATE `industrial_registration` SET `company_supervisor_name` = '$student_supervisor_name' WHERE index_number = '$student_index_number'";
            $execute_my_update_query = mysqli_query($conn,$my_update_query);

            $my_update_query2 = "UPDATE `industrial_registration` SET `company_supervisor_contact` = '$student_supervisor_contact' WHERE index_number = '$student_index_number'";
            $execute_my_update_query = mysqli_query($conn,$my_update_query2);

            $my_update_query3 = "UPDATE `industrial_registration` SET `attachment_region` = '$student_company_region' WHERE index_number = '$student_index_number'";
            $execute_my_update_query = mysqli_query($conn,$my_update_query3);
      }else{
          echo "<script>alert('Details Not Submitted ')</script>";
        }

    }

   }
 }

 // On initial load (or after refresh without POST), fetch any existing assumption data
 if ($assumption_row === null && !empty($student_index)) {
   $existing_assump_q = "SELECT * FROM students_assumption WHERE index_number='$student_index' LIMIT 1";
   $existing_assump_res = mysqli_query($conn, $existing_assump_q);
   if ($existing_assump_res && mysqli_num_rows($existing_assump_res) == 1) {
     $assumption_row = mysqli_fetch_assoc($existing_assump_res);
   }
 }
  ?>

</body>
</html>
