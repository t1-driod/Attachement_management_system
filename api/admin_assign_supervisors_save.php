<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    http_response_code(405);
    return;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true) ?: [];
$assignments = $body['assignments'] ?? [];

if (!is_array($assignments)) {
    echo json_encode(['success' => false, 'error' => 'Invalid assignments']);
    return;
}

$faculties_lower = ['agr', 'arts', 'com', 'cie', 'edu', 'eng', 'law', 'med', 'sci', 'soc', 'vet'];
$region_list = ['Bulawayo', 'Harare', 'Manicaland', 'Mashonaland Central', 'Mashonaland East', 'Mashonaland West', 'Masvingo', 'Matabeleland North', 'Matabeleland South', 'Midlands'];

foreach ($region_list as $region) {
    $row = $assignments[$region] ?? [];
    $sets = [];
    foreach ($faculties_lower as $f) {
        $first = isset($row["first_supervisor_$f"]) ? mysqli_real_escape_string($conn, $row["first_supervisor_$f"]) : '';
        $second = isset($row["second_supervisor_$f"]) ? mysqli_real_escape_string($conn, $row["second_supervisor_$f"]) : '';
        $sets[] = "first_supervisor_$f='$first', second_supervisor_$f='$second'";
    }
    $set_clause = implode(', ', $sets);
    $region_esc = mysqli_real_escape_string($conn, $region);
    $sql = "UPDATE assigned_lecturers SET $set_clause WHERE regions='$region_esc'";
    if (!mysqli_query($conn, $sql)) {
        echo json_encode(['success' => false, 'error' => "Failed to update region: $region"]);
        return;
    }
}

echo json_encode(['success' => true]);
