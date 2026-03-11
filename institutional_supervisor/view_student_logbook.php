<?php
include '../database_connection/database_connection.php';

$supervisor_id = $_COOKIE["inst_supervisor_id"] ?? '';
$supervisor_name = $_COOKIE["inst_supervisor_name"] ?? '';
$supervisor_staff_id = $_COOKIE["inst_supervisor_staff_id"] ?? '';

if ($supervisor_id === '' || $supervisor_staff_id === '') {
  header("Location: institutional_supervisor_login.php");
  exit();
}

$index_number = isset($_GET['index_number']) ? mysqli_real_escape_string($conn, $_GET['index_number']) : '';

if ($index_number === '') {
  header("Location: dashboard.php");
  exit();
}

$supervisor_id_safe = mysqli_real_escape_string($conn, $supervisor_id);

// Allowed only if this supervisor is assigned to this student via assigned_lecturers (region + faculty)
$allowed = false;
$res_lec = mysqli_query($conn, "SELECT lecturer_name FROM visiting_lecturers WHERE id = '$supervisor_id_safe' LIMIT 1");
$lecturer_name = '';
if ($res_lec && ($row_lec = mysqli_fetch_assoc($res_lec))) {
  $lecturer_name = trim($row_lec['lecturer_name'] ?? '');
}
if ($lecturer_name !== '') {
  $faculty_db_map = array('AGR'=>array('AGR'),'ARTS'=>array('ARTS'),'COM'=>array('COM','FAST'),'CIE'=>array('CIE'),'EDU'=>array('EDU'),'ENG'=>array('ENG','FOE'),'LAW'=>array('LAW'),'MED'=>array('MED'),'SCI'=>array('SCI','FBNE'),'SOC'=>array('SOC','FBMS'),'VET'=>array('VET','FHAS'));
  $faculty_codes = array('agr','arts','com','cie','edu','eng','law','med','sci','soc','vet');
  $faculties_upper = array('AGR','ARTS','COM','CIE','EDU','ENG','LAW','MED','SCI','SOC','VET');
  $v = mysqli_query($conn, "SELECT attachment_region, faculty FROM industrial_registration WHERE index_number = '$index_number' LIMIT 1");
  if ($v && mysqli_num_rows($v) > 0) {
    $vr = mysqli_fetch_assoc($v);
    $reg = mysqli_real_escape_string($conn, $vr['attachment_region'] ?? '');
    $stu_fac = trim($vr['faculty'] ?? '');
    if ($reg !== '' && $stu_fac !== '') {
      $ar = mysqli_query($conn, "SELECT * FROM assigned_lecturers WHERE regions = '$reg' LIMIT 1");
      if ($ar && mysqli_num_rows($ar) > 0) {
        $ar = mysqli_fetch_assoc($ar);
        foreach ($faculty_codes as $i => $f) {
          $fac_list = $faculty_db_map[$faculties_upper[$i]] ?? array();
          if (!in_array($stu_fac, $fac_list)) continue;
          if (trim($ar["first_supervisor_{$f}"] ?? '') === $lecturer_name || trim($ar["second_supervisor_{$f}"] ?? '') === $lecturer_name) {
            $allowed = true;
            break;
          }
        }
      }
    }
  }
}
if (!$allowed) {
  echo "<script>alert('You are not assigned to this student.');window.location.href='dashboard.php';</script>";
  exit();
}

$student_query = "
  SELECT DISTINCT student_name, index_number 
  FROM elogbook_entries 
  WHERE index_number='$index_number' 
  LIMIT 1";
$student_result = mysqli_query($conn, $student_query);
$student_info = mysqli_fetch_assoc($student_result);

if (!$student_info) {
  header("Location: dashboard.php");
  exit();
}

$entries_query = "
  SELECT * 
  FROM elogbook_entries 
  WHERE index_number='$index_number' 
  ORDER BY week_number ASC";
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
  </style>
</head>
<body>

<?php $topbar_display_name = htmlspecialchars($supervisor_name) . ' (' . htmlspecialchars($supervisor_staff_id) . ')'; $topbar_logo_src = '../img/header_log.png'; include '../includes/topbar.php'; ?>

<div id="left_side_bar">
  <ul id="menu_list">
    <a class="menu_items_link" href="dashboard.php">
      <li class="menu_items_list">Dashboard</li>
    </a>
    <a class="menu_items_link" href="../index.php">
      <li class="menu_items_list">Logout</li>
    </a>
  </ul>
</div>

<div id="main_content">
  <div class="container-fluid">
    <div class="panel">
      <div class="panel-heading phead">
        <h2 class="panel-title ptitle">
          E-LogBook for
          <?php echo htmlspecialchars($student_info['student_name']); ?>
          (<?php echo htmlspecialchars($student_info['index_number']); ?>)
        </h2>
      </div>
      <div class="panel-body pbody">

        <div class="back-button" style="margin-bottom:20px;">
          <a href="dashboard.php" class="btn btn-default">
            <i class="glyphicon glyphicon-arrow-left"></i> Back to Dashboard
          </a>
        </div>

        <?php if (mysqli_num_rows($entries_result) > 0): ?>
          <?php while ($entry = mysqli_fetch_assoc($entries_result)): ?>
            <div class="week-section">
              <div class="week-header">
                Week <?php echo (int)$entry['week_number']; ?>
                <small style="float: right; font-size: 0.8em; font-weight: normal;">
                  Created: <?php echo date('Y-m-d H:i', strtotime($entry['created_at'])); ?>
                  <?php if ($entry['updated_at'] != $entry['created_at']): ?>
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
                    $job = isset($entry[$fields[0]]) && !empty($entry[$fields[0]])
                      ? nl2br(htmlspecialchars($entry[$fields[0]]))
                      : '<em class="text-muted">Not filled</em>';
                    $skill = isset($entry[$fields[1]]) && !empty($entry[$fields[1]])
                      ? nl2br(htmlspecialchars($entry[$fields[1]]))
                      : '<em class="text-muted">Not filled</em>';
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

