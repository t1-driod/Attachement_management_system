<?php
// GET: lecturers list, current assignments by region, student count per region
$regions = ['Bulawayo', 'Harare', 'Manicaland', 'Mashonaland Central', 'Mashonaland East', 'Mashonaland West', 'Masvingo', 'Matabeleland North', 'Matabeleland South', 'Midlands'];
$faculties = ['AGR', 'ARTS', 'COM', 'CIE', 'EDU', 'ENG', 'LAW', 'MED', 'SCI', 'SOC', 'VET'];

$lecturers = [];
$r = mysqli_query($conn, "SELECT id, lecturer_name, lecturer_faculty, lecturer_department, lecturer_phone_number, lecturer_region_residence, lecturer_email, staff_id FROM visiting_lecturers ORDER BY lecturer_region_residence, lecturer_faculty, lecturer_name");
while ($row = mysqli_fetch_assoc($r)) {
    $lecturers[] = [
        'id' => (int)$row['id'],
        'lecturer_name' => $row['lecturer_name'] ?? '',
        'lecturer_faculty' => $row['lecturer_faculty'] ?? '',
        'lecturer_department' => $row['lecturer_department'] ?? '',
        'lecturer_phone_number' => $row['lecturer_phone_number'] ?? '',
        'lecturer_region_residence' => $row['lecturer_region_residence'] ?? '',
        'lecturer_email' => $row['lecturer_email'] ?? '',
        'staff_id' => $row['staff_id'] ?? null,
    ];
}

$assigned = [];
$ar = mysqli_query($conn, "SELECT * FROM assigned_lecturers");
while ($row = mysqli_fetch_assoc($ar)) {
    $region = $row['regions'] ?? '';
    $assigned[$region] = [];
    foreach ($faculties as $f) {
        $key = strtolower($f);
        $assigned[$region]["first_supervisor_$key"] = $row["first_supervisor_$key"] ?? '';
        $assigned[$region]["second_supervisor_$key"] = $row["second_supervisor_$key"] ?? '';
    }
}

$regionStats = [];
foreach ($regions as $reg) {
    $reg_esc = mysqli_real_escape_string($conn, $reg);
    $q = "SELECT COUNT(*) AS c FROM students_assumption WHERE company_region='$reg_esc'";
    $rs = mysqli_query($conn, $q);
    $regionStats[$reg] = ($rs && ($rw = mysqli_fetch_assoc($rs))) ? (int)$rw['c'] : 0;
}

$departments = ['Applied Mathematics', 'Computer Science', 'Hospitality', 'Marketing', 'Accountancy', 'Professional Studies', 'Liberal Studies', 'Secretariaship', 'Management Studies', 'Purchasing and Supply', 'Electrical/Electronic Engineering', 'Civil Engineering', 'Energy Systems Engineering', 'Automotive Engineering', 'Mechanical Engineering'];

echo json_encode([
    'regions' => $regions,
    'faculties' => $faculties,
    'lecturers' => $lecturers,
    'assigned' => $assigned,
    'regionStats' => $regionStats,
    'departments' => $departments,
]);
