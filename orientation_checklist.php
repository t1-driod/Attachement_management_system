<?php

include 'database_connection/database_connection.php';

// Ensure orientation_checklist table has all expected columns (backwards compatible)
$required_columns = [
    'host_institution' => "ALTER TABLE orientation_checklist ADD COLUMN host_institution VARCHAR(255) DEFAULT NULL AFTER index_number",
    'general_staff_introduction' => "ALTER TABLE orientation_checklist ADD COLUMN general_staff_introduction TINYINT(1) NOT NULL DEFAULT 0",
    'general_facilities_location' => "ALTER TABLE orientation_checklist ADD COLUMN general_facilities_location TINYINT(1) NOT NULL DEFAULT 0",
    'general_tea_coffee_lunch' => "ALTER TABLE orientation_checklist ADD COLUMN general_tea_coffee_lunch TINYINT(1) NOT NULL DEFAULT 0",
    'general_transport_arrangements' => "ALTER TABLE orientation_checklist ADD COLUMN general_transport_arrangements TINYINT(1) NOT NULL DEFAULT 0",
    'general_dress_code' => "ALTER TABLE orientation_checklist ADD COLUMN general_dress_code TINYINT(1) NOT NULL DEFAULT 0",
    'general_code_of_conduct' => "ALTER TABLE orientation_checklist ADD COLUMN general_code_of_conduct TINYINT(1) NOT NULL DEFAULT 0",
    'general_policies_regulations' => "ALTER TABLE orientation_checklist ADD COLUMN general_policies_regulations TINYINT(1) NOT NULL DEFAULT 0",
    'work_workspace' => "ALTER TABLE orientation_checklist ADD COLUMN work_workspace TINYINT(1) NOT NULL DEFAULT 0",
    'work_duty_arrangements' => "ALTER TABLE orientation_checklist ADD COLUMN work_duty_arrangements TINYINT(1) NOT NULL DEFAULT 0",
    'work_schedule_meetings' => "ALTER TABLE orientation_checklist ADD COLUMN work_schedule_meetings TINYINT(1) NOT NULL DEFAULT 0",
    'work_first_meeting_supervisor' => "ALTER TABLE orientation_checklist ADD COLUMN work_first_meeting_supervisor TINYINT(1) NOT NULL DEFAULT 0",
    'health_emergency_procedures' => "ALTER TABLE orientation_checklist ADD COLUMN health_emergency_procedures TINYINT(1) NOT NULL DEFAULT 0",
    'health_safety_policy' => "ALTER TABLE orientation_checklist ADD COLUMN health_safety_policy TINYINT(1) NOT NULL DEFAULT 0",
    'health_first_aid_arrangements' => "ALTER TABLE orientation_checklist ADD COLUMN health_first_aid_arrangements TINYINT(1) NOT NULL DEFAULT 0",
    'health_fire_procedures' => "ALTER TABLE orientation_checklist ADD COLUMN health_fire_procedures TINYINT(1) NOT NULL DEFAULT 0",
    'health_accident_reporting' => "ALTER TABLE orientation_checklist ADD COLUMN health_accident_reporting TINYINT(1) NOT NULL DEFAULT 0",
    'health_manual_handling' => "ALTER TABLE orientation_checklist ADD COLUMN health_manual_handling TINYINT(1) NOT NULL DEFAULT 0",
    'health_safety_regulations' => "ALTER TABLE orientation_checklist ADD COLUMN health_safety_regulations TINYINT(1) NOT NULL DEFAULT 0",
    'health_equipment_instruction' => "ALTER TABLE orientation_checklist ADD COLUMN health_equipment_instruction TINYINT(1) NOT NULL DEFAULT 0",
    'others_student_info_form' => "ALTER TABLE orientation_checklist ADD COLUMN others_student_info_form TINYINT(1) NOT NULL DEFAULT 0",
    'others_social_media_guidelines' => "ALTER TABLE orientation_checklist ADD COLUMN others_social_media_guidelines TINYINT(1) NOT NULL DEFAULT 0",
    'others_it_systems_equipment' => "ALTER TABLE orientation_checklist ADD COLUMN others_it_systems_equipment TINYINT(1) NOT NULL DEFAULT 0",
    'student_signature' => "ALTER TABLE orientation_checklist ADD COLUMN student_signature VARCHAR(255) DEFAULT NULL",
    'student_signature_date' => "ALTER TABLE orientation_checklist ADD COLUMN student_signature_date DATE DEFAULT NULL",
    'host_supervisor_signature' => "ALTER TABLE orientation_checklist ADD COLUMN host_supervisor_signature VARCHAR(255) DEFAULT NULL",
    'host_supervisor_date' => "ALTER TABLE orientation_checklist ADD COLUMN host_supervisor_date DATE DEFAULT NULL",
    'wrl_coordinator_signature' => "ALTER TABLE orientation_checklist ADD COLUMN wrl_coordinator_signature VARCHAR(255) DEFAULT NULL",
    'wrl_coordinator_date' => "ALTER TABLE orientation_checklist ADD COLUMN wrl_coordinator_date DATE DEFAULT NULL",
    'completed_at' => "ALTER TABLE orientation_checklist ADD COLUMN completed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
    'last_updated' => "ALTER TABLE orientation_checklist ADD COLUMN last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
];

