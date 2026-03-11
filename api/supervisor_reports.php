<?php
require_once __DIR__ . '/supervisor_helpers.php';

if (($_SESSION['role'] ?? '') !== 'supervisor') {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    return;
}

$assigned = iasms_get_assigned_indexes_for_current_supervisor($conn);
if (empty($assigned)) {
    echo json_encode([]);
    return;
}

// Reports are stored as files named by index_number (per legacy instructions).
// Filter files whose base filename matches one of the assigned index numbers.
$assignedMap = [];
foreach ($assigned as $idx) {
    $assignedMap[$idx] = true;
}

$uploads_dir = dirname(__DIR__) . '/submit_report/uploads';
$list = [];
if (is_dir($uploads_dir)) {
    $files = array_diff(scandir($uploads_dir), ['.', '..']);
    foreach ($files as $f) {
        $path = $uploads_dir . DIRECTORY_SEPARATOR . $f;
        if (!is_file($path)) {
            continue;
        }
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (!in_array($ext, ['doc', 'docx', 'pdf'], true)) {
            continue;
        }
        $basename = pathinfo($f, PATHINFO_FILENAME);
        if (!isset($assignedMap[$basename])) {
            continue;
        }
        $list[] = [
            'name' => $f,
            'size' => filesize($path),
            'modified' => date('Y-m-d H:i:s', filemtime($path)),
            'index_number' => $basename,
        ];
    }
    usort($list, static function (array $a, array $b): int {
        return strcmp($b['modified'], $a['modified']);
    });
}

echo json_encode($list);

