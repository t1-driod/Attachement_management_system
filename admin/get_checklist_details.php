<?php
include '../database_connection/database_connection.php';

if(isset($_GET['id'])) {
    $checklist_id = (int)$_GET['id'];

    $query = "SELECT * FROM orientation_checklist WHERE id = '$checklist_id'";
    $result = mysqli_query($conn, $query);

    if($result && mysqli_num_rows($result) > 0) {
        $checklist = mysqli_fetch_assoc($result);

        // Define all checklist items with their labels matching the Word document
        $general_items = [
            'general_staff_introduction' => 'Introduction to key staff members and their roles explained',
            'general_facilities_location' => 'Location of facilities such as rest rooms, canteen, etc.',
            'general_tea_coffee_lunch' => 'Tea/coffee and lunch arrangements',
            'general_transport_arrangements' => 'Transport arrangements (if applicable)',
            'general_dress_code' => 'Dress code',
            'general_code_of_conduct' => 'Code of conduct',
            'general_policies_regulations' => 'Policies and regulations'
        ];

        $work_items = [
            'work_workspace' => 'Work space',
            'work_duty_arrangements' => 'Duty arrangements',
            'work_schedule_meetings' => 'Schedule of meetings',
            'work_first_meeting_supervisor' => 'First meeting with host supervisor'
        ];

        $health_items = [
            'health_emergency_procedures' => 'Emergency procedures',
            'health_safety_policy' => 'Safety policy received or location known',
            'health_first_aid_arrangements' => 'First aid arrangements such as location of first aid box, names of first aiders, etc.',
            'health_fire_procedures' => 'Fire procedures and location of fire extinguishers',
            'health_accident_reporting' => 'Accident reporting and location of accident book',
            'health_manual_handling' => 'Manual handling procedures',
            'health_safety_regulations' => 'Safety regulations',
            'health_equipment_instruction' => 'Instruction on equipment and their use'
        ];

        $others_items = [
            'others_student_info_form' => 'Student information form (Contract form)',
            'others_social_media_guidelines' => 'Social media guidelines',
            'others_it_systems_equipment' => 'IT systems and equipment'
        ];

        echo "<div class='checklist-details'>";
        echo "<h4>" . htmlspecialchars($checklist['student_name']) . " (" . htmlspecialchars($checklist['index_number']) . ")</h4>";
        if(!empty($checklist['host_institution'])) {
            echo "<p><strong>Host Institution:</strong> " . htmlspecialchars($checklist['host_institution']) . "</p>";
        }
        echo "<p><strong>Completed on:</strong> " . date('F j, Y \a\t g:i A', strtotime($checklist['completed_at'])) . "</p>";
        echo "<hr>";

        // Count completed items
        $completed_count = 0;
        $total_items = count($general_items) + count($work_items) + count($health_items) + count($others_items);

        echo "<table class='table table-bordered' style='margin-top: 20px;'>";
        echo "<thead><tr><th>Item</th><th>Status</th></tr></thead>";
        echo "<tbody>";

        // General Section
        echo "<tr><td colspan='2' style='background-color: #e9ecef; font-weight: bold; font-style: italic; text-align: center;'><em>General</em></td></tr>";
        foreach($general_items as $field => $label) {
            $is_completed = isset($checklist[$field]) ? $checklist[$field] : false;
            if($is_completed) $completed_count++;
            $status = $is_completed ? '<span class="text-success"><i class="glyphicon glyphicon-ok"></i> Completed</span>' : '<span class="text-muted"><i class="glyphicon glyphicon-remove"></i> Not completed</span>';
            echo "<tr><td>$label</td><td>$status</td></tr>";
        }

        // Work-related Section
        echo "<tr><td colspan='2' style='background-color: #e9ecef; font-weight: bold; font-style: italic; text-align: center;'><em>Work-related</em></td></tr>";
        foreach($work_items as $field => $label) {
            $is_completed = isset($checklist[$field]) ? $checklist[$field] : false;
            if($is_completed) $completed_count++;
            $status = $is_completed ? '<span class="text-success"><i class="glyphicon glyphicon-ok"></i> Completed</span>' : '<span class="text-muted"><i class="glyphicon glyphicon-remove"></i> Not completed</span>';
            echo "<tr><td>$label</td><td>$status</td></tr>";
        }

        // Health and Safety Section
        echo "<tr><td colspan='2' style='background-color: #e9ecef; font-weight: bold; font-style: italic; text-align: center;'><em>Health and Safety</em></td></tr>";
        foreach($health_items as $field => $label) {
            $is_completed = isset($checklist[$field]) ? $checklist[$field] : false;
            if($is_completed) $completed_count++;
            $status = $is_completed ? '<span class="text-success"><i class="glyphicon glyphicon-ok"></i> Completed</span>' : '<span class="text-muted"><i class="glyphicon glyphicon-remove"></i> Not completed</span>';
            echo "<tr><td>$label</td><td>$status</td></tr>";
        }

        // Others Section
        echo "<tr><td colspan='2' style='background-color: #e9ecef; font-weight: bold; font-style: italic; text-align: center;'><em>Others</em></td></tr>";
        foreach($others_items as $field => $label) {
            $is_completed = isset($checklist[$field]) ? $checklist[$field] : false;
            if($is_completed) $completed_count++;
            $status = $is_completed ? '<span class="text-success"><i class="glyphicon glyphicon-ok"></i> Completed</span>' : '<span class="text-muted"><i class="glyphicon glyphicon-remove"></i> Not completed</span>';
            echo "<tr><td>$label</td><td>$status</td></tr>";
        }

        echo "</tbody></table>";

        // Display signature information if available
        if(!empty($checklist['student_signature']) || !empty($checklist['host_supervisor_signature']) || !empty($checklist['wrl_coordinator_signature'])) {
            echo "<hr>";
            echo "<h4>Signatures</h4>";
            echo "<table class='table table-bordered' style='margin-top: 10px;'>";
            echo "<tr><th>Role</th><th>Signature/Name</th><th>Date</th></tr>";
            
            if(!empty($checklist['student_signature'])) {
                echo "<tr><td><strong>Student</strong></td><td>" . htmlspecialchars($checklist['student_signature']) . "</td><td>" . ($checklist['student_signature_date'] ? date('Y-m-d', strtotime($checklist['student_signature_date'])) : '') . "</td></tr>";
            }
            if(!empty($checklist['host_supervisor_signature'])) {
                echo "<tr><td><strong>Host Supervisor</strong></td><td>" . htmlspecialchars($checklist['host_supervisor_signature']) . "</td><td>" . ($checklist['host_supervisor_date'] ? date('Y-m-d', strtotime($checklist['host_supervisor_date'])) : '') . "</td></tr>";
            }
            if(!empty($checklist['wrl_coordinator_signature'])) {
                echo "<tr><td><strong>WRL Coordinator</strong></td><td>" . htmlspecialchars($checklist['wrl_coordinator_signature']) . "</td><td>" . ($checklist['wrl_coordinator_date'] ? date('Y-m-d', strtotime($checklist['wrl_coordinator_date'])) : '') . "</td></tr>";
            }
            
            echo "</table>";
        }

        echo "<hr>";
        echo "<div class='text-center'>";
        echo "<h4>Completion Summary</h4>";
        echo "<div class='progress' style='height: 30px;'>";
        $percentage = round(($completed_count / $total_items) * 100);
        echo "<div class='progress-bar progress-bar-success' role='progressbar' style='width: {$percentage}%' aria-valuenow='$percentage' aria-valuemin='0' aria-valuemax='100'>";
        echo "{$completed_count}/{$total_items} items completed ({$percentage}%)";
        echo "</div>";
        echo "</div>";
        echo "</div>";

        echo "</div>";
    } else {
        echo "<div class='alert alert-danger'>Checklist not found.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Invalid request.</div>";
}
?>