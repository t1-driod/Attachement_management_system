<?php
$index_number = isset($segments[1]) ? urldecode($segments[1]) : '';
if ($index_number === '') {
    echo json_encode(['error' => 'Index number required']);
    http_response_code(400);
    return;
}
$idx = mysqli_real_escape_string($conn, $index_number);

$q = "SELECT id, student_name, index_number, week_number, monday_job_assigned, monday_skill_acquired,
      tuesday_job_assigned, tuesday_skill_acquired, wednesday_job_assigned, wednesday_skill_acquired,
      thursday_job_assigned, thursday_skill_acquired, friday_job_assigned, friday_skill_acquired,
      created_at, updated_at
      FROM elogbook_entries WHERE index_number='$idx' ORDER BY week_number ASC";
$res = mysqli_query($conn, $q);
$entries = [];
while ($row = mysqli_fetch_assoc($res)) {
    $entries[] = [
        'id' => (int)$row['id'],
        'student_name' => $row['student_name'] ?? '',
        'index_number' => $row['index_number'] ?? '',
        'week_number' => (int)$row['week_number'],
        'monday_job_assigned' => $row['monday_job_assigned'] ?? '',
        'monday_skill_acquired' => $row['monday_skill_acquired'] ?? '',
        'tuesday_job_assigned' => $row['tuesday_job_assigned'] ?? '',
        'tuesday_skill_acquired' => $row['tuesday_skill_acquired'] ?? '',
        'wednesday_job_assigned' => $row['wednesday_job_assigned'] ?? '',
        'wednesday_skill_acquired' => $row['wednesday_skill_acquired'] ?? '',
        'thursday_job_assigned' => $row['thursday_job_assigned'] ?? '',
        'thursday_skill_acquired' => $row['thursday_skill_acquired'] ?? '',
        'friday_job_assigned' => $row['friday_job_assigned'] ?? '',
        'friday_skill_acquired' => $row['friday_skill_acquired'] ?? '',
        'created_at' => $row['created_at'] ?? null,
        'updated_at' => $row['updated_at'] ?? null,
    ];
}
echo json_encode(['index_number' => $index_number, 'entries' => $entries]);
