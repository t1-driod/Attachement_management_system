<?php
// POST: approve or reject contract
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true) ?: [];
    $action = $body['action'] ?? '';
    $contract_id = (int)($body['contract_id'] ?? 0);
    $admin_comment = isset($body['admin_comment']) ? mysqli_real_escape_string($conn, $body['admin_comment']) : '';
    if ($contract_id < 1 || !in_array($action, ['approve', 'reject'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid action or contract id']);
        return;
    }
    $status = $action === 'approve' ? 'approved' : 'rejected';
    $q = "UPDATE student_contracts SET status='$status', admin_comment='$admin_comment' WHERE id=$contract_id";
    if (mysqli_query($conn, $q)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Update failed']);
    }
    return;
}

$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$where = '1=1';
if ($status !== '') $where .= " AND status = '$status'";

$q = "SELECT id, student_name, index_number, original_filename, status, submission_date, admin_comment FROM student_contracts WHERE $where ORDER BY submission_date DESC LIMIT 200";
$res = mysqli_query($conn, $q);
$list = [];
while ($row = mysqli_fetch_assoc($res)) {
    $list[] = [
        'id' => (int)$row['id'],
        'student_name' => $row['student_name'] ?? '',
        'index_number' => $row['index_number'] ?? '',
        'original_filename' => $row['original_filename'] ?? '',
        'status' => $row['status'] ?? 'pending',
        'submission_date' => $row['submission_date'] ?? null,
        'admin_comment' => $row['admin_comment'] ?? '',
    ];
}
echo json_encode($list);
