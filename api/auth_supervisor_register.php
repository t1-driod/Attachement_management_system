<?php
/**
 * Institutional supervisor registration. Mirrors institutional_supervisor_signup.php logic.
 * POST JSON: full_name, staff_id, email, phone, password, confirm_password
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    return;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true) ?: [];

$full_name = trim((string)($body['full_name'] ?? ''));
$staff_id = trim((string)($body['staff_id'] ?? ''));
$email = trim((string)($body['email'] ?? ''));
$phone = trim((string)($body['phone'] ?? ''));
$password = trim((string)($body['password'] ?? ''));
$confirm_password = trim((string)($body['confirm_password'] ?? ''));

if ($full_name === '' || $staff_id === '' || $email === '' || $phone === '' || $password === '' || $confirm_password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please fill in all fields.']);
    return;
}

if ($password !== $confirm_password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Password and Confirm Password do not match.']);
    return;
}

$staff_id_safe = mysqli_real_escape_string($conn, $staff_id);
$check = mysqli_query($conn, "SELECT id FROM visiting_lecturers WHERE staff_id='$staff_id_safe' LIMIT 1");
if ($check && mysqli_num_rows($check) > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'An account with this Staff ID already exists.']);
    return;
}

$name_safe = mysqli_real_escape_string($conn, $full_name);
$email_safe = mysqli_real_escape_string($conn, $email);
$phone_safe = mysqli_real_escape_string($conn, $phone);
$pwd_safe = mysqli_real_escape_string($conn, $password);

$insert = "INSERT INTO visiting_lecturers 
  (lecturer_name, lecturer_faculty, lecturer_phone_number, lecturer_region_residence, lecturer_department, lecturer_email, staff_id, password) 
  VALUES ('$name_safe', '', '$phone_safe', '', '', '$email_safe', '$staff_id_safe', '$pwd_safe')";

if (mysqli_query($conn, $insert)) {
    echo json_encode(['success' => true, 'message' => 'Account created. You can now sign in.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to create account. Please try again.']);
}
