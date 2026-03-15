<?php
/**
 * Admin contracts: list and approve/reject. All data from student_contracts table only.
 */
// POST: approve or reject contract (same as HTML admin: form POST with contract_id + action)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prefer $_POST (form body); fallback to $_GET when body is not forwarded (e.g. dev proxy)
    $action = isset($_POST['action']) ? trim(strtolower((string)$_POST['action'])) : (isset($_GET['action']) ? trim(strtolower((string)$_GET['action'])) : '');
    $contract_id = isset($_POST['contract_id']) ? (int)$_POST['contract_id'] : (isset($_GET['contract_id']) ? (int)$_GET['contract_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0));
    $admin_comment = isset($_POST['admin_comment']) ? mysqli_real_escape_string($conn, (string)$_POST['admin_comment']) : '';

    if ($contract_id < 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid or missing contract id']);
        return;
    }
    if (!in_array($action, ['approve', 'reject'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Action must be approve or reject']);
        return;
    }
    $status = $action === 'approve' ? 'approved' : 'rejected';
    $q = "UPDATE student_contracts SET status='$status', admin_comment='$admin_comment' WHERE id=$contract_id";
    if (mysqli_query($conn, $q) && mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        $dbError = mysqli_error($conn);
        $msg = $dbError ? ('Update failed: ' . $dbError) : 'Update failed or contract not found';
        echo json_encode(['success' => false, 'error' => $msg]);
    }
    return;
}

$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$where = '1=1';
if ($status !== '') $where .= " AND status = '$status'";

// Include contract_file so frontend can provide a direct download/view link
$q = "SELECT id, student_name, index_number, original_filename, contract_file, status, submission_date, admin_comment FROM student_contracts WHERE $where ORDER BY submission_date DESC LIMIT 200";
$res = mysqli_query($conn, $q);
$list = [];
while ($row = mysqli_fetch_assoc($res)) {
    $list[] = [
        'id' => (int)$row['id'],
        'student_name' => $row['student_name'] ?? '',
        'index_number' => $row['index_number'] ?? '',
        'original_filename' => $row['original_filename'] ?? '',
        'contract_file' => $row['contract_file'] ?? '',
        'status' => $row['status'] ?? 'pending',
        'submission_date' => $row['submission_date'] ?? null,
        'admin_comment' => $row['admin_comment'] ?? '',
    ];
}
echo json_encode($list);
