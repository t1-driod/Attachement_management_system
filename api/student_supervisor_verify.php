<?php
// POST: verify supervisor password (for visiting or company assessment). Requires student session.
// Uses visiting_lecturers.visiting_assessment_password / company_assessment_password and checks
// that the current student is assigned to that institutional supervisor.
require_once __DIR__ . '/supervisor_shared.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    http_response_code(405);
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    http_response_code(401);
    exit;
}

$index_number = $_SESSION['index_number'] ?? '';
if ($index_number === '') {
    echo json_encode(['success' => false, 'error' => 'Session invalid']);
    exit;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true) ?: [];
$type = $body['type'] ?? '';
$password = trim($body['password'] ?? '');

if ($type !== 'visiting' && $type !== 'company') {
    echo json_encode(['success' => false, 'error' => 'Invalid type']);
    exit;
}

if ($password === '') {
    echo json_encode(['success' => false, 'error' => 'Password required']);
    exit;
}

$pwd = mysqli_real_escape_string($conn, $password);
$col = $type === 'visiting' ? 'visiting_assessment_password' : 'company_assessment_password';

// Check if columns exist
$cr = @mysqli_query($conn, "SHOW COLUMNS FROM visiting_lecturers LIKE '$col'");
if (!$cr || mysqli_num_rows($cr) === 0) {
    // Fallback to legacy supervisors_login (single shared password)
    $q = "SELECT id FROM supervisors_login WHERE password='$pwd' LIMIT 1";
    $r = mysqli_query($conn, $q);
    if (!$r || mysqli_num_rows($r) !== 1) {
        echo json_encode(['success' => false, 'error' => 'Invalid password']);
        exit;
    }
    echo json_encode(['success' => true]);
    exit;
}

$q = "SELECT id, lecturer_name FROM visiting_lecturers WHERE BINARY `$col` = '$pwd' LIMIT 1";
$r = mysqli_query($conn, $q);
if (!$r || mysqli_num_rows($r) !== 1) {
    echo json_encode(['success' => false, 'error' => 'Invalid password']);
    exit;
}

$row = mysqli_fetch_assoc($r);
$lecturer_name = $row['lecturer_name'] ?? '';
if ($lecturer_name === '') {
    echo json_encode(['success' => false, 'error' => 'Invalid password']);
    exit;
}

if (!iasms_is_student_assigned_to_lecturer($conn, $index_number, $lecturer_name)) {
    echo json_encode(['success' => false, 'error' => 'This password is not valid for this student.']);
    exit;
}

echo json_encode(['success' => true]);
exit;
