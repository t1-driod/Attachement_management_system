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

$in_list = "'" . implode(
    "','",
    array_map(
        static function (string $idx) use ($conn): string {
            return mysqli_real_escape_string($conn, $idx);
        },
        $assigned
    )
) . "'";

// POST: approve or reject contract (for assigned students only)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true) ?: [];
    $action = $body['action'] ?? '';
    $contract_id = (int)($body['contract_id'] ?? 0);
    $comment = isset($body['comment']) ? mysqli_real_escape_string($conn, (string)$body['comment']) : '';

    if ($contract_id < 1 || !in_array($action, ['approve', 'reject'], true)) {
        echo json_encode(['success' => false, 'error' => 'Invalid action or contract id']);
        return;
    }

    $status = $action === 'approve' ? 'approved' : 'rejected';

    // Only update contracts belonging to assigned students
    $q = "UPDATE student_contracts
          SET status='$status', admin_comment='$comment'
          WHERE id=$contract_id AND index_number IN ($in_list)";
    if (mysqli_query($conn, $q) && mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Update failed or not allowed']);
    }
    return;
}

$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, (string)$_GET['status']) : '';
$where = "index_number IN ($in_list)";
if ($status !== '') {
    $where .= " AND status = '$status'";
}

$q = "SELECT id, student_name, index_number, original_filename, status, submission_date, admin_comment
      FROM student_contracts
      WHERE $where
      ORDER BY submission_date DESC
      LIMIT 200";
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