foreach ($required_columns as $column => $alterSql) {
    $checkCol = @mysqli_query($conn, "SHOW COLUMNS FROM orientation_checklist LIKE '$column'");
    if ($checkCol && mysqli_num_rows($checkCol) == 0) {
        @mysqli_query($conn, $alterSql);
    }
}

$student_fname = $_COOKIE["student_first_name"];
$student_lname = $_COOKIE["student_last_name"];
$student_full_name = $student_fname." ".$student_lname;
$student_index_number = $_COOKIE["student_index_number"];

$message = "";
$status = "";

// Check if student has already completed the checklist (for reference)
$check_checklist_query = "SELECT * FROM orientation_checklist WHERE index_number='$student_index_number'";
$checklist_result = mysqli_query($conn, $check_checklist_query);
$has_completed = mysqli_num_rows($checklist_result) > 0;

if(isset($_POST["submit_checklist"])){
        // Get host institution
        $host_institution = mysqli_real_escape_string($conn, $_POST["host_institution"] ?? "");
        
        // Get all checkbox values (1 if checked, 0 if not)
        $general_staff_introduction = isset($_POST["general_staff_introduction"]) ? 1 : 0;
        $general_facilities_location = isset($_POST["general_facilities_location"]) ? 1 : 0;
        $general_tea_coffee_lunch = isset($_POST["general_tea_coffee_lunch"]) ? 1 : 0;
        $general_transport_arrangements = isset($_POST["general_transport_arrangements"]) ? 1 : 0;
        $general_dress_code = isset($_POST["general_dress_code"]) ? 1 : 0;
        $general_code_of_conduct = isset($_POST["general_code_of_conduct"]) ? 1 : 0;
        $general_policies_regulations = isset($_POST["general_policies_regulations"]) ? 1 : 0;
        $work_workspace = isset($_POST["work_workspace"]) ? 1 : 0;
        $work_duty_arrangements = isset($_POST["work_duty_arrangements"]) ? 1 : 0;
        $work_schedule_meetings = isset($_POST["work_schedule_meetings"]) ? 1 : 0;
        $work_first_meeting_supervisor = isset($_POST["work_first_meeting_supervisor"]) ? 1 : 0;
        $health_emergency_procedures = isset($_POST["health_emergency_procedures"]) ? 1 : 0;
        $health_safety_policy = isset($_POST["health_safety_policy"]) ? 1 : 0;
        $health_first_aid_arrangements = isset($_POST["health_first_aid_arrangements"]) ? 1 : 0;
        $health_fire_procedures = isset($_POST["health_fire_procedures"]) ? 1 : 0;
        $health_accident_reporting = isset($_POST["health_accident_reporting"]) ? 1 : 0;
        $health_manual_handling = isset($_POST["health_manual_handling"]) ? 1 : 0;
        $health_safety_regulations = isset($_POST["health_safety_regulations"]) ? 1 : 0;
        $health_equipment_instruction = isset($_POST["health_equipment_instruction"]) ? 1 : 0;
        $others_student_info_form = isset($_POST["others_student_info_form"]) ? 1 : 0;
        $others_social_media_guidelines = isset($_POST["others_social_media_guidelines"]) ? 1 : 0;
        $others_it_systems_equipment = isset($_POST["others_it_systems_equipment"]) ? 1 : 0;
        
        // Get signature information
        $student_signature = mysqli_real_escape_string($conn, $_POST["student_signature"] ?? "");
        $student_signature_date = mysqli_real_escape_string($conn, $_POST["student_signature_date"] ?? "");
        $host_supervisor_signature = mysqli_real_escape_string($conn, $_POST["host_supervisor_signature"] ?? "");
        $host_supervisor_date = mysqli_real_escape_string($conn, $_POST["host_supervisor_date"] ?? "");
        $wrl_coordinator_signature = mysqli_real_escape_string($conn, $_POST["wrl_coordinator_signature"] ?? "");
        $wrl_coordinator_date = mysqli_real_escape_string($conn, $_POST["wrl_coordinator_date"] ?? "");

        // Insert checklist data
        $insert_query = "INSERT INTO orientation_checklist
            (student_name, index_number, host_institution, general_staff_introduction, general_facilities_location,
             general_tea_coffee_lunch, general_transport_arrangements, general_dress_code, general_code_of_conduct,
             general_policies_regulations, work_workspace, work_duty_arrangements, work_schedule_meetings,
             work_first_meeting_supervisor, health_emergency_procedures, health_safety_policy,
             health_first_aid_arrangements, health_fire_procedures, health_accident_reporting,
             health_manual_handling, health_safety_regulations, health_equipment_instruction,
             others_student_info_form, others_social_media_guidelines, others_it_systems_equipment,
             student_signature, student_signature_date, host_supervisor_signature, host_supervisor_date,
             wrl_coordinator_signature, wrl_coordinator_date)
            VALUES
            ('$student_full_name', '$student_index_number', '$host_institution', '$general_staff_introduction',
             '$general_facilities_location', '$general_tea_coffee_lunch', '$general_transport_arrangements',
             '$general_dress_code', '$general_code_of_conduct', '$general_policies_regulations', '$work_workspace',
             '$work_duty_arrangements', '$work_schedule_meetings', '$work_first_meeting_supervisor',
             '$health_emergency_procedures', '$health_safety_policy', '$health_first_aid_arrangements',
             '$health_fire_procedures', '$health_accident_reporting', '$health_manual_handling',
             '$health_safety_regulations', '$health_equipment_instruction', '$others_student_info_form',
             '$others_social_media_guidelines', '$others_it_systems_equipment', '$student_signature',
             " . ($student_signature_date ? "'$student_signature_date'" : "NULL") . ",
             '$host_supervisor_signature', " . ($host_supervisor_date ? "'$host_supervisor_date'" : "NULL") . ",
             '$wrl_coordinator_signature', " . ($wrl_coordinator_date ? "'$wrl_coordinator_date'" : "NULL") . ")";

        if(mysqli_query($conn, $insert_query)){
            $message = "Orientation checklist submitted successfully!";
            $status = "success";
            $has_completed = true;
        } else {
            $message = "Error saving checklist. Please try again.";
            $status = "error";
        }
}

