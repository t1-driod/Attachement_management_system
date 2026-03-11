<?php
// POST: verify supervisor password (for visiting or company assessment). Requires student session.
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
$q = "SELECT id FROM supervisors_login WHERE password='$pwd' LIMIT 1";
$r = mysqli_query($conn, $q);
if (!$r || mysqli_num_rows($r) !== 1) {
    echo json_encode(['success' => false, 'error' => 'Invalid password']);
    exit;
}

echo json_encode(['success' => true]);
exit;
