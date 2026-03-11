<?php
require_once __DIR__ . '/supervisor_shared.php';

/**
 * Get list of assigned student index_numbers for current supervisor session.
 *
 * @param mysqli $conn
 * @return string[] index numbers
 */
function iasms_get_assigned_indexes_for_current_supervisor(mysqli $conn): array
{
    if (($_SESSION['role'] ?? '') !== 'supervisor') {
        return [];
    }
    $supervisorId = (string)($_SESSION['user_id'] ?? '');
    $supervisorName = (string)($_SESSION['name'] ?? '');
    [, $summary] = [null, null];
    [$students, $summary] = iasms_get_supervisor_students_and_summary($conn, $supervisorId, $supervisorName);
    $indexes = [];
    foreach ($students as $row) {
        $idx = $row['student_index'] ?? '';
        if ($idx !== '') {
            $indexes[$idx] = true;
        }
    }
    return array_keys($indexes);
}

