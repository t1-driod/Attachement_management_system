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

// Get all orientation checklists with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build query string for pagination with search parameters
$query_string = "";
if($search_term != "" && $search_by != "all") {
  $query_string = "&filter-by=" . urlencode($search_by) . "&txt_search_term=" . urlencode($search_term);
}

// Get total count with search filter
$total_query = "SELECT COUNT(*) as total FROM orientation_checklist" . $where_clause;
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_checklists = $total_row['total'];
$total_pages = ceil($total_checklists / $per_page);

// Get checklists for current page with search filter
$checklists_query = "SELECT * FROM orientation_checklist" . $where_clause . " ORDER BY completed_at DESC LIMIT $offset, $per_page";
$checklists_result = mysqli_query($conn, $checklists_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IASMS - View Orientation Checklists</title>

  <link rel="stylesheet" href="../css/bootstrap-theme.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap.min.css"/>
  <link rel="stylesheet" href="../css/bootstrap-select.css"/>
  <link rel="stylesheet" href="../css/main_page_style.css"/>
  <link rel="stylesheet" href="admin.css"/>

  <script type="text/javascript" src="../js/jquery-3.1.1.min.js"/></script>
  <script type="text/javascript" src="../js/bootstrap.min.js"/></script>

  <style>
    .checklist-table { margin-top: 20px; }
    .completed-item { color: #28a745; font-weight: bold; }
    .pending-item { color: #6c757d; }
    .checklist-preview { max-height: 300px; overflow-y: auto; }
    .item-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 10px;
      margin-top: 10px;
    }
    .item-box {
      padding: 8px;
      border-radius: 4px;
      font-size: 12px;
    }
  </style>
</head>
<body>

<?php $topbar_display_name = 'Admin'; $topbar_logo_src = '../img/header_log.png'; include '../includes/topbar.php'; ?>

<div id="left_side_bar">
<ul id="menu_list">
  <a class="menu_items_link" href="/iasms/admin/view_registered_students/view_registered_students.php"><li class="menu_items_list">Registered Students</li></a>
  <a class="menu_items_link" href="/iasms/admin/view_orientation_checklists.php"><li class="menu_items_list" style="background-color:orange;padding-left:16px">Orientation Checklists</li></a>
  <a class="menu_items_link" href="/iasms/admin/view_elogbooks.php"><li class="menu_items_list">E-Logbooks</li></a>
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
         <h2 class="panel-title ptitle"> View Student Orientation Checklists</h2>
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
          <div class="row mb-3">
            <div class="col-xs-5 col-xs-offset-6">
              <div class="input-group">
                <div class="input-group-btn search-panel">
                  <select class="form-control search_by_side" name="filter-by">
                    <option value="all" <?php echo ($search_by == 'all') ? 'selected' : ''; ?>>Filter By</option>
                    <option value="Student Name" <?php echo ($search_by == 'Student Name') ? 'selected' : ''; ?>>Student Name</option>
                    <option value="Index Number" <?php echo ($search_by == 'Index Number') ? 'selected' : ''; ?>>Index Number</option>
                  </select>
                </div>
                <input type="text" class="form-control" name="txt_search_term" placeholder="Search checklists..." value="<?php echo htmlspecialchars($search_term); ?>">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit" name="btn_search">
                    <i class="glyphicon glyphicon-search"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>
        </form>

        <div class="row mb-3">
          <div class="col-md-12">
            <h4>Total Checklists: <?php echo $total_checklists; ?>
              <?php if($search_term != ""): ?>
                <small>(filtered by <?php echo $search_by; ?>: "<?php echo htmlspecialchars($search_term); ?>")</small>
              <?php endif; ?>
            </h4>
          </div>
        </div>

        <?php if(mysqli_num_rows($checklists_result) > 0): ?>
        <div class="table-responsive checklist-table">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Student Name</th>
                <th>Index Number</th>
                <th>Completed Items</th>
                <th>Completion Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while($checklist = mysqli_fetch_assoc($checklists_result)):
                // Count completed items
                $completed_count = 0;
                $total_items = 0;
                $item_fields = ['general_staff_introduction', 'general_facilities_location', 'general_tea_coffee_lunch',
                               'general_transport_arrangements', 'general_dress_code', 'general_code_of_conduct',
                               'general_policies_regulations', 'work_workspace', 'work_duty_arrangements',
                               'work_schedule_meetings', 'work_first_meeting_supervisor', 'health_emergency_procedures',
                               'health_safety_policy', 'health_first_aid_arrangements', 'health_fire_procedures',
                               'health_accident_reporting', 'health_manual_handling', 'health_safety_regulations',
                               'health_equipment_instruction', 'others_student_info_form', 'others_social_media_guidelines',
                               'others_it_systems_equipment'];

                foreach($item_fields as $field) {
                  $total_items++;
                  if(isset($checklist[$field]) && $checklist[$field]) $completed_count++;
                }
              ?>
              <tr>
                <td><?php echo htmlspecialchars($checklist['student_name']); ?></td>
                <td><?php echo htmlspecialchars($checklist['index_number']); ?></td>
                <td>
                  <span class="badge" style="background-color: <?php echo ($completed_count == $total_items) ? '#28a745' : '#ffc107'; ?>">
                    <?php echo $completed_count; ?>/<?php echo $total_items; ?> completed
                  </span>
                </td>
                <td><?php echo date('Y-m-d H:i', strtotime($checklist['completed_at'])); ?></td>
                <td>
                  <button class="btn btn-sm btn-info" onclick="viewChecklist(<?php echo $checklist['id']; ?>)">
                    <i class="glyphicon glyphicon-eye-open"></i> View Details
                  </button>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav aria-label="Checklist pagination">
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
          <h4>No orientation checklists found.</h4>
          <p>Students will submit their orientation checklists here for review.</p>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<!-- Checklist Details Modal -->
<div class="modal fade" id="checklistModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Orientation Checklist Details</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="checklistDetails">
        <!-- Content will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function viewChecklist(checklistId) {
    // Load checklist details via AJAX
    $.get('get_checklist_details.php?id=' + checklistId, function(data) {
        $('#checklistDetails').html(data);
        $('#checklistModal').modal('show');
    });
}
</script>

</body>
</html>