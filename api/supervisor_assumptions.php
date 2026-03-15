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

$q = "SELECT index_number, first_name, last_name, programme, level, session,
             company_name, company_region, supervisor_name, supervisor_contact, supervisor_email, company_address
      FROM students_assumption
      WHERE index_number IN ($in_list)
      ORDER BY index_number
      LIMIT 200";
$res = mysqli_query($conn, $q);
$list = [];
while ($row = mysqli_fetch_assoc($res)) {
    $list[] = [
        'index_number' => $row['index_number'] ?? '',
        'first_name' => $row['first_name'] ?? '',
        'last_name' => $row['last_name'] ?? '',
        'programme' => $row['programme'] ?? '',
        'level' => $row['level'] ?? '',
        'session' => $row['session'] ?? '',
        'company_name' => $row['company_name'] ?? '',
        'company_region' => $row['company_region'] ?? '',
        'supervisor_name' => $row['supervisor_name'] ?? '',
        'supervisor_contact' => $row['supervisor_contact'] ?? '',
        'supervisor_email' => $row['supervisor_email'] ?? '',
        'company_address' => $row['company_address'] ?? '',
    ];
}

echo json_encode($list);

