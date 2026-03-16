<?php
/**
 * GET: Serve a student's profile photo. Supervisor only; student must be assigned.
 * Route: /supervisor/student-profile/{index_number}/photo
 * $segments is set by index.php.
 */
require_once __DIR__ . '/supervisor_helpers.php';

if (($_SESSION['role'] ?? '') !== 'supervisor') {
    http_response_code(401);
    return;
}
$index_number = isset($segments[2]) ? trim(urldecode($segments[2])) : '';
if ($index_number === '') {
    http_response_code(400);
    return;
}
$assigned = iasms_get_assigned_indexes_for_current_supervisor($conn);
if (!in_array($index_number, $assigned, true)) {
    http_response_code(404);
    return;
}

$base_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'student_profiles';
$safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $index_number);
$extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$path = null;
$ctype = 'image/jpeg';

$idx = mysqli_real_escape_string($conn, $index_number);
$row = @mysqli_fetch_assoc(@mysqli_query($conn, "SELECT filename, content_type FROM student_profile_photos WHERE index_number='$idx' LIMIT 1"));
if ($row && !empty($row['filename'])) {
    $p = $base_dir . DIRECTORY_SEPARATOR . $row['filename'];
    if (file_exists($p) && is_file($p)) {
        $path = $p;
        $ctype = !empty($row['content_type']) ? $row['content_type'] : 'image/jpeg';
    }
}
if ($path === null) {
    foreach ($extensions as $ext) {
        $p = $base_dir . DIRECTORY_SEPARATOR . $safe . '.' . $ext;
        if (file_exists($p) && is_file($p)) {
            $path = $p;
            $ctype = $ext === 'png' ? 'image/png' : ($ext === 'gif' ? 'image/gif' : ($ext === 'webp' ? 'image/webp' : 'image/jpeg'));
            break;
        }
    }
}
if ($path === null) {
    http_response_code(404);
    return;
}

header('Content-Type: ' . $ctype);
header('Cache-Control: private, max-age=300');
readfile($path);
