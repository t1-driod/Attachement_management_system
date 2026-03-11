<?php
include '../database_connection/database_connection.php';

$supervisor_id = isset($_COOKIE["inst_supervisor_id"]) ? trim((string)$_COOKIE["inst_supervisor_id"]) : '';
$supervisor_name = isset($_COOKIE["inst_supervisor_name"]) ? trim((string)$_COOKIE["inst_supervisor_name"]) : '';
$supervisor_staff_id = $_COOKIE["inst_supervisor_staff_id"] ?? '';

// Require valid numeric supervisor id
if ($supervisor_id === '' || $supervisor_staff_id === '' || !ctype_digit($supervisor_id)) {
  header("Location: institutional_supervisor_login.php");
  exit();
}
$supervisor_id_safe = mysqli_real_escape_string($conn, $supervisor_id);

// Get supervisor name from DB if not in cookie (so we match assigned_lecturers which stores names)
if ($supervisor_name === '') {
  $rn = mysqli_query($conn, "SELECT lecturer_name FROM visiting_lecturers WHERE id = '$supervisor_id_safe' LIMIT 1");
  if ($rn && ($rrow = mysqli_fetch_assoc($rn))) {
    $supervisor_name = trim($rrow['lecturer_name'] ?? '');
  }
}
$supervisor_name_esc = mysqli_real_escape_string($conn, $supervisor_name);

$summary = [
  'total_students' => 0,
  'first_visit' => 0,
  'second_visit' => 0,
  'first_visit_with_scoresheet' => 0,
  'second_visit_with_scoresheet' => 0
];

// Students assigned to this supervisor via assigned_lecturers: (region, faculty) where this supervisor is first or second, then industrial_registration by region+faculty
$students = [];
$seen_index = [];
$faculty_db_map = array('AGR'=>array('AGR'), 'ARTS'=>array('ARTS'), 'COM'=>array('COM','FAST'), 'CIE'=>array('CIE'), 'EDU'=>array('EDU'), 'ENG'=>array('ENG','FOE'), 'LAW'=>array('LAW'), 'MED'=>array('MED'), 'SCI'=>array('SCI','FBNE'), 'SOC'=>array('SOC','FBMS'), 'VET'=>array('VET','FHAS'));
$faculty_codes = array('agr','arts','com','cie','edu','eng','law','med','sci','soc','vet');
$faculties_upper = array('AGR','ARTS','COM','CIE','EDU','ENG','LAW','MED','SCI','SOC','VET');

$al_res = mysqli_query($conn, "SELECT regions, first_supervisor_agr, second_supervisor_agr, first_supervisor_arts, second_supervisor_arts, first_supervisor_com, second_supervisor_com, first_supervisor_cie, second_supervisor_cie, first_supervisor_edu, second_supervisor_edu, first_supervisor_eng, second_supervisor_eng, first_supervisor_law, second_supervisor_law, first_supervisor_med, second_supervisor_med, first_supervisor_sci, second_supervisor_sci, first_supervisor_soc, second_supervisor_soc, first_supervisor_vet, second_supervisor_vet FROM assigned_lecturers");
if ($al_res && $supervisor_name !== '') {
  while ($al = mysqli_fetch_assoc($al_res)) {
    $region = $al['regions'] ?? '';
    $region_esc = mysqli_real_escape_string($conn, $region);
    if ($region === '') continue;
    foreach ($faculty_codes as $i => $f) {
      $first = trim($al["first_supervisor_{$f}"] ?? '');
      $second = trim($al["second_supervisor_{$f}"] ?? '');
      if ($first !== $supervisor_name && $second !== $supervisor_name) continue;
      $fac_canonical = $faculties_upper[$i];
      $fac_list = isset($faculty_db_map[$fac_canonical]) ? $faculty_db_map[$fac_canonical] : array($fac_canonical);
      $fac_in = "'" . implode("','", array_map(function($x) use ($conn) { return mysqli_real_escape_string($conn, $x); }, $fac_list)) . "'";
      $stu_res = mysqli_query($conn, "SELECT index_number FROM industrial_registration WHERE attachment_region = '$region_esc' AND faculty IN ($fac_in)");
      if (!$stu_res) continue;
      while ($stu = mysqli_fetch_assoc($stu_res)) {
        $idx = $stu['index_number'] ?? '';
        if ($idx !== '' && !isset($seen_index[$idx])) {
          $seen_index[$idx] = true;
          $idx_esc = mysqli_real_escape_string($conn, $idx);
          $qi = "SELECT i.index_number AS student_index, i.first_name, i.last_name,
                 COALESCE(NULLIF(TRIM(sa.company_region),''), i.attachment_region) AS attachment_region,
                 COALESCE(sa.company_name,'') AS company_name,
                 COALESCE(NULLIF(TRIM(sa.company_region),''), i.attachment_region) AS company_region, 1 AS visit_number
                 FROM industrial_registration i
                 LEFT JOIN students_assumption sa ON sa.index_number = i.index_number
                 WHERE i.index_number = '$idx_esc'";
          $ri = mysqli_query($conn, $qi);
          if ($ri && ($row = mysqli_fetch_assoc($ri))) {
            $students[] = $row;
          }
        }
      }
    }
  }
}

