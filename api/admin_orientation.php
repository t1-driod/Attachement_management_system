<?php
$filter = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where = '1=1';
if ($filter && $search !== '') {
    $like = "'%" . $search . "%'";
    if ($filter === 'Student Name') {
        $where = "(student_name LIKE $like OR index_number LIKE $like)";
    } elseif ($filter === 'Index Number') {
        $where = "index_number LIKE $like";
    }
}

// We only need to know if each checklist item is completed to compute counts
$fields = [
    'general_staff_introduction',
    'general_facilities_location',
    'general_tea_coffee_lunch',
    'general_transport_arrangements',
    'general_dress_code',
    'general_code_of_conduct',
    'general_policies_regulations',
    'work_workspace',
    'work_duty_arrangements',
    'work_schedule_meetings',
    'work_first_meeting_supervisor',
    'health_emergency_procedures',
    'health_safety_policy',
    'health_first_aid_arrangements',
    'health_fire_procedures',
    'health_accident_reporting',
    'health_manual_handling',
    'health_safety_regulations',
    'health_equipment_instruction',
    'others_student_info_form',
    'others_social_media_guidelines',
    'others_it_systems_equipment',
];

$selectFields = 'id, student_name, index_number, completed_at,' . implode(',', $fields);
$q = "SELECT $selectFields FROM orientation_checklist WHERE $where ORDER BY completed_at DESC LIMIT 200";
$res = mysqli_query($conn, $q);
$list = [];
while ($row = mysqli_fetch_assoc($res)) {
    $total = count($fields);
    $completed = 0;
    foreach ($fields as $f) {
        if (!empty($row[$f])) {
            $completed++;
        }
    }
    $pct = $total > 0 ? round(($completed / $total) * 100) : 0;

    $list[] = [
        'id' => (int)$row['id'],
        'student_name' => $row['student_name'] ?? '',
        'index_number' => $row['index_number'] ?? '',
        'completed_at' => $row['completed_at'] ?? null,
        'completed_items' => $completed,
        'total_items' => $total,
        'completion_percent' => $pct,
    ];
}
echo json_encode($list);
