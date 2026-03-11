<?php
include 'database_connection/database_connection.php';

$sql = "CREATE TABLE IF NOT EXISTS orientation_checklist (
    id INT(11) NOT NULL AUTO_INCREMENT,
    student_name VARCHAR(255) NOT NULL,
    index_number VARCHAR(100) NOT NULL,
    host_institution VARCHAR(255) DEFAULT NULL,
    general_staff_introduction TINYINT(1) NOT NULL DEFAULT 0,
    general_facilities_location TINYINT(1) NOT NULL DEFAULT 0,
    general_tea_coffee_lunch TINYINT(1) NOT NULL DEFAULT 0,
    general_transport_arrangements TINYINT(1) NOT NULL DEFAULT 0,
    general_dress_code TINYINT(1) NOT NULL DEFAULT 0,
    general_code_of_conduct TINYINT(1) NOT NULL DEFAULT 0,
    general_policies_regulations TINYINT(1) NOT NULL DEFAULT 0,
    work_workspace TINYINT(1) NOT NULL DEFAULT 0,
    work_duty_arrangements TINYINT(1) NOT NULL DEFAULT 0,
    work_schedule_meetings TINYINT(1) NOT NULL DEFAULT 0,
    work_first_meeting_supervisor TINYINT(1) NOT NULL DEFAULT 0,
    health_emergency_procedures TINYINT(1) NOT NULL DEFAULT 0,
    health_safety_policy TINYINT(1) NOT NULL DEFAULT 0,
    health_first_aid_arrangements TINYINT(1) NOT NULL DEFAULT 0,
    health_fire_procedures TINYINT(1) NOT NULL DEFAULT 0,
    health_accident_reporting TINYINT(1) NOT NULL DEFAULT 0,
    health_manual_handling TINYINT(1) NOT NULL DEFAULT 0,
    health_safety_regulations TINYINT(1) NOT NULL DEFAULT 0,
    health_equipment_instruction TINYINT(1) NOT NULL DEFAULT 0,
    others_student_info_form TINYINT(1) NOT NULL DEFAULT 0,
    others_social_media_guidelines TINYINT(1) NOT NULL DEFAULT 0,
    others_it_systems_equipment TINYINT(1) NOT NULL DEFAULT 0,
    student_signature VARCHAR(255) DEFAULT NULL,
    student_signature_date DATE DEFAULT NULL,
    host_supervisor_signature VARCHAR(255) DEFAULT NULL,
    host_supervisor_date DATE DEFAULT NULL,
    wrl_coordinator_signature VARCHAR(255) DEFAULT NULL,
    wrl_coordinator_date DATE DEFAULT NULL,
    completed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY index_number (index_number)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";

if (mysqli_query($conn, $sql)) {
    echo "<h2>✅ SUCCESS: orientation_checklist table created!</h2>";
    echo "<p>The orientation checklist system is now ready to use.</p>";
    echo "<p><a href='orientation_checklist.php'>Go to Orientation Checklist</a></p>";
    echo "<p><a href='admin/view_orientation_checklists.php'>Go to Admin View Checklists</a></p>";
    echo "<p><strong>Note:</strong> You can now delete this setup file after confirming the table was created.</p>";
} else {
    echo "<h2>❌ ERROR: Failed to create table</h2>";
    echo "<p>Error: " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
?>