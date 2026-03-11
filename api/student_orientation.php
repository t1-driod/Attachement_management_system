<?php
// GET: return current student's checklist status and data (if completed)
// POST: submit orientation checklist (student session required)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    http_response_code(401);
    return;
}

$index_number = $_SESSION['index_number'] ?? '';
$student_name = $_SESSION['name'] ?? '';
if ($index_number === '') {
    echo json_encode(['error' => 'Session invalid']);
    http_response_code(401);
    return;
}

$idx = mysqli_real_escape_string($conn, $index_number);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = "SELECT * FROM orientation_checklist WHERE index_number='$idx' LIMIT 1";
    $res = mysqli_query($conn, $q);
    if (!$res || mysqli_num_rows($res) === 0) {
        echo json_encode(['completed' => false]);
        return;
    }
    $row = mysqli_fetch_assoc($res);
    $data = [
        'completed' => true,
        'student_name' => $row['student_name'] ?? '',
        'index_number' => $row['index_number'] ?? '',
        'host_institution' => $row['host_institution'] ?? '',
        'general_staff_introduction' => (int)($row['general_staff_introduction'] ?? 0),
        'general_facilities_location' => (int)($row['general_facilities_location'] ?? 0),
        'general_tea_coffee_lunch' => (int)($row['general_tea_coffee_lunch'] ?? 0),
        'general_transport_arrangements' => (int)($row['general_transport_arrangements'] ?? 0),
        'general_dress_code' => (int)($row['general_dress_code'] ?? 0),
        'general_code_of_conduct' => (int)($row['general_code_of_conduct'] ?? 0),
        'general_policies_regulations' => (int)($row['general_policies_regulations'] ?? 0),
        'work_workspace' => (int)($row['work_workspace'] ?? 0),
        'work_duty_arrangements' => (int)($row['work_duty_arrangements'] ?? 0),
        'work_schedule_meetings' => (int)($row['work_schedule_meetings'] ?? 0),
        'work_first_meeting_supervisor' => (int)($row['work_first_meeting_supervisor'] ?? 0),
        'health_emergency_procedures' => (int)($row['health_emergency_procedures'] ?? 0),
        'health_safety_policy' => (int)($row['health_safety_policy'] ?? 0),
        'health_first_aid_arrangements' => (int)($row['health_first_aid_arrangements'] ?? 0),
        'health_fire_procedures' => (int)($row['health_fire_procedures'] ?? 0),
        'health_accident_reporting' => (int)($row['health_accident_reporting'] ?? 0),
        'health_manual_handling' => (int)($row['health_manual_handling'] ?? 0),
        'health_safety_regulations' => (int)($row['health_safety_regulations'] ?? 0),
        'health_equipment_instruction' => (int)($row['health_equipment_instruction'] ?? 0),
        'others_student_info_form' => (int)($row['others_student_info_form'] ?? 0),
        'others_social_media_guidelines' => (int)($row['others_social_media_guidelines'] ?? 0),
        'others_it_systems_equipment' => (int)($row['others_it_systems_equipment'] ?? 0),
        'student_signature' => $row['student_signature'] ?? '',
        'student_signature_date' => $row['student_signature_date'] ?? null,
        'host_supervisor_signature' => $row['host_supervisor_signature'] ?? '',
        'host_supervisor_date' => $row['host_supervisor_date'] ?? null,
        'wrl_coordinator_signature' => $row['wrl_coordinator_signature'] ?? '',
        'wrl_coordinator_date' => $row['wrl_coordinator_date'] ?? null,
        'completed_at' => $row['completed_at'] ?? null,
    ];
    echo json_encode($data);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true) ?: [];

    $host_institution = mysqli_real_escape_string($conn, $body['host_institution'] ?? '');
    $general_staff_introduction = !empty($body['general_staff_introduction']) ? 1 : 0;
    $general_facilities_location = !empty($body['general_facilities_location']) ? 1 : 0;
    $general_tea_coffee_lunch = !empty($body['general_tea_coffee_lunch']) ? 1 : 0;
    $general_transport_arrangements = !empty($body['general_transport_arrangements']) ? 1 : 0;
    $general_dress_code = !empty($body['general_dress_code']) ? 1 : 0;
    $general_code_of_conduct = !empty($body['general_code_of_conduct']) ? 1 : 0;
    $general_policies_regulations = !empty($body['general_policies_regulations']) ? 1 : 0;
    $work_workspace = !empty($body['work_workspace']) ? 1 : 0;
    $work_duty_arrangements = !empty($body['work_duty_arrangements']) ? 1 : 0;
    $work_schedule_meetings = !empty($body['work_schedule_meetings']) ? 1 : 0;
    $work_first_meeting_supervisor = !empty($body['work_first_meeting_supervisor']) ? 1 : 0;
    $health_emergency_procedures = !empty($body['health_emergency_procedures']) ? 1 : 0;
    $health_safety_policy = !empty($body['health_safety_policy']) ? 1 : 0;
    $health_first_aid_arrangements = !empty($body['health_first_aid_arrangements']) ? 1 : 0;
    $health_fire_procedures = !empty($body['health_fire_procedures']) ? 1 : 0;
    $health_accident_reporting = !empty($body['health_accident_reporting']) ? 1 : 0;
    $health_manual_handling = !empty($body['health_manual_handling']) ? 1 : 0;
    $health_safety_regulations = !empty($body['health_safety_regulations']) ? 1 : 0;
    $health_equipment_instruction = !empty($body['health_equipment_instruction']) ? 1 : 0;
    $others_student_info_form = !empty($body['others_student_info_form']) ? 1 : 0;
    $others_social_media_guidelines = !empty($body['others_social_media_guidelines']) ? 1 : 0;
    $others_it_systems_equipment = !empty($body['others_it_systems_equipment']) ? 1 : 0;
    $student_signature = mysqli_real_escape_string($conn, $body['student_signature'] ?? '');
    $student_signature_date = !empty($body['student_signature_date']) ? "'" . mysqli_real_escape_string($conn, $body['student_signature_date']) . "'" : 'NULL';
    $host_supervisor_signature = mysqli_real_escape_string($conn, $body['host_supervisor_signature'] ?? '');
    $host_supervisor_date = !empty($body['host_supervisor_date']) ? "'" . mysqli_real_escape_string($conn, $body['host_supervisor_date']) . "'" : 'NULL';
    $wrl_coordinator_signature = mysqli_real_escape_string($conn, $body['wrl_coordinator_signature'] ?? '');
    $wrl_coordinator_date = !empty($body['wrl_coordinator_date']) ? "'" . mysqli_real_escape_string($conn, $body['wrl_coordinator_date']) . "'" : 'NULL';

    $name_esc = mysqli_real_escape_string($conn, $student_name);

    $check = mysqli_query($conn, "SELECT id FROM orientation_checklist WHERE index_number='$idx' LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'error' => 'Checklist already submitted']);
        return;
    }

    $sql = "INSERT INTO orientation_checklist
        (student_name, index_number, host_institution, general_staff_introduction, general_facilities_location,
         general_tea_coffee_lunch, general_transport_arrangements, general_dress_code, general_code_of_conduct,
         general_policies_regulations, work_workspace, work_duty_arrangements, work_schedule_meetings,
         work_first_meeting_supervisor, health_emergency_procedures, health_safety_policy,
         health_first_aid_arrangements, health_fire_procedures, health_accident_reporting,
         health_manual_handling, health_safety_regulations, health_equipment_instruction,
         others_student_info_form, others_social_media_guidelines, others_it_systems_equipment,
         student_signature, student_signature_date, host_supervisor_signature, host_supervisor_date,
         wrl_coordinator_signature, wrl_coordinator_date)
        VALUES
        ('$name_esc', '$idx', '$host_institution', $general_staff_introduction, $general_facilities_location,
         $general_tea_coffee_lunch, $general_transport_arrangements, $general_dress_code, $general_code_of_conduct,
         $general_policies_regulations, $work_workspace, $work_duty_arrangements, $work_schedule_meetings,
         $work_first_meeting_supervisor, $health_emergency_procedures, $health_safety_policy,
         $health_first_aid_arrangements, $health_fire_procedures, $health_accident_reporting,
         $health_manual_handling, $health_safety_regulations, $health_equipment_instruction,
         $others_student_info_form, $others_social_media_guidelines, $others_it_systems_equipment,
         '$student_signature', $student_signature_date, '$host_supervisor_signature', $host_supervisor_date,
         '$wrl_coordinator_signature', $wrl_coordinator_date)";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save checklist']);
    }
    return;
}

echo json_encode(['error' => 'Method not allowed']);
http_response_code(405);
