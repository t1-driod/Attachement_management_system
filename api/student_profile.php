<?php
/**
 * Student account profile: GET = profile data; POST = update name + optional photo upload.
 */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    http_response_code(401);
    return;
}

$index_number = $_SESSION['index_number'] ?? '';
if ($index_number === '') {
    echo json_encode(['error' => 'Session invalid']);
    http_response_code(401);
    return;
}

$idx = mysqli_real_escape_string($conn, $index_number);
$base_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'student_profiles';
$safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $index_number);
$extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

function hasProfilePhoto($conn, string $idx, string $base_dir, string $safe, array $extensions): bool {
    $idx_esc = mysqli_real_escape_string($conn, $idx);
    $res = @mysqli_query($conn, "SELECT 1 FROM student_profile_photos WHERE index_number='$idx_esc' LIMIT 1");
    if ($res && mysqli_fetch_assoc($res)) return true;
    foreach ($extensions as $ext) {
        if (file_exists($base_dir . DIRECTORY_SEPARATOR . $safe . '.' . $ext)) {
            return true;
        }
    }
    return false;
}

// GET: return profile
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = "SELECT first_name, last_name, index_number FROM registered_students WHERE index_number='$idx' LIMIT 1";
    $res = mysqli_query($conn, $q);
    if (!$res || mysqli_num_rows($res) !== 1) {
        echo json_encode(['error' => 'Student not found']);
        return;
    }
    $row = mysqli_fetch_assoc($res);
    echo json_encode([
        'first_name' => $row['first_name'] ?? '',
        'last_name' => $row['last_name'] ?? '',
        'index_number' => $row['index_number'] ?? '',
        'has_photo' => hasProfilePhoto($conn, $idx, $base_dir, $safe, $extensions),
    ]);
    return;
}

// POST: update profile (name + optional photo)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    return;
}

// Multipart form: ensure we have POST data (if body was too large, PHP leaves $_POST/$_FILES empty)
$content_length = isset($_SERVER['CONTENT_LENGTH']) ? (int)$_SERVER['CONTENT_LENGTH'] : 0;
if ($content_length > 0 && empty($_POST) && empty($_FILES)) {
    echo json_encode(['success' => false, 'error' => 'Request too large. Image must be under 5MB and total request within server limits.']);
    return;
}

$first_name = isset($_POST['first_name']) ? trim((string)$_POST['first_name']) : null;
$last_name = isset($_POST['last_name']) ? trim((string)$_POST['last_name']) : null;

if (!is_dir($base_dir)) {
    if (!@mkdir($base_dir, 0755, true)) {
        echo json_encode(['success' => false, 'error' => 'Server could not create upload directory.']);
        return;
    }
}
if (!is_writable($base_dir)) {
    echo json_encode(['success' => false, 'error' => 'Upload directory is not writable.']);
    return;
}

// Upload photo if provided
if (isset($_FILES['photo'])) {
    $err = (int) $_FILES['photo']['error'];
    if ($err !== UPLOAD_ERR_OK) {
        if ($err === UPLOAD_ERR_NO_FILE) {
            echo json_encode(['success' => false, 'error' => 'No file received. Try a smaller image, or restart the dev server if using Vite proxy.']);
            return;
        }
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'Image exceeds server limit.',
            UPLOAD_ERR_FORM_SIZE => 'Image must be under 5MB.',
            UPLOAD_ERR_PARTIAL => 'Upload was interrupted. Please try again.',
            UPLOAD_ERR_NO_FILE => 'No file was selected.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server upload folder missing.',
            UPLOAD_ERR_CANT_WRITE => 'Server could not save the file.',
            UPLOAD_ERR_EXTENSION => 'Upload blocked by server.',
        ];
        $msg = $messages[$err] ?? 'Upload failed (error ' . $err . ').';
        echo json_encode(['success' => false, 'error' => $msg]);
        return;
    }
    $file = $_FILES['photo'];
    $tmp = $file['tmp_name'];
    $name = $file['name'];
    if (!is_uploaded_file($tmp)) {
        echo json_encode(['success' => false, 'error' => 'Invalid upload.']);
        return;
    }
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, $extensions, true)) {
        echo json_encode(['success' => false, 'error' => 'Allowed formats: jpg, jpeg, png, gif, webp']);
        return;
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => 'Image must be under 5MB']);
        return;
    }
    $dest = $base_dir . DIRECTORY_SEPARATOR . $safe . '.' . $ext;
    // Remove any existing photo for this student
    foreach ($extensions as $e) {
        $old = $base_dir . DIRECTORY_SEPARATOR . $safe . '.' . $e;
        if (file_exists($old) && is_file($old)) {
            @unlink($old);
        }
    }
    if (!move_uploaded_file($tmp, $dest)) {
        echo json_encode(['success' => false, 'error' => 'Failed to save photo. Check server write permissions for uploads/student_profiles.']);
        return;
    }
    $filename = $safe . '.' . $ext;
    $content_type = $ext === 'png' ? 'image/png' : ($ext === 'gif' ? 'image/gif' : ($ext === 'webp' ? 'image/webp' : 'image/jpeg'));
    $fn_esc = mysqli_real_escape_string($conn, $filename);
    $ct_esc = mysqli_real_escape_string($conn, $content_type);
    $upsert = "INSERT INTO student_profile_photos (index_number, filename, content_type) VALUES ('$idx', '$fn_esc', '$ct_esc')
               ON DUPLICATE KEY UPDATE filename = '$fn_esc', content_type = '$ct_esc', updated_at = CURRENT_TIMESTAMP";
    @mysqli_query($conn, $upsert);
    // Table may not exist yet; photo is still on disk and will be served by file fallback
}

// Update name if provided
if ($first_name !== null || $last_name !== null) {
    $set = [];
    if ($first_name !== null) $set[] = "first_name = '" . mysqli_real_escape_string($conn, $first_name) . "'";
    if ($last_name !== null) $set[] = "last_name = '" . mysqli_real_escape_string($conn, $last_name) . "'";
    if (!empty($set)) {
        $sql = "UPDATE registered_students SET " . implode(', ', $set) . " WHERE index_number = '$idx'";
        if (!mysqli_query($conn, $sql)) {
            echo json_encode(['success' => false, 'error' => 'Failed to update name']);
            return;
        }
        $q = "SELECT first_name, last_name FROM registered_students WHERE index_number='$idx' LIMIT 1";
        $r = mysqli_query($conn, $q);
        if ($r && mysqli_num_rows($r) === 1) {
            $row = mysqli_fetch_assoc($r);
            $_SESSION['name'] = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
        }
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Profile updated.',
    'has_photo' => hasProfilePhoto($conn, $idx, $base_dir, $safe, $extensions),
]);
