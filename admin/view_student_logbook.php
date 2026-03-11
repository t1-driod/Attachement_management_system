<?php
include '../database_connection/database_connection.php';

// Get student index number from URL
$index_number = isset($_GET['index_number']) ? mysqli_real_escape_string($conn, $_GET['index_number']) : '';

if(empty($index_number)){
    header("Location: view_elogbooks.php");
    exit();
}

// Get student information
$student_query = "SELECT DISTINCT student_name, index_number FROM elogbook_entries WHERE index_number='$index_number' LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student_info = mysqli_fetch_assoc($student_result);

if(!$student_info){
    header("Location: view_elogbooks.php");
    exit();
}

// Get all logbook entries for this student, ordered by week number
$entries_query = "SELECT * FROM elogbook_entries WHERE index_number='$index_number' ORDER BY week_number ASC";
$entries_result = mysqli_query($conn, $entries_query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IASMS - Student E-LogBook</title>

  <link rel="stylesheet" href="../css/bootstrap-theme.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap-select.css"/>
  <link rel="stylesheet" href="../css/main_page_style.css"/>

  <script type="text/javascript" src="../js/jquery-3.1.1.min.js"></script>
  <script type="text/javascript" src="../js/bootstrap.min.js"></script>

  <style>
    .week-section {
      margin-bottom: 30px;
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 15px;
      background-color: #f9f9f9;
    }
    .week-header {
      background-color: #007bff;
      color: white;
      padding: 10px 15px;
      margin: -15px -15px 15px -15px;
      border-radius: 5px 5px 0 0;
      font-weight: bold;
      font-size: 1.2em;
    }
    .day-row {
      margin-bottom: 10px;
      padding: 10px;
      background-color: white;
      border-left: 3px solid #007bff;
    }
    .day-label {
      font-weight: bold;
      color: #007bff;
      margin-bottom: 5px;
    }
    .back-button {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<?php $topbar_display_name = 'Admin'; $topbar_logo_src = '../img/header_log.png'; include '../includes/topbar.php'; ?>

<div id="left_side_bar">
<ul id="menu_list">
  <a class="menu_items_link" href="/iasms/admin/view_registered_students/view_registered_students.php"><li class="menu_items_list">Registered Students</li></a>
  <a class="menu_items_link" href="/iasms/admin/view_orientation_checklists.php"><li class="menu_items_list">Orientation Checklists</li></a>
  <a class="menu_items_link" href="/iasms/admin/view_elogbooks.php"><li class="menu_items_list" style="background-color:orange;padding-left:16px">E-Logbooks</li></a>
  <a class="menu_items_link" href="/iasms/admin/manage_contracts.php"><li class="menu_items_list">View Contracts</li></a>
  <a class="menu_items_link" href="/iasms/admin/view_submitted_reports.php"><li class="menu_items_list">View Submitted Reports</li></a>
  <a class="menu_items_link" href="/iasms/admin/students_assumptions/students_assumptions.php"><li class="menu_items_list">Student Assumptions</li></a>
  <a class="menu_items_link" href="/iasms/admin/assign_supervisors/assign_supervisors.php"><li class="menu_items_list">Assign Supervisors</li></a>
  <a class="menu_items_link" href="/iasms/admin/visiting_score/visiting_supervisors_score.php"><li class="menu_items_list">Visiting Superviors Score</li></a>
  <a class="menu_items_link" href="/iasms/admin/company_score/company_supervisor_score.php"><li class="menu_items_list">Company Supervisor Score</li></a>
  <a class="menu_items_link" href="/iasms/admin/change_password/change_password.php"><li class="menu_items_list">Change Password</li></a>
  <a class="menu_items_link" href="/iasms/index.php"><li class="menu_items_list">Logout</li></a>
</ul>
</div>

<div id="main_content">
  <div class="container-fluid">
    <div class="panel">
      <div class="panel-heading phead">
         <h2 class="panel-title ptitle">E-LogBook for <?php echo htmlspecialchars($student_info['student_name']); ?> (<?php echo htmlspecialchars($student_info['index_number']); ?>)</h2>
      </div>
      <div class="panel-body pbody">

        <div class="back-button">
          <a href="view_elogbooks.php" class="btn btn-default">
            <i class="glyphicon glyphicon-arrow-left"></i> Back to Student List
          </a>
        </div>

        <?php if(mysqli_num_rows($entries_result) > 0): ?>
          <?php while($entry = mysqli_fetch_assoc($entries_result)): ?>
            <div class="week-section">
              <div class="week-header">
                Week <?php echo (int)$entry['week_number']; ?>
                <small style="float: right; font-size: 0.8em; font-weight: normal;">
                  Created: <?php echo date('Y-m-d H:i', strtotime($entry['created_at'])); ?>
                  <?php if($entry['updated_at'] != $entry['created_at']): ?>
                    | Updated: <?php echo date('Y-m-d H:i', strtotime($entry['updated_at'])); ?>
                  <?php endif; ?>
                </small>
              </div>

              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width:15%;">Day</th>
                    <th style="width:42.5%;">Job Assigned</th>
                    <th style="width:42.5%;">Special Skill Acquired</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $days = [
                      'Monday' => ['monday_job_assigned', 'monday_skill_acquired'],
                      'Tuesday' => ['tuesday_job_assigned', 'tuesday_skill_acquired'],
                      'Wednesday' => ['wednesday_job_assigned', 'wednesday_skill_acquired'],
                      'Thursday' => ['thursday_job_assigned', 'thursday_skill_acquired'],
                      'Friday' => ['friday_job_assigned', 'friday_skill_acquired'],
                  ];

                  foreach ($days as $day => $fields) {
                      $job = isset($entry[$fields[0]]) && !empty($entry[$fields[0]]) ? nl2br(htmlspecialchars($entry[$fields[0]])) : '<em class="text-muted">Not filled</em>';
                      $skill = isset($entry[$fields[1]]) && !empty($entry[$fields[1]]) ? nl2br(htmlspecialchars($entry[$fields[1]])) : '<em class="text-muted">Not filled</em>';
                      echo "<tr>";
                      echo "<td><strong>$day</strong></td>";
                      echo "<td>$job</td>";
                      echo "<td>$skill</td>";
                      echo "</tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="alert alert-warning">
            <h4>No logbook entries found.</h4>
            <p>This student has not submitted any logbook entries yet.</p>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

</body>
</html>
