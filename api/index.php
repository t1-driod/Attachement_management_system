<?php
/**
 * IASMS REST API entry point.
 * All requests to /iasms/api/* are routed here. Expects JSON for POST; returns JSON.
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

session_start();

require __DIR__ . '/../database_connection/database_connection.php';

$uri = $_SERVER['REQUEST_URI'] ?? '';
$base = '/iasms/api';
$path = (strpos($uri, $base) === 0) ? trim(substr($uri, strlen($base)), '/?') : '';
$path = preg_replace('/\?.*/', '', $path);
$segments = $path ? explode('/', $path) : [];

// Route: auth
if (!empty($segments[0]) && $segments[0] === 'auth') {
    $action = $segments[1] ?? '';
    if ($action === 'login') {
        require __DIR__ . '/auth_login.php';
        exit;
    }
    if ($action === 'logout') {
        require __DIR__ . '/auth_logout.php';
        exit;
    }
    if ($action === 'check') {
        require __DIR__ . '/auth_check.php';
        exit;
    }
    if ($action === 'register') {
        require __DIR__ . '/auth_register.php';
        exit;
    }
    if ($action === 'supervisor-register') {
        require __DIR__ . '/auth_supervisor_register.php';
        exit;
    }
}

// Route: admin dashboard stats
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'stats') {
    require __DIR__ . '/admin_stats.php';
    exit;
}

// Route: admin students (industrial_registration)
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'students') {
    require __DIR__ . '/admin_students.php';
    exit;
}

// Route: admin orientation checklists
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'orientation') {
    require __DIR__ . '/admin_orientation.php';
    exit;
}

// Route: admin orientation checklist detail
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'orientation-detail') {
    require __DIR__ . '/admin_orientation_detail.php';
    exit;
}

// Route: admin elogbooks
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'elogbooks') {
    require __DIR__ . '/admin_elogbooks.php';
    exit;
}

// Route: admin contracts
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'contracts') {
    require __DIR__ . '/admin_contracts.php';
    exit;
}

// Route: admin reports (file list)
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'reports') {
    require __DIR__ . '/admin_reports.php';
    exit;
}

// Route: admin assumptions
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'assumptions') {
    require __DIR__ . '/admin_assumptions.php';
    exit;
}

// Route: admin visiting scores
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'visiting-scores') {
    require __DIR__ . '/admin_visiting_scores.php';
    exit;
}

// Route: admin company scores
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'company-scores') {
    require __DIR__ . '/admin_company_scores.php';
    exit;
}

// Route: admin assign supervisors (GET data)
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'assign-supervisors' && ($segments[2] ?? '') === '') {
    require __DIR__ . '/admin_assign_supervisors_data.php';
    exit;
}

// Route: admin assign supervisors - add lecturer (POST)
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'assign-supervisors' && ($segments[2] ?? '') === 'lecturer') {
    require __DIR__ . '/admin_assign_supervisors_lecturer.php';
    exit;
}

// Route: admin assign supervisors - save assignments (POST)
if (!empty($segments[0]) && $segments[0] === 'admin' && ($segments[1] ?? '') === 'assign-supervisors' && ($segments[2] ?? '') === 'save') {
    require __DIR__ . '/admin_assign_supervisors_save.php';
    exit;
}

// Route: supervisor dashboard stats
if (!empty($segments[0]) && $segments[0] === 'supervisor' && ($segments[1] ?? '') === 'stats') {
    require __DIR__ . '/supervisor_stats.php';
    exit;
}

// Route: supervisor students (for view logbook list)
if (!empty($segments[0]) && $segments[0] === 'supervisor' && ($segments[1] ?? '') === 'students') {
    require __DIR__ . '/supervisor_students.php';
    exit;
}

// Route: supervisor orientation checklists (assigned students only)
if (!empty($segments[0]) && $segments[0] === 'supervisor' && ($segments[1] ?? '') === 'orientation') {
    require __DIR__ . '/supervisor_orientation.php';
    exit;
}

// Route: supervisor elogbooks (assigned students only)
if (!empty($segments[0]) && $segments[0] === 'supervisor' && ($segments[1] ?? '') === 'elogbooks') {
    require __DIR__ . '/supervisor_elogbooks.php';
    exit;
}

// Route: supervisor student assumptions (assigned students only)
if (!empty($segments[0]) && $segments[0] === 'supervisor' && ($segments[1] ?? '') === 'assumptions') {
    require __DIR__ . '/supervisor_assumptions.php';
    exit;
}

// Route: supervisor contracts (assigned students only)
if (!empty($segments[0]) && $segments[0] === 'supervisor' && ($segments[1] ?? '') === 'contracts') {
    require __DIR__ . '/supervisor_contracts.php';
    exit;
}

// Route: supervisor submitted reports (assigned students only)
if (!empty($segments[0]) && $segments[0] === 'supervisor' && ($segments[1] ?? '') === 'reports') {
    require __DIR__ . '/supervisor_reports.php';
    exit;
}

// Route: supervisor grade submission (visiting supervisor scores)
if (!empty($segments[0]) && $segments[0] === 'supervisor' && ($segments[1] ?? '') === 'grade') {
    require __DIR__ . '/supervisor_grade.php';
    exit;
}

// Route: elogbook entries for a student
if (!empty($segments[0]) && $segments[0] === 'elogbook' && !empty($segments[1])) {
    require __DIR__ . '/elogbook_student.php';
    exit;
}

// Route: student orientation checklist (GET/POST, requires student session)
if (!empty($segments[0]) && $segments[0] === 'student' && ($segments[1] ?? '') === 'orientation') {
    require __DIR__ . '/student_orientation.php';
    exit;
}

// Route: student e-logbook submit (POST, requires student session)
if (!empty($segments[0]) && $segments[0] === 'student' && ($segments[1] ?? '') === 'elogbook') {
    require __DIR__ . '/student_elogbook.php';
    exit;
}

// Route: student grades (GET, requires student session)
if (!empty($segments[0]) && $segments[0] === 'student' && ($segments[1] ?? '') === 'grades') {
    require __DIR__ . '/student_grades.php';
    exit;
}

// Route: student supervisor verify (POST)
if (!empty($segments[0]) && $segments[0] === 'student' && ($segments[1] ?? '') === 'supervisor' && ($segments[2] ?? '') === 'verify') {
    require __DIR__ . '/student_supervisor_verify.php';
    exit;
}

// Route: student supervisor grade submit (POST)
if (!empty($segments[0]) && $segments[0] === 'student' && ($segments[1] ?? '') === 'supervisor' && ($segments[2] ?? '') === 'grade') {
    require __DIR__ . '/student_supervisor_grade.php';
    exit;
}

echo json_encode(['error' => 'Not found']);
http_response_code(404);
