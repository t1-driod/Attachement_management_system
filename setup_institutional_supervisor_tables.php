<?php
include 'database_connection/database_connection.php';

// Institutional supervisors are now in visiting_lecturers (staff_id + password).
// Only create the assignment table; supervisor_id references visiting_lecturers.id.
$sql_students = "CREATE TABLE IF NOT EXISTS institutional_supervisor_students (
  id INT(11) NOT NULL AUTO_INCREMENT,
  supervisor_id INT(11) NOT NULL,
  student_index VARCHAR(100) NOT NULL,
  visit_number TINYINT(1) NOT NULL,
  PRIMARY KEY (id),
  KEY supervisor_id (supervisor_id),
  KEY student_index (student_index)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";

$ok = true;

if (mysqli_query($conn, $sql_students)) {
  echo "<h2>✅ SUCCESS: institutional_supervisor_students table created (or already exists)!</h2>";
} else {
  $ok = false;
  echo "<h2>❌ ERROR: Failed to create institutional_supervisor_students table</h2>";
  echo "<p>Error: " . mysqli_error($conn) . "</p>";
}

if ($ok) {
  echo "<h3>The institutional supervisor module is ready.</h3>";
  echo "<p>Supervisors are added as lecturers in <a href='admin/assign_supervisors/assign_supervisors.php'>Assign Supervisors</a> with Staff ID and Password to enable institutional login.</p>";
  echo "<p>They can also <a href='institutional_supervisor/institutional_supervisor_signup.php'>sign up here</a> (stored in visiting_lecturers).</p>";
  echo "<p><strong>Note:</strong> Run the migration in admin/assign_supervisors/migration_visiting_lecturers_institutional_merge.sql to add staff_id and password columns to visiting_lecturers if not already done.</p>";
}

mysqli_close($conn);
?>

