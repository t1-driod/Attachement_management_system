<?php
/**
 * Student registration (registered_students). Mirrors index.php btn_signup logic.
 * POST JSON: first_name, last_name, index_number, password
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    return;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true) ?: [];

$first_name = trim((string)($body['first_name'] ?? ''));
$last_name = trim((string)($body['last_name'] ?? ''));
$index_number = trim((string)($body['index_number'] ?? ''));
$password = trim((string)($body['password'] ?? ''));

if ($first_name === '' || $last_name === '' || $index_number === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Provide details for all fields.']);
    return;
}

$idx = mysqli_real_escape_string($conn, $index_number);
$check = mysqli_query($conn, "SELECT index_number FROM registered_students WHERE index_number='$idx' LIMIT 1");
if ($check && mysqli_num_rows($check) > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'An account with this index number already exists.']);
    return;
}

$fname = mysqli_real_escape_string($conn, $first_name);
$lname = mysqli_real_escape_string($conn, $last_name);
$pwd = mysqli_real_escape_string($conn, $password);
$insert = "INSERT INTO registered_students (first_name, last_name, index_number, password) VALUES ('$fname','$lname','$idx','$pwd')";

if (mysqli_query($conn, $insert)) {
    echo json_encode(['success' => true, 'message' => 'Registration successful. You can now sign in.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to register. Please try again.']);
}
