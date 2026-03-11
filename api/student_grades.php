<?php
// GET: current student's grades (visiting + company supervisor) from industrial_registration or grade tables
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

$visiting_grade = null;
$company_grade = null;
$visiting_date = null;
$company_date = null;

$q = "SELECT visiting_supervisor_grade, company_supervisor_grade FROM industrial_registration WHERE index_number='$idx' LIMIT 1";
$r = mysqli_query($conn, $q);
if ($r && $row = mysqli_fetch_assoc($r)) {
    $visiting_grade = $row['visiting_supervisor_grade'] !== null && $row['visiting_supervisor_grade'] !== '' ? (float)$row['visiting_supervisor_grade'] : null;
    $company_grade = $row['company_supervisor_grade'] !== null && $row['company_supervisor_grade'] !== '' ? (float)$row['company_supervisor_grade'] : null;
}

if ($visiting_grade === null) {
    $vq = "SELECT grade, date FROM visiting_supervisor_grade WHERE user_index='$idx' ORDER BY date DESC LIMIT 1";
    $vr = mysqli_query($conn, $vq);
    if ($vr && $vrow = mysqli_fetch_assoc($vr)) {
        $visiting_grade = (float)$vrow['grade'];
        $visiting_date = $vrow['date'] ?? null;
    }
}
if ($company_grade === null) {
    $cq = "SELECT grade, date FROM company_supervisor_grade WHERE user_index='$idx' ORDER BY date DESC LIMIT 1";
    $cr = mysqli_query($conn, $cq);
    if ($cr && $crow = mysqli_fetch_assoc($cr)) {
        $company_grade = (float)$crow['grade'];
        $company_date = $crow['date'] ?? null;
    }
}

echo json_encode([
    'visitingSupervisorGrade' => $visiting_grade,
    'companySupervisorGrade' => $company_grade,
    'visitingDate' => $visiting_date,
    'companyDate' => $company_date,
]);
exit;
