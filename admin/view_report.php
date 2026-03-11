<?php
/**
 * Serves a submitted report file for viewing in the browser (inline).
 * PDFs display in-browser; Word files may open in browser or default app.
 */

$uploads_dir = dirname(__DIR__) . '/submit_report/uploads';
$allowed_extensions = array('doc', 'docx', 'pdf');

if (!isset($_GET['file']) || $_GET['file'] === '') {
    header('HTTP/1.0 400 Bad Request');
    exit('No file specified.');
}

$requested = $_GET['file'];
$filename = basename($requested);
$path = $uploads_dir . DIRECTORY_SEPARATOR . $filename;

$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
if (!in_array($ext, $allowed_extensions)) {
    header('HTTP/1.0 403 Forbidden');
    exit('File type not allowed.');
}

if (!is_file($path) || !is_readable($path)) {
    header('HTTP/1.0 404 Not Found');
    exit('File not found.');
}

$mime = array(
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'pdf'  => 'application/pdf',
);
$content_type = isset($mime[$ext]) ? $mime[$ext] : 'application/octet-stream';

header('Content-Type: ' . $content_type);
header('Content-Disposition: inline; filename="' . basename($filename) . '"');
header('Content-Length: ' . filesize($path));
header('Cache-Control: no-cache, must-revalidate');
readfile($path);
exit;
