<?php
if (!isset($_SESSION['role'])) {
    echo json_encode(['authenticated' => false]);
    return;
}
$user = [
    'id' => $_SESSION['user_id'] ?? '',
    'name' => $_SESSION['name'] ?? '',
    'role' => $_SESSION['role'],
];
if ($_SESSION['role'] === 'student') {
    $user['indexNumber'] = $_SESSION['index_number'] ?? '';
}
if ($_SESSION['role'] === 'supervisor') {
    $user['staffId'] = $_SESSION['staff_id'] ?? '';
}
echo json_encode(['authenticated' => true, 'user' => $user]);