$summary['total_students'] = count($students);
$assigned_indexes = array_keys($seen_index);

// Visit/scoresheet counts: use student list only (no institutional_supervisor_students)
if (!empty($assigned_indexes)) {
  $in_list = "'" . implode("','", array_map(function($idx) use ($conn) { return mysqli_real_escape_string($conn, $idx); }, $assigned_indexes)) . "'";
  $summary['first_visit'] = count($assigned_indexes);
  $summary['second_visit'] = 0;
  $res_first_scores = mysqli_query($conn, "SELECT COUNT(DISTINCT vsg.user_index) AS c FROM visiting_supervisor_grade vsg WHERE vsg.user_index IN ($in_list)");
  if ($res_first_scores) { $row = mysqli_fetch_assoc($res_first_scores); $summary['first_visit_with_scoresheet'] = (int)$row['c']; }
  $summary['second_visit_with_scoresheet'] = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IASMS - Institutional Supervisor Dashboard</title>

  <link rel="stylesheet" href="../css/bootstrap-theme.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap-select.css"/>
  <link rel="stylesheet" href="../css/main_page_style.css"/>

  <script type="text/javascript" src="../js/jquery-3.1.1.min.js"></script>
  <script type="text/javascript" src="../js/bootstrap.min.js"></script>

  <style>
    .stat-card {
      text-align: center;
      padding: 15px;
      border-radius: 6px;
      color: #fff;
      margin-bottom: 20px;
    }
    .bg-total { background-color: #007bff; }
    .bg-first { background-color: #28a745; }
    .bg-second { background-color: #17a2b8; }
    .bg-score { background-color: #ffc107; color: #333; }
    .sidebar-title {
      font-weight: bold;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<?php $topbar_display_name = htmlspecialchars($supervisor_name) . ' (' . htmlspecialchars($supervisor_staff_id) . ')'; $topbar_logo_src = '../img/header_log.png'; include '../includes/topbar.php'; ?>

<div id="left_side_bar">
  <ul id="menu_list">
    <a class="menu_items_link" href="dashboard.php">
      <li class="menu_items_list" style="background-color:orange;padding-left:16px">
        Dashboard
      </li>
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
        <h2 class="panel-title ptitle">Institutional Supervisor Dashboard</h2>
      </div>
      <div class="panel-body pbody">

        <div class="row">
          <div class="col-md-3">
            <div class="stat-card bg-total">
              <h4>Total Assigned Students</h4>
              <h2><?php echo $summary['total_students']; ?></h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card bg-first">
              <h4>First Visits</h4>
              <h2><?php echo $summary['first_visit']; ?></h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card bg-second">
              <h4>Second Visits</h4>
              <h2><?php echo $summary['second_visit']; ?></h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card bg-score">
              <h4>Scoresheets (1st / 2nd)</h4>
              <h2>
                <?php echo $summary['first_visit_with_scoresheet']; ?>
                /
                <?php echo $summary['second_visit_with_scoresheet']; ?>
              </h2>
            </div>
          </div>
        </div>

        <hr>

        <h3>Assigned Students</h3>
        <?php if (empty($students)): ?>
          <div class="alert alert-info">
            No students have been assigned to you yet.
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th style="text-align:center">Index Number</th>
                  <th style="text-align:center">Student Name</th>
                  <th style="text-align:center">Visit</th>
                  <th style="text-align:center">Company</th>
                  <th style="text-align:center">Region</th>
                  <th style="text-align:center">Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($students as $s): ?>
                <tr style="text-align:center">
                  <td><?php echo htmlspecialchars($s['student_index']); ?></td>
                  <td><?php echo htmlspecialchars(trim(($s['first_name'] ?? '').' '.($s['last_name'] ?? ''))); ?></td>
                  <td>
                    <?php
                      if ((int)$s['visit_number'] === 1) {
                        echo "First Visit";
                      } elseif ((int)$s['visit_number'] === 2) {
                        echo "Second Visit";
                      } else {
                        echo htmlspecialchars($s['visit_number']);
                      }
                    ?>
                  </td>
                  <td><?php echo htmlspecialchars($s['company_name'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($s['company_region'] ?? ''); ?></td>
                  <td>
                    <a href="view_student_logbook.php?index_number=<?php echo urlencode($s['student_index']); ?>"
                       class="btn btn-xs btn-primary">
                      View Logbook
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

</body>
</html>

