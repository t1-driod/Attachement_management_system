<?php
/**
 * Student assumption of duty: GET = registration check + current assumption; POST = submit assumption.
 * Requires student to be in industrial_registration first. Uses students_assumption table.
 */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    http_response_code(401);
    return;
}

$index_number = $_SESSION['index_number'] ?? '';
if ($index_number === '') {
    echo json_encode(['error' => 'Session invalid']);
    http_response_code(401);
    return;
}

$idx = mysqli_real_escape_string($conn, $index_number);

// Check industrial registration (required to submit assumption)
$reg_q = "SELECT first_name, last_name, other_name, programme, level, session FROM industrial_registration WHERE index_number='$idx' LIMIT 1";
$reg_res = mysqli_query($conn, $reg_q);
if (!$reg_res || mysqli_num_rows($reg_res) === 0) {
    echo json_encode([
        'registered' => false,
        'error' => 'You must complete industrial registration before submitting the assumption of duty form.',
    ]);
    return;
}
$reg_row = mysqli_fetch_assoc($reg_res);
$student_fname = $reg_row['first_name'];
$student_lname = $reg_row['last_name'];
$student_other_name = $reg_row['other_name'] ?? '';
$student_programme = $reg_row['programme'] ?? '';
$student_level = $reg_row['level'] ?? '';
$student_session = $reg_row['session'] ?? '';

// GET: return student info + current assumption if any
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $aq = "SELECT company_name, supervisor_name, supervisor_contact, supervisor_email, company_region, company_address
           FROM students_assumption WHERE index_number='$idx' LIMIT 1";
    $ares = mysqli_query($conn, $aq);
    $assumption = null;
    if ($ares && mysqli_num_rows($ares) === 1) {
        $assumption = mysqli_fetch_assoc($ares);
    }
    echo json_encode([
        'registered' => true,
        'student' => [
            'first_name' => $student_fname,
            'last_name' => $student_lname,
            'other_name' => $student_other_name,
            'programme' => $student_programme,
            'level' => $student_level,
            'session' => $student_session,
            'index_number' => $index_number,
        ],
        'submitted' => $assumption !== null,
        'assumption' => $assumption,
    ]);
    return;
}

// POST: submit assumption
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    return;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true) ?: [];

$company_name = trim($body['company_name'] ?? '');
$supervisor_name = trim($body['supervisor_name'] ?? '');
$supervisor_contact = trim($body['supervisor_contact'] ?? '');
$supervisor_email = trim($body['supervisor_email'] ?? '');
$company_region = trim($body['company_region'] ?? '');
$company_address = trim($body['company_address'] ?? '');

if ($company_name === '' || $supervisor_name === '' || $supervisor_contact === '' || $supervisor_email === '' || $company_region === '' || $company_address === '') {
    echo json_encode(['success' => false, 'error' => 'All company and supervisor fields are required.']);
    return;
}

$existing = mysqli_query($conn, "SELECT id FROM students_assumption WHERE index_number='$idx' LIMIT 1");
if ($existing && mysqli_num_rows($existing) > 0) {
    echo json_encode(['success' => false, 'error' => 'You have already submitted the assumption of duty form.']);
    return;
}

$reg_type = 'INDUSTRIAL REGISTRATION';
$cn_esc = mysqli_real_escape_string($conn, $company_name);
$sn_esc = mysqli_real_escape_string($conn, $supervisor_name);
$sc_esc = mysqli_real_escape_string($conn, $supervisor_contact);
$se_esc = mysqli_real_escape_string($conn, $supervisor_email);
$cr_esc = mysqli_real_escape_string($conn, $company_region);
$ca_esc = mysqli_real_escape_string($conn, $company_address);
$sf_esc = mysqli_real_escape_string($conn, $student_fname);
$sl_esc = mysqli_real_escape_string($conn, $student_lname);
$so_esc = mysqli_real_escape_string($conn, $student_other_name);
$sp_esc = mysqli_real_escape_string($conn, $student_programme);
$slv_esc = mysqli_real_escape_string($conn, $student_level);
$ss_esc = mysqli_real_escape_string($conn, $student_session);

$insert = "INSERT INTO students_assumption (first_name, last_name, other_name, index_number, level, programme, session,
            company_name, supervisor_name, supervisor_contact, supervisor_email, company_region, company_address, registration_type)
           VALUES ('$sf_esc', '$sl_esc', '$so_esc', '$idx', '$slv_esc', '$sp_esc', '$ss_esc',
                   '$cn_esc', '$sn_esc', '$sc_esc', '$se_esc', '$cr_esc', '$ca_esc', '$reg_type')";

if (!mysqli_query($conn, $insert)) {
    echo json_encode(['success' => false, 'error' => 'Submission failed. Please try again.']);
    return;
}

// Update industrial_registration with supervisor and region (mirror legacy behaviour)
mysqli_query($conn, "UPDATE industrial_registration SET company_supervisor_name='$sn_esc', company_supervisor_contact='$sc_esc', attachment_region='$cr_esc' WHERE index_number='$idx'");

echo json_encode([
    'success' => true,
    'message' => 'Assumption of duty form submitted successfully.',
]);
