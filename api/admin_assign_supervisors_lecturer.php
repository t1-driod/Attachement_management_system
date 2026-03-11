<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    http_response_code(405);
    return;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true) ?: [];

$name = trim($body['lecturer_name'] ?? '');
$department = trim($body['lecturer_department'] ?? '');
$contact = trim($body['lecturer_phone_number'] ?? '');
$faculty = trim($body['lecturer_faculty'] ?? '');
$email = trim($body['lecturer_email'] ?? '');
$region = trim($body['lecturer_region_residence'] ?? '');
$staff_id = trim($body['staff_id'] ?? '');
$password = trim($body['password'] ?? '');

if ($name === '' || $department === '' || $contact === '' || $faculty === '' || $region === '') {
    echo json_encode(['success' => false, 'error' => 'Name, department, contact, faculty and region are required']);
    return;
}

$name_esc = mysqli_real_escape_string($conn, $name);
$dept_esc = mysqli_real_escape_string($conn, $department);
$contact_esc = mysqli_real_escape_string($conn, $contact);
$faculty_esc = mysqli_real_escape_string($conn, $faculty);
$email_esc = mysqli_real_escape_string($conn, $email);
$region_esc = mysqli_real_escape_string($conn, $region);
$staff_id_esc = $staff_id !== '' ? "'" . mysqli_real_escape_string($conn, $staff_id) . "'" : 'NULL';
$password_esc = ($staff_id !== '' && $password !== '') ? "'" . mysqli_real_escape_string($conn, $password) . "'" : 'NULL';

$sql = "INSERT INTO visiting_lecturers (lecturer_name, lecturer_faculty, lecturer_phone_number, lecturer_region_residence, lecturer_department, lecturer_email, staff_id, password) VALUES ('$name_esc', '$faculty_esc', '$contact_esc', '$region_esc', '$dept_esc', '$email_esc', $staff_id_esc, $password_esc)";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'id' => (int)mysqli_insert_id($conn)]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to add lecturer']);
}