// Get checklist data if already completed
$checklist_data = [];
if($has_completed){
    $checklist_result = mysqli_query($conn, $check_checklist_query);
    $checklist_data = mysqli_fetch_assoc($checklist_result);
}

// Ensure expected keys exist to prevent undefined array key warnings
$defaults = [
    'student_name' => '',
    'index_number' => '',
    'host_institution' => '',

    // checklist booleans
    'general_staff_introduction' => 0,
    'general_facilities_location' => 0,
    'general_tea_coffee_lunch' => 0,
    'general_transport_arrangements' => 0,
    'general_dress_code' => 0,
    'general_code_of_conduct' => 0,
    'general_policies_regulations' => 0,
    'work_workspace' => 0,
    'work_duty_arrangements' => 0,
    'work_schedule_meetings' => 0,
    'work_first_meeting_supervisor' => 0,
    'health_emergency_procedures' => 0,
    'health_safety_policy' => 0,
    'health_first_aid_arrangements' => 0,
    'health_fire_procedures' => 0,
    'health_accident_reporting' => 0,
    'health_manual_handling' => 0,
    'health_safety_regulations' => 0,
    'health_equipment_instruction' => 0,
    'others_student_info_form' => 0,
    'others_social_media_guidelines' => 0,
    'others_it_systems_equipment' => 0,

    // signatures and dates
    'student_signature' => '',
    'student_signature_date' => null,
    'host_supervisor_signature' => '',
    'host_supervisor_date' => null,
    'wrl_coordinator_signature' => '',
    'wrl_coordinator_date' => null,

    'completed_at' => null
];

