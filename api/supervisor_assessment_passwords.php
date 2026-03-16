<?php
/**
 * GET: whether visiting/company assessment passwords are set.
 * POST: set visiting or company assessment password (body: type, password).
 * Uses signed cookie (set on supervisor login) to identify which row to use, so the correct
 * supervisor's data is always shown/updated even if the PHP session is shared.
 */
if (($_SESSION['role'] ?? '') !== 'supervisor') {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    return;
}

require_once __DIR__ . '/supervisor_staff_cookie.php';

// Cookie (set on login) identifies which supervisor's row to use; fall back to session
$staff_id = iasms_get_supervisor_staff_id_from_cookie();
if ($staff_id === '') {
    $staff_id = trim((string)($_SESSION['staff_id'] ?? ''));
}
if ($staff_id === '') {
    http_response_code(401);
    echo json_encode(['error' => 'Session invalid. Please log out and log in again with your Staff ID.']);
    return;
}

$staff_id_esc = mysqli_real_escape_string($conn, $staff_id);

// Resolve supervisor row only by staff_id so each supervisor updates their own row
$r = mysqli_query($conn, "SELECT id, staff_id FROM visiting_lecturers WHERE BINARY staff_id = '$staff_id_esc' LIMIT 1");
if (!$r || mysqli_num_rows($r) !== 1) {
    http_response_code(404);
    echo json_encode(['error' => 'Your account was not found (Staff ID: ' . $staff_id . '). Please contact admin.']);
    return;
}
$sup_row = mysqli_fetch_assoc($r);
$supervisor_id = (string)$sup_row['id'];
$sid_esc = mysqli_real_escape_string($conn, $supervisor_id);

// Ensure columns exist (optional migration)
$cr = @mysqli_query($conn, "SHOW COLUMNS FROM visiting_lecturers LIKE 'visiting_assessment_password'");
if ($cr && mysqli_num_rows($cr) === 0) {
    @mysqli_query($conn, "ALTER TABLE visiting_lecturers ADD COLUMN visiting_assessment_password VARCHAR(255) NULL DEFAULT NULL");
}
$cr = @mysqli_query($conn, "SHOW COLUMNS FROM visiting_lecturers LIKE 'company_assessment_password'");
if ($cr && mysqli_num_rows($cr) === 0) {
    @mysqli_query($conn, "ALTER TABLE visiting_lecturers ADD COLUMN company_assessment_password VARCHAR(255) NULL DEFAULT NULL");
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = "SELECT visiting_assessment_password, company_assessment_password FROM visiting_lecturers WHERE id = '$sid_esc' LIMIT 1";
    $res = mysqli_query($conn, $q);
    if (!$res || mysqli_num_rows($res) !== 1) {
        echo json_encode(['has_visiting' => false, 'has_company' => false]);
        return;
    }
    $row = mysqli_fetch_assoc($res);
    $v = $row['visiting_assessment_password'] ?? '';
    $c = $row['company_assessment_password'] ?? '';
    echo json_encode([
        'has_visiting' => $v !== '' && $v !== null,
        'has_company' => $c !== '' && $c !== null,
        'visiting_password' => $v !== '' && $v !== null ? $v : null,
        'company_password' => $c !== '' && $c !== null ? $c : null,
    ]);
    return;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    return;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true) ?: [];
$type = trim((string)($body['type'] ?? ''));
$password = trim((string)($body['password'] ?? ''));

if ($type !== 'visiting' && $type !== 'company') {
    echo json_encode(['success' => false, 'error' => 'Type must be visiting or company']);
    return;
}

if ($password === '') {
    echo json_encode(['success' => false, 'error' => 'Password is required']);
    return;
}

$col = $type === 'visiting' ? 'visiting_assessment_password' : 'company_assessment_password';
$pwd_esc = mysqli_real_escape_string($conn, $password);

$sql = "UPDATE visiting_lecturers SET `$col` = '$pwd_esc' WHERE id = '$sid_esc' LIMIT 1";
if (!mysqli_query($conn, $sql)) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . mysqli_error($conn)]);
    return;
}

echo json_encode(['success' => true, 'message' => 'Password saved.']);
return;
