<?php
// Assigned students for the currently logged-in institutional supervisor.
// Uses the same selection logic as the legacy institutional_supervisor/dashboard.php.

if (($_SESSION['role'] ?? '') !== 'supervisor') {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    return;
}

require_once __DIR__ . '/supervisor_shared.php';

$supervisorId = (string)($_SESSION['user_id'] ?? '');
$supervisorName = (string)($_SESSION['name'] ?? '');

[$students, ] = iasms_get_supervisor_students_and_summary($conn, $supervisorId, $supervisorName);

// Ensure the payload matches the StudentSummary type expected by the frontend.
$list = [];
foreach ($students as $row) {
    $list[] = [
        'student_index' => $row['student_index'] ?? '',
        'first_name' => $row['first_name'] ?? '',
        'last_name' => $row['last_name'] ?? '',
        'company_name' => $row['company_name'] ?? '',
        'company_region' => $row['company_region'] ?? '',
        'attachment_region' => $row['attachment_region'] ?? '',
        'visit_number' => (int)($row['visit_number'] ?? 1),
    ];
}

echo json_encode($list);