$checklist_data = array_merge($defaults, $checklist_data);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IASMS - Orientation Checklist</title>

  <link rel="stylesheet" href="css/bootstrap-theme.min.css"/>
  <link rel="stylesheet" href="css/bootstrap.min.css"/>
  <link rel="stylesheet" href="css/bootstrap-select.css"/>
  <link rel="stylesheet" href="css/main_page_style.css"/>

  <script type="text/javascript" src="js/jquery-3.1.1.min.js"/></script>
  <script type="text/javascript" src="js/bootstrap.min.js"/></script>

  <style>
    .checklist-document {
      max-width: 900px;
      margin: 20px auto;
      padding: 40px;
      background-color: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .document-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .university-logo {
      width: 120px;
      height: 120px;
      margin: 0 auto 15px;
      display: block;
    }
    .university-name {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 20px;
      color: #000;
    }
    .document-title {
      font-size: 20px;
      font-weight: bold;
      color: #0066cc;
      text-align: center;
      margin-bottom: 30px;
    }
    .student-info {
      margin-bottom: 20px;
      font-size: 14px;
    }
    .info-line {
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
    }
    .info-label {
      font-weight: normal;
    }
    .info-field {
      border-bottom: 1px dotted #000;
      flex: 1;
      margin: 0 10px;
      min-height: 20px;
    }
    .instructions {
      font-size: 12px;
      font-style: italic;
      margin-bottom: 25px;
      color: #555;
      line-height: 1.6;
    }
    .checklist-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    .checklist-table th {
      background-color: #f0f0f0;
      border: 2px solid #000;
      padding: 10px;
      text-align: center;
      font-weight: bold;
    }
    .checklist-table td {
      border: 1px solid #000;
      padding: 10px;
      vertical-align: top;
    }
    .checklist-table td:first-child {
      width: 75%;
    }
    .checklist-table td:last-child {
      width: 25%;
      text-align: center;
    }
    .section-header {
      background-color: #e9ecef;
      font-weight: bold;
      font-style: italic;
      text-align: center;
      padding: 8px;
    }
    .checklist-item {
      padding: 5px;
    }
    .tick-box {
      width: 18px;
      height: 18px;
      border: 2px solid #000;
      display: inline-block;
      cursor: pointer;
      position: relative;
      vertical-align: middle;
      margin-left: 4px;
    }
    .tick-box.checked {
      background-color: #000;
    }
    .tick-box.checked:after {
      content: '✓';
      color: white;
      position: absolute;
      top: -4px;
      left: 2px;
      font-size: 16px;
      font-weight: bold;
      line-height: 18px;
    }
    /* Show the real checkbox so clicking is always reliable */
    input[type="checkbox"] {
      display: inline-block;
      width: 18px;
      height: 18px;
      margin: 0;
      vertical-align: middle;
    }
    .submit-section {
      text-align: center;
      margin-top: 30px;
    }
    .host-institution-input {
      border: none;
      border-bottom: 1px dotted #000;
      background: transparent;
      padding: 2px 5px;
      width: 300px;
      font-size: 14px;
    }
  </style>
</head>
<body>

<?php $topbar_logo_src = 'img/header_log.png'; include 'includes/student_topbar.php'; ?>

<div id="left_side_bar">
<ul id="menu_list">
  <a class="menu_items_link" href="instructions_page/instructions_page.php"><li class="menu_items_list">Instructions</li></a>
  <a class="menu_items_link" href="Register_page/Register_page.php"><li class="menu_items_list">Register</li></a>
  <a class="menu_items_link" href="student_assumption/student_assumption.php"><li class="menu_items_list">Submit Assupmtion</li></a>
  <a class="menu_items_link" href="e-logbook/elogbook_dynamic.php"><li class="menu_items_list">E-Logbook</li></a>
  <a class="menu_items_link" href="orientation_checklist.php"><li class="menu_items_list" style="background-color:orange;padding-left:16px">Orientation Checklist</li></a>
  <a class="menu_items_link" href="submit_contract.php"><li class="menu_items_list">Submit Contract</li></a>
  <a class="menu_items_link" href="company_supervisor/company_supervisor_login.php"><li class="menu_items_list">Company Supervisor</li></a>
  <a class="menu_items_link" href="visiting_supervisor/visiting_supervisor_login.php"><li class="menu_items_list">Visiting Supervisor</li></a>
  <a class="menu_items_link" href="submit_report/submit_report.php"><li class="menu_items_list">Submit Report</li></a>
  <a class="menu_items_link" href="index.php"><li class="menu_items_list">Logout</li></a>
</ul>
</div>

<div id="main_content">
  <div class="container-fluid">
    <div class="panel">
      <div class="panel-heading phead">
         <h2 class="panel-title ptitle"> Work Related Learning Placement - Student Orientation Checklist</h2>
      </div>
      <div class="panel-body pbody">

        <?php if($message != ""): ?>
        <div class="alert alert-<?php echo ($status == 'success') ? 'success' : (($status == 'warning') ? 'warning' : 'danger'); ?> alert-dismissible fade in">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <div class="checklist-document">
          <?php if($has_completed): ?>
            <!-- Document Header -->
            <div class="document-header">
              <img src="img/header_log.png" alt="University Logo" class="university-logo">
              <div class="university-name">UNIVERSITY OF ZIMBABWE</div>
              <div class="document-title">Work Related Learning Placement Student Orientation Checklist</div>
            </div>

            <!-- Student Information -->
            <div class="student-info">
              <div class="info-line">
                <span class="info-label">Name of student:</span>
                <span class="info-field"><?php echo htmlspecialchars($checklist_data['student_name']); ?></span>
                <span class="info-label">Reg. Number:</span>
                <span class="info-field"><?php echo htmlspecialchars($checklist_data['index_number']); ?></span>
              </div>
              <div class="info-line">
                <span class="info-label">Host Institution:</span>
                <span class="info-field"><?php echo htmlspecialchars($checklist_data['host_institution'] ?? ''); ?></span>
              </div>
            </div>

            <!-- Instructions -->
            <div class="instructions">
              (Please date the items below when they occur and inform the WRL Coordinator of any items not covered within one week of the start of the attachment period. *Complete where applicable according to Faculty expectations).
            </div>

            <!-- Completed Checklist Display -->
            <table class="checklist-table">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Tick done</th>
                </tr>
              </thead>
              <tbody>
                <!-- General Section -->
                <tr>
                  <td colspan="2" class="section-header"><strong><em>General</em></strong></td>
                </tr>
                <tr>
                  <td class="checklist-item">Introduction to key staff members and their roles explained</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['general_staff_introduction']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Location of facilities such as rest rooms, canteen, etc.</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['general_facilities_location']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Tea/coffee and lunch arrangements</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['general_tea_coffee_lunch']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Transport arrangements (if applicable)</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['general_transport_arrangements']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Dress code</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['general_dress_code']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Code of conduct</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['general_code_of_conduct']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Policies and regulations</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['general_policies_regulations']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>

                <!-- Work-related Section -->
                <tr>
                  <td colspan="2" class="section-header"><strong><em>Work-related</em></strong></td>
                </tr>
                <tr>
                  <td class="checklist-item">Work space</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['work_workspace']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Duty arrangements</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['work_duty_arrangements']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Schedule of meetings</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['work_schedule_meetings']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">First meeting with host supervisor</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['work_first_meeting_supervisor']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>

                <!-- Health and Safety Section -->
                <tr>
                  <td colspan="2" class="section-header"><strong><em>Health and Safety</em></strong></td>
                </tr>
                <tr>
                  <td class="checklist-item">Emergency procedures</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['health_emergency_procedures']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Safety policy received or location known</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['health_safety_policy']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">First aid arrangements such as location of first aid box, names of first aiders, etc.</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['health_first_aid_arrangements']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Fire procedures and location of fire extinguishers</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['health_fire_procedures']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Accident reporting and location of accident book</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['health_accident_reporting']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Manual handling procedures</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['health_manual_handling']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Safety regulations</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['health_safety_regulations']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Instruction on equipment and their use</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['health_equipment_instruction']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>

                <!-- Others Section -->
                <tr>
                  <td colspan="2" class="section-header"><strong><em>Others</em></strong></td>
                </tr>
                <tr>
                  <td class="checklist-item">Student information form (Contract form)</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['others_student_info_form']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">Social media guidelines</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['others_social_media_guidelines']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td class="checklist-item">IT systems and equipment</td>
                  <td style="text-align: center;">
                    <?php if($checklist_data['others_it_systems_equipment']): ?>
                      <span class="tick-box checked"></span>
                    <?php else: ?>
                      <span class="tick-box"></span>
                    <?php endif; ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Signature Section -->
            <div style="margin-top: 40px; margin-bottom: 30px;">
              <table style="width: 100%; border-collapse: collapse;">
                <tr>
                  <td style="width: 33%; padding: 10px; vertical-align: top;">
                    <div style="margin-bottom: 40px;">
                      <div style="border-bottom: 1px solid #000; margin-bottom: 5px; min-height: 50px;"></div>
                      <strong>Student:</strong><br>
                      <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 200px;">
                        <?php echo htmlspecialchars($checklist_data['student_signature'] ?? ''); ?>
                      </div>
                      <br>
                      <strong>Date:</strong>
                      <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 150px;">
                        <?php echo $checklist_data['student_signature_date'] ? date('Y-m-d', strtotime($checklist_data['student_signature_date'])) : ''; ?>
                      </div>
                    </div>
                  </td>
                  <td style="width: 33%; padding: 10px; vertical-align: top;">
                    <div style="margin-bottom: 40px;">
                      <div style="border-bottom: 1px solid #000; margin-bottom: 5px; min-height: 50px;"></div>
                      <strong>Host Supervisor:</strong><br>
                      <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 200px;">
                        <?php echo htmlspecialchars($checklist_data['host_supervisor_signature'] ?? ''); ?>
                      </div>
                      <br>
                      <strong>Date:</strong>
                      <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 150px;">
                        <?php echo $checklist_data['host_supervisor_date'] ? date('Y-m-d', strtotime($checklist_data['host_supervisor_date'])) : ''; ?>
                      </div>
                    </div>
                  </td>
                  <td style="width: 33%; padding: 10px; vertical-align: top;">
                    <div style="margin-bottom: 40px;">
                      <div style="border-bottom: 1px solid #000; margin-bottom: 5px; min-height: 50px;"></div>
                      <strong>WRL Coordinator:</strong><br>
                      <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 200px;">
                        <?php echo htmlspecialchars($checklist_data['wrl_coordinator_signature'] ?? ''); ?>
                      </div>
                      <br>
                      <strong>Date:</strong>
                      <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 150px;">
                        <?php echo $checklist_data['wrl_coordinator_date'] ? date('Y-m-d', strtotime($checklist_data['wrl_coordinator_date'])) : ''; ?>
                      </div>
                    </div>
                  </td>
                </tr>
              </table>
            </div>

            <div class="text-center mt-4">
              <p><strong>Completed on:</strong> <?php echo empty($checklist_data['completed_at']) ? '' : date('F j, Y', strtotime($checklist_data['completed_at'])); ?></p>
            </div>
          <?php else: ?>
            <!-- Document Header -->
            <div class="document-header">
              <img src="img/header_log.png" alt="University Logo" class="university-logo">
              <div class="university-name">UNIVERSITY OF ZIMBABWE</div>
              <div class="document-title">Work Related Learning Placement Student Orientation Checklist</div>
            </div>

            <!-- Checklist Form -->
            <form method="post" action="">
              <!-- Student Information -->
              <div class="student-info">
                <div class="info-line">
                  <span class="info-label">Name of student:</span>
                  <span class="info-field"><?php echo htmlspecialchars($student_full_name); ?></span>
                  <span class="info-label">Reg. Number:</span>
                  <span class="info-field"><?php echo htmlspecialchars($student_index_number); ?></span>
                </div>
                <div class="info-line">
                  <span class="info-label">Host Institution:</span>
                  <span class="info-field">
                    <input type="text" name="host_institution" class="host-institution-input" placeholder="Enter host institution name" required>
                  </span>
                </div>
              </div>

              <!-- Instructions -->
              <div class="instructions">
                (Please date the items below when they occur and inform the WRL Coordinator of any items not covered within one week of the start of the attachment period. *Complete where applicable according to Faculty expectations).
              </div>
              <table class="checklist-table">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Tick done</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- General Section -->
                  <tr>
                    <td colspan="2" class="section-header"><strong><em>General</em></strong></td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Introduction to key staff members and their roles explained</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="general_staff_introduction" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Location of facilities such as rest rooms, canteen, etc.</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="general_facilities_location" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Tea/coffee and lunch arrangements</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="general_tea_coffee_lunch" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Transport arrangements (if applicable)</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="general_transport_arrangements" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Dress code</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="general_dress_code" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Code of conduct</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="general_code_of_conduct" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Policies and regulations</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="general_policies_regulations" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>

                  <!-- Work-related Section -->
                  <tr>
                    <td colspan="2" class="section-header"><strong><em>Work-related</em></strong></td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Work space</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="work_workspace" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Duty arrangements</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="work_duty_arrangements" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Schedule of meetings</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="work_schedule_meetings" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">First meeting with host supervisor</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="work_first_meeting_supervisor" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>

                  <!-- Health and Safety Section -->
                  <tr>
                    <td colspan="2" class="section-header"><strong><em>Health and Safety</em></strong></td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Emergency procedures</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="health_emergency_procedures" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Safety policy received or location known</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="health_safety_policy" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">First aid arrangements such as location of first aid box, names of first aiders, etc.</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="health_first_aid_arrangements" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Fire procedures and location of fire extinguishers</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="health_fire_procedures" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Accident reporting and location of accident book</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="health_accident_reporting" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Manual handling procedures</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="health_manual_handling" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Safety regulations</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="health_safety_regulations" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Instruction on equipment and their use</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="health_equipment_instruction" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>

                  <!-- Others Section -->
                  <tr>
                    <td colspan="2" class="section-header"><strong><em>Others</em></strong></td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Student information form (Contract form)</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="others_student_info_form" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">Social media guidelines</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="others_social_media_guidelines" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                  <tr>
                    <td class="checklist-item">IT systems and equipment</td>
                    <td>
                      <label style="cursor: pointer;">
                        <input type="checkbox" name="others_it_systems_equipment" value="1">
                        <span class="tick-box"></span>
                      </label>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Signature Section -->
              <div style="margin-top: 40px; margin-bottom: 30px;">
                <table style="width: 100%; border-collapse: collapse;">
                  <tr>
                    <td style="width: 33%; padding: 10px; vertical-align: top;">
                      <div style="margin-bottom: 40px;">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 5px; min-height: 50px;"></div>
                        <strong>Student:</strong><br>
                        <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 200px;">
                          <?php if(!$has_completed): ?>
                            <input type="text" name="student_signature" placeholder="Student name/signature" style="border: none; background: transparent; width: 100%;">
                          <?php else: ?>
                            <?php echo htmlspecialchars($checklist_data['student_signature'] ?? ''); ?>
                          <?php endif; ?>
                        </div>
                        <br>
                        <strong>Date:</strong>
                        <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 150px;">
                          <?php if(!$has_completed): ?>
                            <input type="date" name="student_signature_date" style="border: none; background: transparent; width: 100%;">
                          <?php else: ?>
                            <?php echo $checklist_data['student_signature_date'] ? date('Y-m-d', strtotime($checklist_data['student_signature_date'])) : ''; ?>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td style="width: 33%; padding: 10px; vertical-align: top;">
                      <div style="margin-bottom: 40px;">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 5px; min-height: 50px;"></div>
                        <strong>Host Supervisor:</strong><br>
                        <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 200px;">
                          <?php if(!$has_completed): ?>
                            <input type="text" name="host_supervisor_signature" placeholder="Supervisor name/signature" style="border: none; background: transparent; width: 100%;">
                          <?php else: ?>
                            <?php echo htmlspecialchars($checklist_data['host_supervisor_signature'] ?? ''); ?>
                          <?php endif; ?>
                        </div>
                        <br>
                        <strong>Date:</strong>
                        <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 150px;">
                          <?php if(!$has_completed): ?>
                            <input type="date" name="host_supervisor_date" style="border: none; background: transparent; width: 100%;">
                          <?php else: ?>
                            <?php echo $checklist_data['host_supervisor_date'] ? date('Y-m-d', strtotime($checklist_data['host_supervisor_date'])) : ''; ?>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td style="width: 33%; padding: 10px; vertical-align: top;">
                      <div style="margin-bottom: 40px;">
                        <div style="border-bottom: 1px solid #000; margin-bottom: 5px; min-height: 50px;"></div>
                        <strong>WRL Coordinator:</strong><br>
                        <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 200px;">
                          <?php if(!$has_completed): ?>
                            <input type="text" name="wrl_coordinator_signature" placeholder="Coordinator name/signature" style="border: none; background: transparent; width: 100%;">
                          <?php else: ?>
                            <?php echo htmlspecialchars($checklist_data['wrl_coordinator_signature'] ?? ''); ?>
                          <?php endif; ?>
                        </div>
                        <br>
                        <strong>Date:</strong>
                        <div style="border-bottom: 1px dotted #000; margin-top: 5px; min-height: 20px; display: inline-block; width: 150px;">
                          <?php if(!$has_completed): ?>
                            <input type="date" name="wrl_coordinator_date" style="border: none; background: transparent; width: 100%;">
                          <?php else: ?>
                            <?php echo $checklist_data['wrl_coordinator_date'] ? date('Y-m-d', strtotime($checklist_data['wrl_coordinator_date'])) : ''; ?>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                  </tr>
                </table>
              </div>

              <div class="submit-section">
                <button type="submit" name="submit_checklist" class="btn btn-success btn-lg">
                  <i class="glyphicon glyphicon-check"></i> Submit Orientation Checklist
                </button>
                <p class="mt-3">
                  <small class="text-muted">
                    <i class="glyphicon glyphicon-info-sign"></i>
                    You can only submit this checklist once. Please ensure all applicable items are ticked before submitting.
                  </small>
                </p>
              </div>
            </form>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
// Make checkboxes work with custom tick boxes
$(document).ready(function(){
    $('input[type="checkbox"]').change(function(){
        if($(this).is(':checked')){
            $(this).next('.tick-box').addClass('checked');
        } else {
            $(this).next('.tick-box').removeClass('checked');
        }
    });
    
    // Initialize checkboxes if page is reloaded with data
    $('input[type="checkbox"]:checked').each(function(){
        $(this).next('.tick-box').addClass('checked');
    });
});
</script>

</body>
</html>