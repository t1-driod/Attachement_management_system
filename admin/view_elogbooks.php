<?php
include '../database_connection/database_connection.php';

$message = "";
$status = "";

// Handle search
$search_term = "";
$search_by = "all";
$where_clause = "";

if(isset($_POST["btn_search"])){
  $search_by = $_POST["filter-by"];
  $search_term = mysqli_real_escape_string($conn, $_POST["txt_search_term"]);
} elseif(isset($_GET["filter-by"]) && isset($_GET["txt_search_term"])){
  $search_by = $_GET["filter-by"];
  $search_term = mysqli_real_escape_string($conn, $_GET["txt_search_term"]);
}

// Build WHERE clause for filtering
if($search_by != "all" && $search_term != ""){
  switch ($search_by) {
    case 'Student Name':
      $where_clause = " WHERE student_name LIKE '%$search_term%'";
      break;
    case 'Index Number':
      $where_clause = " WHERE index_number LIKE '%$search_term%'";
      break;
    default:
      $where_clause = "";
      break;
  }
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build query string for pagination with search parameters
$query_string = "";
if($search_term != "" && $search_by != "all") {
  $query_string = "&filter-by=" . urlencode($search_by) . "&txt_search_term=" . urlencode($search_term);
}

// Get unique students who have submitted logbooks
// Group by index_number to show each student only once
$total_query = "SELECT COUNT(DISTINCT index_number) as total FROM elogbook_entries" . $where_clause;
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_students = $total_row['total'];
$total_pages = $total_students > 0 ? ceil($total_students / $per_page) : 1;

// Validate current page
if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;
$offset = ($page - 1) * $per_page;

// Get unique students with their logbook entry counts
$students_query = "SELECT 
    student_name, 
    index_number, 
    COUNT(*) as total_weeks,
    MIN(created_at) as first_submission,
    MAX(updated_at) as last_updated
FROM elogbook_entries" . $where_clause . "
GROUP BY index_number, student_name
ORDER BY last_updated DESC
LIMIT $offset, $per_page";
$students_result = mysqli_query($conn, $students_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IASMS - View E-LogBooks</title>

  <link rel="stylesheet" href="../css/bootstrap-theme.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap-select.css"/>
  <link rel="stylesheet" href="../css/main_page_style.css"/>

  <script type="text/javascript" src="../js/jquery-3.1.1.min.js"></script>
  <script type="text/javascript" src="../js/bootstrap.min.js"></script>

  <style>
    .elogbook-table { margin-top: 20px; }
    .week-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 4px;
      background-color: #e9ecef;
      font-size: 0.9em;
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
         <h2 class="panel-title ptitle"> View Student E-LogBooks</h2>
      </div>
      <div class="panel-body pbody">

        <?php if($message != ""): ?>
        <div class="alert alert-<?php echo ($status == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade in">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <!-- Search Form -->
        <form method="post" action="">
          <div class="row" style="margin-bottom: 15px;">
            <div class="col-xs-5 col-xs-offset-6">
              <div class="input-group">
                <div class="input-group-btn search-panel">
                  <select class="form-control search_by_side" name="filter-by">
                    <option value="all" <?php echo ($search_by == 'all') ? 'selected' : ''; ?>>Filter By</option>
                    <option value="Student Name" <?php echo ($search_by == 'Student Name') ? 'selected' : ''; ?>>Student Name</option>
                    <option value="Index Number" <?php echo ($search_by == 'Index Number') ? 'selected' : ''; ?>>Index Number</option>
                  </select>
                </div>
                <input type="text" class="form-control" name="txt_search_term" placeholder="Search students..." value="<?php echo htmlspecialchars($search_term); ?>">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit" name="btn_search">
                    <i class="glyphicon glyphicon-search"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>
        </form>

        <div class="row" style="margin-bottom: 15px;">
          <div class="col-md-12">
            <h4>Students with Submitted E-LogBooks: <?php echo $total_students; ?>
              <?php if($search_term != "" && $search_by != "all"): ?>
                <small>(filtered by <?php echo htmlspecialchars($search_by); ?>: "<?php echo htmlspecialchars($search_term); ?>")</small>
              <?php endif; ?>
            </h4>
          </div>
        </div>

        <?php if(mysqli_num_rows($students_result) > 0): ?>
        <div class="table-responsive elogbook-table">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Student Name</th>
                <th>Index Number</th>
                <th>Total Weeks Submitted</th>
                <th>First Submission</th>
                <th>Last Updated</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while($student = mysqli_fetch_assoc($students_result)): ?>
              <tr>
                <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                <td><?php echo htmlspecialchars($student['index_number']); ?></td>
                <td><span class="week-badge"><?php echo (int)$student['total_weeks']; ?> week(s)</span></td>
                <td><?php echo date('Y-m-d H:i', strtotime($student['first_submission'])); ?></td>
                <td><?php echo date('Y-m-d H:i', strtotime($student['last_updated'])); ?></td>
                <td>
                  <a href="view_student_logbook.php?index_number=<?php echo urlencode($student['index_number']); ?>" class="btn btn-sm btn-primary">
                    <i class="glyphicon glyphicon-book"></i> View Logbook
                  </a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav aria-label="E-LogBook pagination">
          <ul class="pagination justify-content-center">
            <?php if($page > 1): ?>
              <li class="page-item"><a class="page-link" href="?page=<?php echo $page-1 . $query_string; ?>">Previous</a></li>
            <?php endif; ?>

            <?php for($i = 1; $i <= $total_pages; $i++): ?>
              <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i . $query_string; ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
              <li class="page-item"><a class="page-link" href="?page=<?php echo $page+1 . $query_string; ?>">Next</a></li>
            <?php endif; ?>
          </ul>
        </nav>
        <?php endif; ?>

        <?php else: ?>
        <div class="alert alert-info">
          <h4>No students with submitted E-LogBooks found.</h4>
          <p>Students who have submitted logbook entries will appear here once they start using the E-LogBook.</p>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

</body>
</html>

