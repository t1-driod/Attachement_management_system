<?php
include 'database_connection/database_connection.php';

// Create the new elogbook_entries table
$sql = "CREATE TABLE IF NOT EXISTS elogbook_entries (
    id INT(11) NOT NULL AUTO_INCREMENT,
    student_name VARCHAR(255) NOT NULL,
    index_number VARCHAR(100) NOT NULL,
    week_number INT(11) NOT NULL,
    monday_job_assigned LONGTEXT,
    monday_skill_acquired MEDIUMTEXT,
    tuesday_job_assigned MEDIUMTEXT,
    tuesday_skill_acquired MEDIUMTEXT,
    wednesday_job_assigned MEDIUMTEXT,
    wednesday_skill_acquired MEDIUMTEXT,
    thursday_job_assigned MEDIUMTEXT,
    thursday_skill_acquired MEDIUMTEXT,
    friday_job_assigned MEDIUMTEXT,
    friday_skill_acquired MEDIUMTEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY index_number (index_number),
    KEY week_number (week_number),
    KEY student_week (index_number, week_number)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";

if (mysqli_query($conn, $sql)) {
    echo "<h2>✅ SUCCESS: elogbook_entries table created!</h2>";

    // Migrate data from old week tables
    echo "<h3>Migrating existing data...</h3>";

    for ($week = 1; $week <= 10; $week++) {
        $table_name = "week{$week}_table";

        // Check if old table exists
        $check_table = mysqli_query($conn, "SHOW TABLES LIKE '$table_name'");
        if (mysqli_num_rows($check_table) > 0) {
            // Get data from old table
            $select_query = "SELECT * FROM $table_name";
            $result = mysqli_query($conn, $select_query);

            if (mysqli_num_rows($result) > 0) {
                echo "<p>Migrating Week $week data...</p>";

                while ($row = mysqli_fetch_assoc($result)) {
                    // Check if this data already exists in new table
                    $check_exists = "SELECT id FROM elogbook_entries
                                   WHERE index_number='{$row['index_number']}'
                                   AND week_number='$week'";
                    $exists_result = mysqli_query($conn, $check_exists);

                    if (mysqli_num_rows($exists_result) == 0) {
                        // Insert into new table
                        $insert_query = "INSERT INTO elogbook_entries
                            (student_name, index_number, week_number,
                             monday_job_assigned, monday_skill_acquired,
                             tuesday_job_assigned, tuesday_skill_acquired,
                             wednesday_job_assigned, wednesday_skill_acquired,
                             thursday_job_assigned, thursday_skill_acquired,
                             friday_job_assigned, friday_skill_acquired,
                             created_at, updated_at)
                            VALUES
                            ('{$row['username']}', '{$row['index_number']}', '$week',
                             '{$row['monday_job_assigned']}', '{$row['monday_special_skill_acquired']}',
                             '{$row['tuesday_job_assigned']}', '{$row['tuesday_special_skill_acquired']}',
                             '{$row['wednesday_job_assigned']}', '{$row['wednesday_special_skill_acquired']}',
                             '{$row['thursday_job_assigned']}', '{$row['thursday_special_skill_acquired']}',
                             '{$row['friday_job_assigned']}', '{$row['friday_special_skill_acquired']}',
                             '{$row['date']}', '{$row['date']}')";

                        if (mysqli_query($conn, $insert_query)) {
                            echo "<span style='color: green;'>✓ Week $week data migrated for {$row['index_number']}</span><br>";
                        } else {
                            echo "<span style='color: red;'>✗ Error migrating Week $week for {$row['index_number']}: " . mysqli_error($conn) . "</span><br>";
                        }
                    } else {
                        echo "<span style='color: blue;'>- Week $week data already exists for {$row['index_number']}</span><br>";
                    }
                }
            } else {
                echo "<p>No data found in Week $week table</p>";
            }
        } else {
            echo "<p>Week $week table does not exist</p>";
        }
    }

    echo "<h2>✅ Migration completed!</h2>";
    echo "<p>The dynamic e-logbook system is now ready to use.</p>";
    echo "<p><a href='e-logbook/elogbook_dynamic.php'>Go to Dynamic E-LogBook</a></p>";
    echo "<p><strong>Note:</strong> You can now delete this setup file after confirming the migration worked.</p>";

} else {
    echo "<h2>❌ ERROR: Failed to create table</h2>";
    echo "<p>Error: " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
?>