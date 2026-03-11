<?php
// Supervisor dashboard stats for the currently logged-in institutional supervisor.
// Mirrors the legacy institutional_supervisor/dashboard.php logic but returns JSON
// in the shape expected by the React dashboard (SupervisorDashboardStats).

if (($_SESSION['role'] ?? '') !== 'supervisor') {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    return;
}

require_once __DIR__ . '/supervisor_shared.php';

$supervisorId = (string)($_SESSION['user_id'] ?? '');
$supervisorName = (string)($_SESSION['name'] ?? '');

[, $summary] = iasms_get_supervisor_students_and_summary($conn, $supervisorId, $supervisorName);

// Map legacy summary keys to frontend DTO keys
$stats = [
    'totalStudents' => (int)($summary['total_students'] ?? 0),
    'firstVisits' => (int)($summary['first_visit'] ?? 0),
    'secondVisits' => (int)($summary['second_visit'] ?? 0),
    'firstVisitWithScoresheet' => (int)($summary['first_visit_with_scoresheet'] ?? 0),
    'secondVisitWithScoresheet' => (int)($summary['second_visit_with_scoresheet'] ?? 0),
];

echo json_encode($stats);
