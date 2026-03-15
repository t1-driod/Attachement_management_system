<?php
/**
 * GET: Serve the current student's profile photo. No JSON; outputs image binary.
 * Reads filename and content_type from student_profile_photos table if present; else falls back to file scan.
 */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    return;
}
$index_number = $_SESSION['index_number'] ?? '';
if ($index_number === '') {
    http_response_code(401);
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
