<?php
/**
 * GET /api/admin/contracts/download?id=123
 * Streams the contract PDF for admin from student_contracts. No JSON; sends file headers and body.
 */
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid contract id']);
    exit;
}

$q = "SELECT contract_file, original_filename FROM student_contracts WHERE id=$id LIMIT 1";
$res = mysqli_query($conn, $q);
$row = $res ? mysqli_fetch_assoc($res) : null;
if (!$row || empty($row['contract_file'])) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Contract not found']);
    exit;
}

$contract_file = $row['contract_file'];
$base = dirname(__DIR__);
$path = $base . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $contract_file);

if (!is_file($path)) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'File not found']);
    exit;
}

$filename = !empty($row['original_filename']) ? $row['original_filename'] : basename($path);
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . str_replace('"', '\\"', $filename) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
