<?php
$stats = [
    'registeredStudents' => 0,
    'orientationChecklists' => 0,
    'elogbooksSubmitted' => 0,
    'contractsPending' => 0,
    'contractsApproved' => 0,
    'reportsSubmitted' => 0,
    'assumptionsCount' => 0,
    'visitingScoresCount' => 0,
    'companyScoresCount' => 0,
];

$r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM industrial_registration");
if ($r && $row = mysqli_fetch_assoc($r)) $stats['registeredStudents'] = (int)$row['c'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM orientation_checklist");
if ($r && $row = mysqli_fetch_assoc($r)) $stats['orientationChecklists'] = (int)$row['c'];

$r = mysqli_query($conn, "SELECT COUNT(DISTINCT index_number) AS c FROM elogbook_entries");
if ($r && $row = mysqli_fetch_assoc($r)) $stats['elogbooksSubmitted'] = (int)$row['c'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM student_contracts WHERE status = 'pending'");
if ($r && $row = mysqli_fetch_assoc($r)) $stats['contractsPending'] = (int)$row['c'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM student_contracts WHERE status = 'approved'");
if ($r && $row = mysqli_fetch_assoc($r)) $stats['contractsApproved'] = (int)$row['c'];

$uploads_dir = dirname(__DIR__) . '/submit_report/uploads';
$reports_count = 0;
if (is_dir($uploads_dir)) {
    $files = array_diff(scandir($uploads_dir), ['.', '..']);
    foreach ($files as $f) {
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, ['doc', 'docx', 'pdf'])) $reports_count++;
    }
}
$stats['reportsSubmitted'] = $reports_count;

$r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM students_assumption");
if ($r && $row = mysqli_fetch_assoc($r)) $stats['assumptionsCount'] = (int)$row['c'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM visiting_supervisor_grade");
if ($r && $row = mysqli_fetch_assoc($r)) $stats['visitingScoresCount'] = (int)$row['c'];

$r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM company_supervisor_grade");
if ($r && $row = mysqli_fetch_assoc($r)) $stats['companyScoresCount'] = (int)$row['c'];

echo json_encode($stats);
