<?php
$uploads_dir = dirname(__DIR__) . '/submit_report/uploads';
$list = [];
if (is_dir($uploads_dir)) {
    $files = array_diff(scandir($uploads_dir), ['.', '..']);
    foreach ($files as $f) {
        $path = $uploads_dir . DIRECTORY_SEPARATOR . $f;
        if (is_file($path)) {
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            if (in_array($ext, ['doc', 'docx', 'pdf'])) {
                $list[] = [
                    'name' => $f,
                    'size' => filesize($path),
                    'modified' => date('Y-m-d H:i:s', filemtime($path)),
                ];
            }
        }
    }
    usort($list, function ($a, $b) {
        return strcmp($b['modified'], $a['modified']);
    });
}
echo json_encode($list);
