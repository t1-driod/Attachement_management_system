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

// Reuse same fields as admin_orientation.php
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
$q = "SELECT $selectFields FROM orientation_checklist WHERE index_number IN ($in_list) ORDER BY completed_at DESC LIMIT 200";
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

