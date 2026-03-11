<?php
/**
 * Admin page: list industrial attachment reports submitted by students.
 * Files are stored in submit_report/uploads/ (no DB record).
 */

$uploads_dir = dirname(__DIR__) . '/submit_report/uploads';
$allowed_extensions = array('doc', 'docx', 'pdf');
$reports = array();

if (is_dir($uploads_dir)) {
    $files = array_diff(scandir($uploads_dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $uploads_dir . DIRECTORY_SEPARATOR . $file;
        if (is_file($path)) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_extensions)) {
                $reports[] = array(
                    'name' => $file,
                    'size' => filesize($path),
                    'modified' => filemtime($path),
                );
            }
        }
    }
    // Sort by modified date, newest first
    usort($reports, function ($a, $b) {
        return $b['modified'] - $a['modified'];
    });
}
?>

<!DOCTYPE html>
<html lang="en" class="bg-pink">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IASMS - View Submitted Reports</title>

  <link rel="stylesheet" href="../css/bootstrap-theme.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap.min.css"/>
  <link rel="stylesheet" href="../css/main_page_style.css"/>

  <script type="text/javascript" src="../js/jquery-3.1.1.min.js"></script>
  <script type="text/javascript" src="../js/bootstrap.min.js"></script>
</head>
<body>

<?php $topbar_display_name = 'Admin'; $topbar_logo_src = '../img/header_log.png'; include '../includes/topbar.php'; ?>

<div id="left_side_bar">
<ul id="menu_list">
  <a class="menu_items_link" href="view_registered_students/view_registered_students.php"><li class="menu_items_list">Registered Students</li></a>
  <a class="menu_items_link" href="view_orientation_checklists.php"><li class="menu_items_list">Orientation Checklists</li></a>
  <a class="menu_items_link" href="view_elogbooks.php"><li class="menu_items_list">E-Logbooks</li></a>
  <a class="menu_items_link" href="manage_contracts.php"><li class="menu_items_list">View Contracts</li></a>
  <a class="menu_items_link" href="view_submitted_reports.php"><li class="menu_items_list" style="background-color:orange;padding-left:16px">View Submitted Reports</li></a>
  <a class="menu_items_link" href="students_assumptions/students_assumptions.php"><li class="menu_items_list">Student Assumptions</li></a>
  <a class="menu_items_link" href="assign_supervisors/assign_supervisors.php"><li class="menu_items_list">Assign Supervisors</li></a>
  <a class="menu_items_link" href="visiting_score/visiting_supervisors_score.php"><li class="menu_items_list">Visiting Superviors Score</li></a>
  <a class="menu_items_link" href="company_score/company_supervisor_score.php"><li class="menu_items_list">Company Supervisor Score</li></a>
  <a class="menu_items_link" href="change_password/change_password.php"><li class="menu_items_list">Change Password</li></a>
  <a class="menu_items_link" href="../index.php"><li class="menu_items_list">Logout</li></a>
</ul>
</div>

<div id="main_content">
  <div class="container-fluid">
    <div class="panel">
      <div class="panel-heading phead">
        <h2 class="panel-title ptitle">View Submitted Reports</h2>
      </div>
      <div class="panel-body pbody">
        <p class="text-muted">Industrial attachment reports submitted by students. Files are named by index number (e.g. Word/PDF).</p>
        <?php if (empty($reports)): ?>
          <div class="alert alert-info">No submitted reports found.</div>
        <?php else: ?>
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th style="text-align:center">#</th>
                <th style="text-align:center">File name (Index / Report)</th>
                <th style="text-align:center">Size</th>
                <th style="text-align:center">Submitted on</th>
                <th style="text-align:center">View / Download</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reports as $i => $r): ?>
                <tr style="text-align:center;font-size:1em">
                  <td><?php echo ($i + 1); ?></td>
                  <td><?php echo htmlspecialchars($r['name']); ?></td>
                  <td><?php echo number_format($r['size'] / 1024, 1); ?> KB</td>
                  <td><?php echo date('d M Y H:i', $r['modified']); ?></td>
                  <td>
                    <a href="view_report.php?file=<?php echo urlencode($r['name']); ?>" class="btn btn-default btn-sm" target="_blank" title="Open in browser">View</a>
                    <a href="download_report.php?file=<?php echo urlencode($r['name']); ?>" class="btn btn-primary btn-sm">Download</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>
