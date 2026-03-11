<?php
// Ensure visit_number exists on visiting_supervisor_grade so we can show first/second visit scores
$colCheck = mysqli_query($conn, "SHOW COLUMNS FROM visiting_supervisor_grade LIKE 'visit_number'");
if (!$colCheck || mysqli_num_rows($colCheck) === 0) {
    mysqli_query($conn, "ALTER TABLE visiting_supervisor_grade ADD COLUMN visit_number TINYINT(1) NOT NULL DEFAULT 1 AFTER user_index");
}

$filter = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where = '1=1';
if ($filter && $search !== '') {
    $like = "'%" . $search . "%'";
    switch ($filter) {
        case 'first_name': $where = "ir.first_name LIKE $like"; break;
        case 'last_name': $where = "ir.last_name LIKE $like"; break;
        case 'index_number': $where = "ir.index_number LIKE $like"; break;
        case 'programme': $where = "ir.programme LIKE $like"; break;
        case 'level': $where = "ir.level LIKE $like"; break;
        case 'session': $where = "ir.session LIKE $like"; break;
        case 'score': $where = "(ir.visiting_supervisor_grade LIKE $like OR vsg.first_visit_grade LIKE $like OR vsg.second_visit_grade LIKE $like)"; break;
    }
}

$q = "SELECT ir.index_number, ir.first_name, ir.last_name, ir.programme, ir.level, ir.session,
       vsg.first_visit_grade, vsg.second_visit_grade
FROM industrial_registration ir
LEFT JOIN (
    SELECT user_index,
           MAX(CASE WHEN visit_number = 1 THEN grade END) AS first_visit_grade,
           MAX(CASE WHEN visit_number = 2 THEN grade END) AS second_visit_grade
    FROM visiting_supervisor_grade
    GROUP BY user_index
) vsg ON ir.index_number = vsg.user_index
WHERE $where
ORDER BY ir.index_number
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
        'first_visit_grade' => $row['first_visit_grade'] !== null && $row['first_visit_grade'] !== '' ? (int)$row['first_visit_grade'] : null,
        'second_visit_grade' => $row['second_visit_grade'] !== null && $row['second_visit_grade'] !== '' ? (int)$row['second_visit_grade'] : null,
    ];
}
echo json_encode($list);
