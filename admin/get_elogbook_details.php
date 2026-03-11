<?php
include '../database_connection/database_connection.php';

if(isset($_GET['id'])) {
    $entry_id = (int)$_GET['id'];

    $query = "SELECT * FROM elogbook_entries WHERE id = '$entry_id'";
    $result = mysqli_query($conn, $query);

    if($result && mysqli_num_rows($result) > 0) {
        $entry = mysqli_fetch_assoc($result);

        echo "<div class='elogbook-details'>";
        echo "<h4>" . htmlspecialchars($entry['student_name']) . " (" . htmlspecialchars($entry['index_number']) . ")</h4>";
        echo "<p><strong>Week:</strong> " . (int)$entry['week_number'] . "</p>";
        echo "<p><strong>Created on:</strong> " . date('F j, Y \a\t g:i A', strtotime($entry['created_at'])) . "</p>";
        echo "<p><strong>Last updated:</strong> " . date('F j, Y \a\t g:i A', strtotime($entry['updated_at'])) . "</p>";
        echo "<hr>";

        echo "<table class='table table-bordered' style='margin-top: 10px;'>";
        echo "<thead><tr><th style='width:15%;'>Day</th><th>Job Assigned</th><th>Special Skill Acquired</th></tr></thead>";
        echo "<tbody>";

        $days = [
            'Monday' => ['monday_job_assigned', 'monday_skill_acquired'],
            'Tuesday' => ['tuesday_job_assigned', 'tuesday_skill_acquired'],
            'Wednesday' => ['wednesday_job_assigned', 'wednesday_skill_acquired'],
            'Thursday' => ['thursday_job_assigned', 'thursday_skill_acquired'],
            'Friday' => ['friday_job_assigned', 'friday_skill_acquired'],
        ];

        foreach ($days as $day => $fields) {
            $job = isset($entry[$fields[0]]) ? nl2br(htmlspecialchars($entry[$fields[0]])) : '';
            $skill = isset($entry[$fields[1]]) ? nl2br(htmlspecialchars($entry[$fields[1]])) : '';
            echo "<tr>";
            echo "<td><strong>$day</strong></td>";
            echo "<td>$job</td>";
            echo "<td>$skill</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";

        echo "</div>";
    } else {
        echo "<div class='alert alert-danger'>E-LogBook entry not found.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Invalid request.</div>";
}
?>

