
			<?php

			include '../../database_connection/database_connection.php';

			// Zimbabwe's 10 provinces
			$regions = array("Bulawayo","Harare","Manicaland","Mashonaland Central","Mashonaland East",
			  "Mashonaland West","Masvingo","Matabeleland North","Matabeleland South","Midlands");

			$regions_2 = array("-- Select Province --","Bulawayo","Harare","Manicaland","Mashonaland Central","Mashonaland East",
			  "Mashonaland West","Masvingo","Matabeleland North","Matabeleland South","Midlands");

			// Faculties: AGR, ARTS, COM, CIE, EDU, ENG, LAW, MED, SCI, SOC, VET
			$faculties = array("-- Select Lecturer Faculty --","AGR","ARTS","COM","CIE","EDU","ENG","LAW","MED","SCI","SOC","VET");
			$faculty_codes = array("agr","arts","com","cie","edu","eng","law","med","sci","soc","vet");

				$departments = array("-- Select Lecturer Department -- ","Applied Mathematics","Computer Science","Hospitality","Marketing","Accountancy","Professional Studies","Liberal Studies","Secretariaship","Management Studies","Purchasing and Supply","Electrical/Electronic Engineering","Civil Engineering","Energy Systems Engineering","Automotive Engineering","Mechanical Engineering");

			$mysql_query_1 = "SELECT * FROM visiting_lecturers";

			// Load current assignments so dropdowns show saved selections
			$current_assigned = array();
			$assigned_res = mysqli_query($conn, "SELECT * FROM assigned_lecturers");
			if ($assigned_res) {
				while ($ar = mysqli_fetch_assoc($assigned_res)) {
					$current_assigned[$ar['regions']] = $ar;
				}
			}
			// Show only supervisors for this faculty AND province (region)
			function selected_lecturer_option($conn, $faculty, $region, $selected_value) {
				$selected_value = (string)($selected_value ?? '');
				$faculty_esc = mysqli_real_escape_string($conn, $faculty);
				$region_esc = mysqli_real_escape_string($conn, $region);
				$query = "SELECT * FROM visiting_lecturers WHERE lecturer_faculty = '$faculty_esc' AND lecturer_region_residence = '$region_esc' ORDER BY lecturer_name";
				$r = mysqli_query($conn, $query);
				if (!$r) return;
				echo "<option value=\"\">-- Select --</option>";
				while ($row = mysqli_fetch_array($r)) {
					$name = $row["lecturer_name"];
					$sel = ($name === $selected_value) ? ' selected="selected"' : '';
					echo "<option" . $sel . ">" . htmlspecialchars($name) . "</option>";
				}
			}

			// Handle add lecturer before any output (so redirect can work)
			$add_lecturer_error = false;
			if(isset($_POST["btn_add_lecturer"])){
				$lecturer_name = $_POST["txt_lecturer_name"] ?? '';
				$lecturer_department = $_POST["lecturers_department"] ?? '';
				$lecturer_contact = $_POST["txt_lecturer_contact"] ?? '';
				$lecturer_faculty = $_POST["lecturers_faculty"] ?? '';
				$lecturer_email = $_POST["txt_lecturer_email"] ?? '';
				$lecturer_region = $_POST["selected_region"] ?? '';
				$staff_id = trim($_POST["txt_staff_id"] ?? '');
				$staff_password = $_POST["txt_staff_password"] ?? '';
				if($lecturer_name!=""&&$lecturer_department!=""&&$lecturer_contact!=""&&$lecturer_faculty!=""&&$lecturer_region!=""){
					$name_esc = mysqli_real_escape_string($conn, $lecturer_name);
					$dept_esc = mysqli_real_escape_string($conn, $lecturer_department);
					$contact_esc = mysqli_real_escape_string($conn, $lecturer_contact);
					$faculty_esc = mysqli_real_escape_string($conn, $lecturer_faculty);
					$email_esc = mysqli_real_escape_string($conn, $lecturer_email);
					$region_esc = mysqli_real_escape_string($conn, $lecturer_region);
					$staff_id_esc = $staff_id !== '' ? "'".mysqli_real_escape_string($conn, $staff_id)."'" : "NULL";
					$password_esc = ($staff_id !== '' && $staff_password !== '') ? "'".mysqli_real_escape_string($conn, $staff_password)."'" : "NULL";
					$my_insert_query = "INSERT INTO `visiting_lecturers` (`lecturer_name`, `lecturer_faculty`, `lecturer_phone_number`, `lecturer_region_residence`, `lecturer_department`, `lecturer_email`, `staff_id`, `password`) VALUES ('$name_esc', '$faculty_esc', '$contact_esc', '$region_esc', '$dept_esc', '$email_esc', $staff_id_esc, $password_esc)";
					if(mysqli_query($conn,$my_insert_query)){
						header('Location: assign_supervisors.php?lecturer_added=1');
						exit;
					}
				}else{
					$add_lecturer_error = true;
				}
			}

			 ?>


			<!DOCTYPE html>
			<html lang="en" class="bg-pink">
			<head>
			  <meta charset="utf-8">
			  <meta http-equiv="X-UA-Compatible" content="IE=edge">
			  <meta name="viewport" content="width=device-width, initial-scale=1">
			  <title>IASMS</title>

			  <link rel="stylesheet" href="../../css/bootstrap-theme.min.css"/>
			  <link rel="stylesheet" href="../../css/bootstrap.min.css"/>
			  <link rel="stylesheet" href="../../css/main_page_style.css"/>
			  <link rel="stylesheet" href="assign_supervisors.css"/>

			  <script type="text/javascript" src="../../js/jquery-3.1.1.min.js"/></script>
			  <script type="text/javascript" src="../../js/bootstrap.min.js"/></script>


			</head>
			<body>
			<?php
			if(!empty($_GET['lecturer_added'])){ echo "<script>alert('Lecturer has been added successfully.'); if(window.history && history.replaceState) history.replaceState(null,'','assign_supervisors.php');</script>"; }
			if(!empty($add_lecturer_error)){ echo "<script>alert('Please fill all required spaces.');</script>"; }
			?>

			<?php $topbar_display_name = 'Admin'; $topbar_logo_src = '../../img/header_log.png'; include '../../includes/topbar.php'; ?>

			<div id="left_side_bar">
			<ul id="menu_list">
			  <a class="menu_items_link" href="../view_registered_students/view_registered_students.php"><li class="menu_items_list">Registered Students</li></a>
			  <a class="menu_items_link" href="../view_orientation_checklists.php"><li class="menu_items_list">Orientation Checklists</li></a>
			  <a class="menu_items_link" href="../view_elogbooks.php"><li class="menu_items_list">E-Logbooks</li></a>
  <a class="menu_items_link" href="../manage_contracts.php"><li class="menu_items_list">View Contracts</li></a>
  <a class="menu_items_link" href="../view_submitted_reports.php"><li class="menu_items_list">View Submitted Reports</li></a>
  <a class="menu_items_link" href="../students_assumptions/students_assumptions.php"><li class="menu_items_list" >Student Assumptions</li></a>
			  <a class="menu_items_link" href="assign_supervisors.php"><li class="menu_items_list">Assign Supervisors</li></a>
			  <a class="menu_items_link" href="../visiting_score/visiting_supervisors_score.php"><li class="menu_items_list">Visiting Superviors Score</li></a>
			  <a class="menu_items_link" href="../company_score/company_supervisor_score.php"><li class="menu_items_list">Company Supervisor Score</li></a>
			  <a class="menu_items_link" href="../change_password/change_password.php"><li class="menu_items_list">Change Password</li></a>
			  <a class="menu_items_link" href="../../index.php"><li class="menu_items_list">Logout</li></a>
			</ul>
			</div>

			<div class="container-fluid">
			<div id="main_content">
				<div class = "panel">
				   <div class = "panel-heading phead">
					  <h2 class = "panel-title ptitle"> Assign Supervisors</h2>
				   </div>
						<div class = "panel-body pbody">

						<div class = "panel">
						<div class = "panel-heading phead">
							<h2 class = "panel-title ptitle"> Students Statistics</h2>
					   </div>
							<div class = "panel-body pbody">

						  <table class="table table-bordered table-hover">

							  <thead>
								<tr>
									<th style="text-align:center">Bulawayo</th>
									<th style="text-align:center">Harare</th>
									<th style="text-align:center">Manicaland</th>
									<th style="text-align:center">Mash. Central</th>
									<th style="text-align:center">Mash. East</th>
									<th style="text-align:center">Mash. West</th>
									<th style="text-align:center">Masvingo</th>
									<th style="text-align:center">Mat. North</th>
									<th style="text-align:center">Mat. South</th>
									<th style="text-align:center">Midlands</th>

								</tr>

							  </thead>

							  <tbody>
								<?php

								echo "<tr style='text-align:center;font-size:1.1em' width='100%'>";

								$mycounter = 0;                
								while($mycounter < 10){	
								$selected_item = $regions[$mycounter];
								$mysql_query_command_1 = "SELECT company_region FROM students_assumption WHERE company_region='$selected_item'";				
								$execute_result_query = mysqli_query($conn,$mysql_query_command_1);
								$row_cnt = mysqli_num_rows($execute_result_query); 					
								echo "<td>".$row_cnt."</td>";						
								$mycounter++;							
								}

								echo "</tr>";

								 ?>
							  </tbody>
						</table>
				 </div>
			   </div>


						   <div class = "panel">
						<div class = "panel-heading phead">
							<h2 class = "panel-title ptitle"> Registered Lecturers</h2>
					   </div>
							<div class = "panel-body pbody">

						  <table class="table table-bordered table-hover">

							  <thead>
								<tr>
									<th style="text-align:center">Name</th>
									<th style="text-align:center">Faculty</th>
									<th style="text-align:center">Department</th>
									<th style="text-align:center">Phone Number</th>
									<th style="text-align:center">Residence Province</th>
									<th style="text-align:center">E-mail</th>
									<th style="text-align:center">Staff ID (institutional login)</th>
								</tr>
							  </thead>
							  <tbody>
								<?php
								$mysql_query_command_1 = $mysql_query_1;
								$execute_result_query = mysqli_query($conn,$mysql_query_command_1);
								if ($execute_result_query) {
								while ($row_set = mysqli_fetch_array($execute_result_query)){
								  $staff_id_display = isset($row_set["staff_id"]) && $row_set["staff_id"] !== '' ? htmlspecialchars($row_set["staff_id"]) : '—';
								  echo "<tr style='text-align:center;font-size:1.1em'>";
								  echo "<td>".htmlspecialchars($row_set["lecturer_name"])."</td>";
								  echo "<td>".htmlspecialchars($row_set["lecturer_faculty"])."</td>";
								  echo "<td>".htmlspecialchars($row_set["lecturer_department"])."</td>";
								  echo "<td>".htmlspecialchars($row_set["lecturer_phone_number"])."</td>";
								  echo "<td>".htmlspecialchars($row_set["lecturer_region_residence"])."</td>";
								  echo "<td>".htmlspecialchars($row_set["lecturer_email"])."</td>";
								  echo "<td>".$staff_id_display."</td>";
								  echo "</tr>";
								}
								}
								 ?>
							  </tbody>
						</table>
				 </div>
			   </div>

			   <div class="panel">
				 <div class = "panel-heading phead phead-adjusted">
					  <h2 class = "panel-title ptitle"> Add Lecturer</h2>
				   </div>
						<div class = "panel-body">

						<form method="post" action="">

						<div class="row">
						<div class="col-md-12">

						<div class="col-md-5 col-md-offset-1">
						<input type="text" placeholder="Enter Name" name="txt_lecturer_name" class="form-control"/>
						</div>

						<div class="col-md-5">
						 <select class="form-control" id="lecturers_department" name="lecturers_department">
						  <?php
						  foreach($departments as $val) { echo "<option>".$val."</option>";};
						  ?>
						</select>
						</div>


						</div>
						</div>
						<br>

						<div class="row">
						<div class="col-md-12">

						<div class="col-md-5 col-md-offset-1">
						<input type="text" placeholder="Enter Contact (0200000000)" name="txt_lecturer_contact" class="form-control"/>
						</div>

						<div class="col-md-5">
						<select class="form-control" id="lecturers_faculty" name="lecturers_faculty">
						  <?php
							foreach($faculties as $val) { echo "<option>".$val."</option>";};
						   ?>
						</select>
						</div>


						</div>
						</div>
						<br>

						<div class="row">
						<div class="col-md-12">

						<div class="col-md-5 col-md-offset-1">
						<input type="email" placeholder="Enter valid email address" name="txt_lecturer_email" class="form-control"/>
						</div>

						<div class="col-md-5">
						<label for="selected_region" class="control-label">Province</label>
						<select class="form-control" id="selected_region" name="selected_region">
						<?php
						foreach($regions_2 as $val) { echo "<option>".$val."</option>";};
						?>
						</select>
						</div>

						</div>
						</div>

						<br>
						<div class="row">
						<div class="col-md-12">
						<div class="col-md-5 col-md-offset-1">
						<label for="txt_staff_id" class="control-label">Staff ID (for institutional supervisor login, optional)</label>
						<input type="text" placeholder="e.g. STF001" name="txt_staff_id" id="txt_staff_id" class="form-control"/>
						</div>
						<div class="col-md-5">
						<label for="txt_staff_password" class="control-label">Password (if Staff ID set, for institutional login)</label>
						<input type="password" placeholder="Leave blank if not institutional supervisor" name="txt_staff_password" id="txt_staff_password" class="form-control"/>
						</div>
						</div>
						</div>

						<div style="float: right">
						<input type="submit" value="Add" name="btn_add_lecturer" class="btn btn-primary"/>
						</div>

						</div>           

				   </div>

						</form>	
			   </div>

			 </div>



			 <div class = "panel">
						<div class = "panel-heading phead">
							<h2 class = "panel-title ptitle"> Assign Supervisors</h2>
					   </div>
							<div class = "panel-body pbody">
						  
						  <form method="post" action="">

						  <table class="table table-bordered table-hover">

							  <thead>
								<tr>
									<th style="text-align:center" width="10%">Provinces</th>
									<th colspan="11" style="text-align:center">Faculties</th>

								</tr>

							  </thead>
					  
							  <tbody>


							  <tr style='text-align:center;font-size:1.1em' width='100%'>

									<td></td>
									<td>AGR</td>
									<td>ARTS</td>
									<td>COM</td>
									<td>CIE</td>
									<td>EDU</td>
									<td>ENG</td>
									<td>LAW</td>
									<td>MED</td>
									<td>SCI</td>
									<td>SOC</td>
									<td>VET</td>

							  </tr>

							  <tr>

								<td>Bulawayo</td>  
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_accra_agr"><?php selected_lecturer_option($conn, 'AGR', 'Bulawayo', $current_assigned["Bulawayo"]["first_supervisor_agr"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_accra_agr"><?php selected_lecturer_option($conn, 'AGR', 'Bulawayo', $current_assigned["Bulawayo"]["second_supervisor_agr"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_accra_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Bulawayo', $current_assigned["Bulawayo"]["first_supervisor_arts"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_accra_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Bulawayo', $current_assigned["Bulawayo"]["second_supervisor_arts"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_accra_com"><?php selected_lecturer_option($conn, 'COM', 'Bulawayo', $current_assigned["Bulawayo"]["first_supervisor_com"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_accra_com"><?php selected_lecturer_option($conn, 'COM', 'Bulawayo', $current_assigned["Bulawayo"]["second_supervisor_com"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_accra_cie"><?php selected_lecturer_option($conn, 'CIE', 'Bulawayo', $current_assigned["Bulawayo"]["first_supervisor_cie"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_accra_cie"><?php selected_lecturer_option($conn, 'CIE', 'Bulawayo', $current_assigned["Bulawayo"]["second_supervisor_cie"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_accra_edu"><?php selected_lecturer_option($conn, 'EDU', 'Bulawayo', $current_assigned["Bulawayo"]["first_supervisor_edu"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_accra_edu"><?php selected_lecturer_option($conn, 'EDU', 'Bulawayo', $current_assigned["Bulawayo"]["second_supervisor_edu"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_accra_eng"><?php selected_lecturer_option($conn, 'ENG', 'Bulawayo', $current_assigned["Bulawayo"]["first_supervisor_eng"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_accra_eng"><?php selected_lecturer_option($conn, 'ENG', 'Bulawayo', $current_assigned["Bulawayo"]["second_supervisor_eng"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_accra_law"><?php selected_lecturer_option($conn, 'LAW', 'Bulawayo', $current_assigned["Bulawayo"]["first_supervisor_law"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_accra_law"><?php selected_lecturer_option($conn, 'LAW', 'Bulawayo', $current_assigned["Bulawayo"]["second_supervisor_law"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_accra_med"><?php selected_lecturer_option($conn, 'MED', 'Bulawayo', $current_assigned["Bulawayo"]["first_supervisor_med"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_accra_med"><?php selected_lecturer_option($conn, 'MED', 'Bulawayo', $current_assigned["Bulawayo"]["second_supervisor_med"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_accra_sci"><?php selected_lecturer_option($conn, 'SCI', 'Bulawayo', $current_assigned["Bulawayo"]["first_supervisor_sci"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_accra_sci"><?php selected_lecturer_option($conn, 'SCI', 'Bulawayo', $current_assigned["Bulawayo"]["second_supervisor_sci"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_accra_soc"><?php selected_lecturer_option($conn, 'SOC', 'Bulawayo', $current_assigned["Bulawayo"]["first_supervisor_soc"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_accra_soc"><?php selected_lecturer_option($conn, 'SOC', 'Bulawayo', $current_assigned["Bulawayo"]["second_supervisor_soc"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_accra_vet"><?php selected_lecturer_option($conn, 'VET', 'Bulawayo', $current_assigned["Bulawayo"]["first_supervisor_vet"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_accra_vet"><?php selected_lecturer_option($conn, 'VET', 'Bulawayo', $current_assigned["Bulawayo"]["second_supervisor_vet"] ?? ''); ?></select></td>
							  </tr>                 

							  <tr>

								<td>Harare</td>  
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_central_agr"><?php selected_lecturer_option($conn, 'AGR', 'Harare', $current_assigned["Harare"]["first_supervisor_agr"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_central_agr"><?php selected_lecturer_option($conn, 'AGR', 'Harare', $current_assigned["Harare"]["second_supervisor_agr"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_central_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Harare', $current_assigned["Harare"]["first_supervisor_arts"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_central_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Harare', $current_assigned["Harare"]["second_supervisor_arts"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_central_com"><?php selected_lecturer_option($conn, 'COM', 'Harare', $current_assigned["Harare"]["first_supervisor_com"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_central_com"><?php selected_lecturer_option($conn, 'COM', 'Harare', $current_assigned["Harare"]["second_supervisor_com"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_central_cie"><?php selected_lecturer_option($conn, 'CIE', 'Harare', $current_assigned["Harare"]["first_supervisor_cie"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_central_cie"><?php selected_lecturer_option($conn, 'CIE', 'Harare', $current_assigned["Harare"]["second_supervisor_cie"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_central_edu"><?php selected_lecturer_option($conn, 'EDU', 'Harare', $current_assigned["Harare"]["first_supervisor_edu"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_central_edu"><?php selected_lecturer_option($conn, 'EDU', 'Harare', $current_assigned["Harare"]["second_supervisor_edu"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_central_eng"><?php selected_lecturer_option($conn, 'ENG', 'Harare', $current_assigned["Harare"]["first_supervisor_eng"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_central_eng"><?php selected_lecturer_option($conn, 'ENG', 'Harare', $current_assigned["Harare"]["second_supervisor_eng"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_central_law"><?php selected_lecturer_option($conn, 'LAW', 'Harare', $current_assigned["Harare"]["first_supervisor_law"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_central_law"><?php selected_lecturer_option($conn, 'LAW', 'Harare', $current_assigned["Harare"]["second_supervisor_law"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_central_med"><?php selected_lecturer_option($conn, 'MED', 'Harare', $current_assigned["Harare"]["first_supervisor_med"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_central_med"><?php selected_lecturer_option($conn, 'MED', 'Harare', $current_assigned["Harare"]["second_supervisor_med"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_central_sci"><?php selected_lecturer_option($conn, 'SCI', 'Harare', $current_assigned["Harare"]["first_supervisor_sci"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_central_sci"><?php selected_lecturer_option($conn, 'SCI', 'Harare', $current_assigned["Harare"]["second_supervisor_sci"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_central_soc"><?php selected_lecturer_option($conn, 'SOC', 'Harare', $current_assigned["Harare"]["first_supervisor_soc"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_central_soc"><?php selected_lecturer_option($conn, 'SOC', 'Harare', $current_assigned["Harare"]["second_supervisor_soc"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_central_vet"><?php selected_lecturer_option($conn, 'VET', 'Harare', $current_assigned["Harare"]["first_supervisor_vet"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_central_vet"><?php selected_lecturer_option($conn, 'VET', 'Harare', $current_assigned["Harare"]["second_supervisor_vet"] ?? ''); ?></select></td>
							  </tr>

							  <tr>

								<td>Manicaland</td>  
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_eastern_agr"><?php selected_lecturer_option($conn, 'AGR', 'Manicaland', $current_assigned["Manicaland"]["first_supervisor_agr"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_eastern_agr"><?php selected_lecturer_option($conn, 'AGR', 'Manicaland', $current_assigned["Manicaland"]["second_supervisor_agr"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_eastern_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Manicaland', $current_assigned["Manicaland"]["first_supervisor_arts"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_eastern_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Manicaland', $current_assigned["Manicaland"]["second_supervisor_arts"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_eastern_com"><?php selected_lecturer_option($conn, 'COM', 'Manicaland', $current_assigned["Manicaland"]["first_supervisor_com"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_eastern_com"><?php selected_lecturer_option($conn, 'COM', 'Manicaland', $current_assigned["Manicaland"]["second_supervisor_com"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_eastern_cie"><?php selected_lecturer_option($conn, 'CIE', 'Manicaland', $current_assigned["Manicaland"]["first_supervisor_cie"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_eastern_cie"><?php selected_lecturer_option($conn, 'CIE', 'Manicaland', $current_assigned["Manicaland"]["second_supervisor_cie"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_eastern_edu"><?php selected_lecturer_option($conn, 'EDU', 'Manicaland', $current_assigned["Manicaland"]["first_supervisor_edu"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_eastern_edu"><?php selected_lecturer_option($conn, 'EDU', 'Manicaland', $current_assigned["Manicaland"]["second_supervisor_edu"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_eastern_eng"><?php selected_lecturer_option($conn, 'ENG', 'Manicaland', $current_assigned["Manicaland"]["first_supervisor_eng"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_eastern_eng"><?php selected_lecturer_option($conn, 'ENG', 'Manicaland', $current_assigned["Manicaland"]["second_supervisor_eng"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_eastern_law"><?php selected_lecturer_option($conn, 'LAW', 'Manicaland', $current_assigned["Manicaland"]["first_supervisor_law"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_eastern_law"><?php selected_lecturer_option($conn, 'LAW', 'Manicaland', $current_assigned["Manicaland"]["second_supervisor_law"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_eastern_med"><?php selected_lecturer_option($conn, 'MED', 'Manicaland', $current_assigned["Manicaland"]["first_supervisor_med"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_eastern_med"><?php selected_lecturer_option($conn, 'MED', 'Manicaland', $current_assigned["Manicaland"]["second_supervisor_med"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_eastern_sci"><?php selected_lecturer_option($conn, 'SCI', 'Manicaland', $current_assigned["Manicaland"]["first_supervisor_sci"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_eastern_sci"><?php selected_lecturer_option($conn, 'SCI', 'Manicaland', $current_assigned["Manicaland"]["second_supervisor_sci"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_eastern_soc"><?php selected_lecturer_option($conn, 'SOC', 'Manicaland', $current_assigned["Manicaland"]["first_supervisor_soc"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_eastern_soc"><?php selected_lecturer_option($conn, 'SOC', 'Manicaland', $current_assigned["Manicaland"]["second_supervisor_soc"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_eastern_vet"><?php selected_lecturer_option($conn, 'VET', 'Manicaland', $current_assigned["Manicaland"]["first_supervisor_vet"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_eastern_vet"><?php selected_lecturer_option($conn, 'VET', 'Manicaland', $current_assigned["Manicaland"]["second_supervisor_vet"] ?? ''); ?></select></td>
							  </tr>

							  <tr>

								<td>Mashonaland Central</td>  
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_western_agr"><?php selected_lecturer_option($conn, 'AGR', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["first_supervisor_agr"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_western_agr"><?php selected_lecturer_option($conn, 'AGR', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["second_supervisor_agr"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_western_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["first_supervisor_arts"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_western_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["second_supervisor_arts"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_western_com"><?php selected_lecturer_option($conn, 'COM', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["first_supervisor_com"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_western_com"><?php selected_lecturer_option($conn, 'COM', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["second_supervisor_com"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_western_cie"><?php selected_lecturer_option($conn, 'CIE', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["first_supervisor_cie"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_western_cie"><?php selected_lecturer_option($conn, 'CIE', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["second_supervisor_cie"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_western_edu"><?php selected_lecturer_option($conn, 'EDU', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["first_supervisor_edu"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_western_edu"><?php selected_lecturer_option($conn, 'EDU', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["second_supervisor_edu"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_western_eng"><?php selected_lecturer_option($conn, 'ENG', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["first_supervisor_eng"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_western_eng"><?php selected_lecturer_option($conn, 'ENG', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["second_supervisor_eng"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_western_law"><?php selected_lecturer_option($conn, 'LAW', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["first_supervisor_law"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_western_law"><?php selected_lecturer_option($conn, 'LAW', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["second_supervisor_law"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_western_med"><?php selected_lecturer_option($conn, 'MED', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["first_supervisor_med"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_western_med"><?php selected_lecturer_option($conn, 'MED', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["second_supervisor_med"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_western_sci"><?php selected_lecturer_option($conn, 'SCI', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["first_supervisor_sci"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_western_sci"><?php selected_lecturer_option($conn, 'SCI', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["second_supervisor_sci"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_western_soc"><?php selected_lecturer_option($conn, 'SOC', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["first_supervisor_soc"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_western_soc"><?php selected_lecturer_option($conn, 'SOC', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["second_supervisor_soc"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_western_vet"><?php selected_lecturer_option($conn, 'VET', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["first_supervisor_vet"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_western_vet"><?php selected_lecturer_option($conn, 'VET', 'Mashonaland Central', $current_assigned["Mashonaland Central"]["second_supervisor_vet"] ?? ''); ?></select></td>
							  </tr>

							  <tr>

								<td>Mashonaland East</td>  
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_ashanti_agr"><?php selected_lecturer_option($conn, 'AGR', 'Mashonaland East', $current_assigned["Mashonaland East"]["first_supervisor_agr"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_ashanti_agr"><?php selected_lecturer_option($conn, 'AGR', 'Mashonaland East', $current_assigned["Mashonaland East"]["second_supervisor_agr"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_ashanti_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Mashonaland East', $current_assigned["Mashonaland East"]["first_supervisor_arts"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_ashanti_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Mashonaland East', $current_assigned["Mashonaland East"]["second_supervisor_arts"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_ashanti_com"><?php selected_lecturer_option($conn, 'COM', 'Mashonaland East', $current_assigned["Mashonaland East"]["first_supervisor_com"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_ashanti_com"><?php selected_lecturer_option($conn, 'COM', 'Mashonaland East', $current_assigned["Mashonaland East"]["second_supervisor_com"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_ashanti_cie"><?php selected_lecturer_option($conn, 'CIE', 'Mashonaland East', $current_assigned["Mashonaland East"]["first_supervisor_cie"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_ashanti_cie"><?php selected_lecturer_option($conn, 'CIE', 'Mashonaland East', $current_assigned["Mashonaland East"]["second_supervisor_cie"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_ashanti_edu"><?php selected_lecturer_option($conn, 'EDU', 'Mashonaland East', $current_assigned["Mashonaland East"]["first_supervisor_edu"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_ashanti_edu"><?php selected_lecturer_option($conn, 'EDU', 'Mashonaland East', $current_assigned["Mashonaland East"]["second_supervisor_edu"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_ashanti_eng"><?php selected_lecturer_option($conn, 'ENG', 'Mashonaland East', $current_assigned["Mashonaland East"]["first_supervisor_eng"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_ashanti_eng"><?php selected_lecturer_option($conn, 'ENG', 'Mashonaland East', $current_assigned["Mashonaland East"]["second_supervisor_eng"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_ashanti_law"><?php selected_lecturer_option($conn, 'LAW', 'Mashonaland East', $current_assigned["Mashonaland East"]["first_supervisor_law"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_ashanti_law"><?php selected_lecturer_option($conn, 'LAW', 'Mashonaland East', $current_assigned["Mashonaland East"]["second_supervisor_law"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_ashanti_med"><?php selected_lecturer_option($conn, 'MED', 'Mashonaland East', $current_assigned["Mashonaland East"]["first_supervisor_med"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_ashanti_med"><?php selected_lecturer_option($conn, 'MED', 'Mashonaland East', $current_assigned["Mashonaland East"]["second_supervisor_med"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_ashanti_sci"><?php selected_lecturer_option($conn, 'SCI', 'Mashonaland East', $current_assigned["Mashonaland East"]["first_supervisor_sci"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_ashanti_sci"><?php selected_lecturer_option($conn, 'SCI', 'Mashonaland East', $current_assigned["Mashonaland East"]["second_supervisor_sci"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_ashanti_soc"><?php selected_lecturer_option($conn, 'SOC', 'Mashonaland East', $current_assigned["Mashonaland East"]["first_supervisor_soc"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_ashanti_soc"><?php selected_lecturer_option($conn, 'SOC', 'Mashonaland East', $current_assigned["Mashonaland East"]["second_supervisor_soc"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_ashanti_vet"><?php selected_lecturer_option($conn, 'VET', 'Mashonaland East', $current_assigned["Mashonaland East"]["first_supervisor_vet"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_ashanti_vet"><?php selected_lecturer_option($conn, 'VET', 'Mashonaland East', $current_assigned["Mashonaland East"]["second_supervisor_vet"] ?? ''); ?></select></td>
							  </tr>

							  <tr>

								<td>Mashonaland West</td>  
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_northern_agr"><?php selected_lecturer_option($conn, 'AGR', 'Mashonaland West', $current_assigned["Mashonaland West"]["first_supervisor_agr"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_northern_agr"><?php selected_lecturer_option($conn, 'AGR', 'Mashonaland West', $current_assigned["Mashonaland West"]["second_supervisor_agr"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_northern_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Mashonaland West', $current_assigned["Mashonaland West"]["first_supervisor_arts"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_northern_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Mashonaland West', $current_assigned["Mashonaland West"]["second_supervisor_arts"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_northern_com"><?php selected_lecturer_option($conn, 'COM', 'Mashonaland West', $current_assigned["Mashonaland West"]["first_supervisor_com"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_northern_com"><?php selected_lecturer_option($conn, 'COM', 'Mashonaland West', $current_assigned["Mashonaland West"]["second_supervisor_com"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_northern_cie"><?php selected_lecturer_option($conn, 'CIE', 'Mashonaland West', $current_assigned["Mashonaland West"]["first_supervisor_cie"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_northern_cie"><?php selected_lecturer_option($conn, 'CIE', 'Mashonaland West', $current_assigned["Mashonaland West"]["second_supervisor_cie"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_northern_edu"><?php selected_lecturer_option($conn, 'EDU', 'Mashonaland West', $current_assigned["Mashonaland West"]["first_supervisor_edu"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_northern_edu"><?php selected_lecturer_option($conn, 'EDU', 'Mashonaland West', $current_assigned["Mashonaland West"]["second_supervisor_edu"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_northern_eng"><?php selected_lecturer_option($conn, 'ENG', 'Mashonaland West', $current_assigned["Mashonaland West"]["first_supervisor_eng"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_northern_eng"><?php selected_lecturer_option($conn, 'ENG', 'Mashonaland West', $current_assigned["Mashonaland West"]["second_supervisor_eng"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_northern_law"><?php selected_lecturer_option($conn, 'LAW', 'Mashonaland West', $current_assigned["Mashonaland West"]["first_supervisor_law"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_northern_law"><?php selected_lecturer_option($conn, 'LAW', 'Mashonaland West', $current_assigned["Mashonaland West"]["second_supervisor_law"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_northern_med"><?php selected_lecturer_option($conn, 'MED', 'Mashonaland West', $current_assigned["Mashonaland West"]["first_supervisor_med"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_northern_med"><?php selected_lecturer_option($conn, 'MED', 'Mashonaland West', $current_assigned["Mashonaland West"]["second_supervisor_med"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_northern_sci"><?php selected_lecturer_option($conn, 'SCI', 'Mashonaland West', $current_assigned["Mashonaland West"]["first_supervisor_sci"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_northern_sci"><?php selected_lecturer_option($conn, 'SCI', 'Mashonaland West', $current_assigned["Mashonaland West"]["second_supervisor_sci"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_northern_soc"><?php selected_lecturer_option($conn, 'SOC', 'Mashonaland West', $current_assigned["Mashonaland West"]["first_supervisor_soc"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_northern_soc"><?php selected_lecturer_option($conn, 'SOC', 'Mashonaland West', $current_assigned["Mashonaland West"]["second_supervisor_soc"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_northern_vet"><?php selected_lecturer_option($conn, 'VET', 'Mashonaland West', $current_assigned["Mashonaland West"]["first_supervisor_vet"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_northern_vet"><?php selected_lecturer_option($conn, 'VET', 'Mashonaland West', $current_assigned["Mashonaland West"]["second_supervisor_vet"] ?? ''); ?></select></td>
							  </tr>

							  <tr>

								<td>Masvingo</td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_east_agr"><?php selected_lecturer_option($conn, 'AGR', 'Masvingo', $current_assigned["Masvingo"]["first_supervisor_agr"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_east_agr"><?php selected_lecturer_option($conn, 'AGR', 'Masvingo', $current_assigned["Masvingo"]["second_supervisor_agr"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_east_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Masvingo', $current_assigned["Masvingo"]["first_supervisor_arts"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_east_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Masvingo', $current_assigned["Masvingo"]["second_supervisor_arts"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_east_com"><?php selected_lecturer_option($conn, 'COM', 'Masvingo', $current_assigned["Masvingo"]["first_supervisor_com"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_east_com"><?php selected_lecturer_option($conn, 'COM', 'Masvingo', $current_assigned["Masvingo"]["second_supervisor_com"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_east_cie"><?php selected_lecturer_option($conn, 'CIE', 'Masvingo', $current_assigned["Masvingo"]["first_supervisor_cie"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_east_cie"><?php selected_lecturer_option($conn, 'CIE', 'Masvingo', $current_assigned["Masvingo"]["second_supervisor_cie"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_east_edu"><?php selected_lecturer_option($conn, 'EDU', 'Masvingo', $current_assigned["Masvingo"]["first_supervisor_edu"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_east_edu"><?php selected_lecturer_option($conn, 'EDU', 'Masvingo', $current_assigned["Masvingo"]["second_supervisor_edu"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_east_eng"><?php selected_lecturer_option($conn, 'ENG', 'Masvingo', $current_assigned["Masvingo"]["first_supervisor_eng"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_east_eng"><?php selected_lecturer_option($conn, 'ENG', 'Masvingo', $current_assigned["Masvingo"]["second_supervisor_eng"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_east_law"><?php selected_lecturer_option($conn, 'LAW', 'Masvingo', $current_assigned["Masvingo"]["first_supervisor_law"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_east_law"><?php selected_lecturer_option($conn, 'LAW', 'Masvingo', $current_assigned["Masvingo"]["second_supervisor_law"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_east_med"><?php selected_lecturer_option($conn, 'MED', 'Masvingo', $current_assigned["Masvingo"]["first_supervisor_med"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_east_med"><?php selected_lecturer_option($conn, 'MED', 'Masvingo', $current_assigned["Masvingo"]["second_supervisor_med"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_east_sci"><?php selected_lecturer_option($conn, 'SCI', 'Masvingo', $current_assigned["Masvingo"]["first_supervisor_sci"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_east_sci"><?php selected_lecturer_option($conn, 'SCI', 'Masvingo', $current_assigned["Masvingo"]["second_supervisor_sci"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_east_soc"><?php selected_lecturer_option($conn, 'SOC', 'Masvingo', $current_assigned["Masvingo"]["first_supervisor_soc"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_east_soc"><?php selected_lecturer_option($conn, 'SOC', 'Masvingo', $current_assigned["Masvingo"]["second_supervisor_soc"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_east_vet"><?php selected_lecturer_option($conn, 'VET', 'Masvingo', $current_assigned["Masvingo"]["first_supervisor_vet"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_east_vet"><?php selected_lecturer_option($conn, 'VET', 'Masvingo', $current_assigned["Masvingo"]["second_supervisor_vet"] ?? ''); ?></select></td>
							  </tr>

							  <tr>

								<td>Matabeleland North</td>  
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_west_agr"><?php selected_lecturer_option($conn, 'AGR', 'Matabeleland North', $current_assigned["Matabeleland North"]["first_supervisor_agr"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_west_agr"><?php selected_lecturer_option($conn, 'AGR', 'Matabeleland North', $current_assigned["Matabeleland North"]["second_supervisor_agr"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_west_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Matabeleland North', $current_assigned["Matabeleland North"]["first_supervisor_arts"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_west_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Matabeleland North', $current_assigned["Matabeleland North"]["second_supervisor_arts"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_west_com"><?php selected_lecturer_option($conn, 'COM', 'Matabeleland North', $current_assigned["Matabeleland North"]["first_supervisor_com"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_west_com"><?php selected_lecturer_option($conn, 'COM', 'Matabeleland North', $current_assigned["Matabeleland North"]["second_supervisor_com"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_west_cie"><?php selected_lecturer_option($conn, 'CIE', 'Matabeleland North', $current_assigned["Matabeleland North"]["first_supervisor_cie"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_west_cie"><?php selected_lecturer_option($conn, 'CIE', 'Matabeleland North', $current_assigned["Matabeleland North"]["second_supervisor_cie"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_west_edu"><?php selected_lecturer_option($conn, 'EDU', 'Matabeleland North', $current_assigned["Matabeleland North"]["first_supervisor_edu"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_west_edu"><?php selected_lecturer_option($conn, 'EDU', 'Matabeleland North', $current_assigned["Matabeleland North"]["second_supervisor_edu"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_west_eng"><?php selected_lecturer_option($conn, 'ENG', 'Matabeleland North', $current_assigned["Matabeleland North"]["first_supervisor_eng"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_west_eng"><?php selected_lecturer_option($conn, 'ENG', 'Matabeleland North', $current_assigned["Matabeleland North"]["second_supervisor_eng"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_west_law"><?php selected_lecturer_option($conn, 'LAW', 'Matabeleland North', $current_assigned["Matabeleland North"]["first_supervisor_law"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_west_law"><?php selected_lecturer_option($conn, 'LAW', 'Matabeleland North', $current_assigned["Matabeleland North"]["second_supervisor_law"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_west_med"><?php selected_lecturer_option($conn, 'MED', 'Matabeleland North', $current_assigned["Matabeleland North"]["first_supervisor_med"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_west_med"><?php selected_lecturer_option($conn, 'MED', 'Matabeleland North', $current_assigned["Matabeleland North"]["second_supervisor_med"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_west_sci"><?php selected_lecturer_option($conn, 'SCI', 'Matabeleland North', $current_assigned["Matabeleland North"]["first_supervisor_sci"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_west_sci"><?php selected_lecturer_option($conn, 'SCI', 'Matabeleland North', $current_assigned["Matabeleland North"]["second_supervisor_sci"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_west_soc"><?php selected_lecturer_option($conn, 'SOC', 'Matabeleland North', $current_assigned["Matabeleland North"]["first_supervisor_soc"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_west_soc"><?php selected_lecturer_option($conn, 'SOC', 'Matabeleland North', $current_assigned["Matabeleland North"]["second_supervisor_soc"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_upper_west_vet"><?php selected_lecturer_option($conn, 'VET', 'Matabeleland North', $current_assigned["Matabeleland North"]["first_supervisor_vet"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_upper_west_vet"><?php selected_lecturer_option($conn, 'VET', 'Matabeleland North', $current_assigned["Matabeleland North"]["second_supervisor_vet"] ?? ''); ?></select></td>
							  </tr>

							  <tr>

								<td>Matabeleland South</td>  
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_volta_agr"><?php selected_lecturer_option($conn, 'AGR', 'Matabeleland South', $current_assigned["Matabeleland South"]["first_supervisor_agr"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_volta_agr"><?php selected_lecturer_option($conn, 'AGR', 'Matabeleland South', $current_assigned["Matabeleland South"]["second_supervisor_agr"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_volta_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Matabeleland South', $current_assigned["Matabeleland South"]["first_supervisor_arts"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_volta_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Matabeleland South', $current_assigned["Matabeleland South"]["second_supervisor_arts"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_volta_com"><?php selected_lecturer_option($conn, 'COM', 'Matabeleland South', $current_assigned["Matabeleland South"]["first_supervisor_com"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_volta_com"><?php selected_lecturer_option($conn, 'COM', 'Matabeleland South', $current_assigned["Matabeleland South"]["second_supervisor_com"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_volta_cie"><?php selected_lecturer_option($conn, 'CIE', 'Matabeleland South', $current_assigned["Matabeleland South"]["first_supervisor_cie"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_volta_cie"><?php selected_lecturer_option($conn, 'CIE', 'Matabeleland South', $current_assigned["Matabeleland South"]["second_supervisor_cie"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_volta_edu"><?php selected_lecturer_option($conn, 'EDU', 'Matabeleland South', $current_assigned["Matabeleland South"]["first_supervisor_edu"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_volta_edu"><?php selected_lecturer_option($conn, 'EDU', 'Matabeleland South', $current_assigned["Matabeleland South"]["second_supervisor_edu"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_volta_eng"><?php selected_lecturer_option($conn, 'ENG', 'Matabeleland South', $current_assigned["Matabeleland South"]["first_supervisor_eng"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_volta_eng"><?php selected_lecturer_option($conn, 'ENG', 'Matabeleland South', $current_assigned["Matabeleland South"]["second_supervisor_eng"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_volta_law"><?php selected_lecturer_option($conn, 'LAW', 'Matabeleland South', $current_assigned["Matabeleland South"]["first_supervisor_law"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_volta_law"><?php selected_lecturer_option($conn, 'LAW', 'Matabeleland South', $current_assigned["Matabeleland South"]["second_supervisor_law"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_volta_med"><?php selected_lecturer_option($conn, 'MED', 'Matabeleland South', $current_assigned["Matabeleland South"]["first_supervisor_med"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_volta_med"><?php selected_lecturer_option($conn, 'MED', 'Matabeleland South', $current_assigned["Matabeleland South"]["second_supervisor_med"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_volta_sci"><?php selected_lecturer_option($conn, 'SCI', 'Matabeleland South', $current_assigned["Matabeleland South"]["first_supervisor_sci"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_volta_sci"><?php selected_lecturer_option($conn, 'SCI', 'Matabeleland South', $current_assigned["Matabeleland South"]["second_supervisor_sci"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_volta_soc"><?php selected_lecturer_option($conn, 'SOC', 'Matabeleland South', $current_assigned["Matabeleland South"]["first_supervisor_soc"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_volta_soc"><?php selected_lecturer_option($conn, 'SOC', 'Matabeleland South', $current_assigned["Matabeleland South"]["second_supervisor_soc"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_volta_vet"><?php selected_lecturer_option($conn, 'VET', 'Matabeleland South', $current_assigned["Matabeleland South"]["first_supervisor_vet"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_volta_vet"><?php selected_lecturer_option($conn, 'VET', 'Matabeleland South', $current_assigned["Matabeleland South"]["second_supervisor_vet"] ?? ''); ?></select></td>
							  </tr>

							  <tr>

								<td>Midlands</td>  
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_brong_agr"><?php selected_lecturer_option($conn, 'AGR', 'Midlands', $current_assigned["Midlands"]["first_supervisor_agr"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_brong_agr"><?php selected_lecturer_option($conn, 'AGR', 'Midlands', $current_assigned["Midlands"]["second_supervisor_agr"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_brong_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Midlands', $current_assigned["Midlands"]["first_supervisor_arts"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_brong_arts"><?php selected_lecturer_option($conn, 'ARTS', 'Midlands', $current_assigned["Midlands"]["second_supervisor_arts"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_brong_com"><?php selected_lecturer_option($conn, 'COM', 'Midlands', $current_assigned["Midlands"]["first_supervisor_com"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_brong_com"><?php selected_lecturer_option($conn, 'COM', 'Midlands', $current_assigned["Midlands"]["second_supervisor_com"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_brong_cie"><?php selected_lecturer_option($conn, 'CIE', 'Midlands', $current_assigned["Midlands"]["first_supervisor_cie"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_brong_cie"><?php selected_lecturer_option($conn, 'CIE', 'Midlands', $current_assigned["Midlands"]["second_supervisor_cie"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_brong_edu"><?php selected_lecturer_option($conn, 'EDU', 'Midlands', $current_assigned["Midlands"]["first_supervisor_edu"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_brong_edu"><?php selected_lecturer_option($conn, 'EDU', 'Midlands', $current_assigned["Midlands"]["second_supervisor_edu"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_brong_eng"><?php selected_lecturer_option($conn, 'ENG', 'Midlands', $current_assigned["Midlands"]["first_supervisor_eng"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_brong_eng"><?php selected_lecturer_option($conn, 'ENG', 'Midlands', $current_assigned["Midlands"]["second_supervisor_eng"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_brong_law"><?php selected_lecturer_option($conn, 'LAW', 'Midlands', $current_assigned["Midlands"]["first_supervisor_law"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_brong_law"><?php selected_lecturer_option($conn, 'LAW', 'Midlands', $current_assigned["Midlands"]["second_supervisor_law"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_brong_med"><?php selected_lecturer_option($conn, 'MED', 'Midlands', $current_assigned["Midlands"]["first_supervisor_med"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_brong_med"><?php selected_lecturer_option($conn, 'MED', 'Midlands', $current_assigned["Midlands"]["second_supervisor_med"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_brong_sci"><?php selected_lecturer_option($conn, 'SCI', 'Midlands', $current_assigned["Midlands"]["first_supervisor_sci"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_brong_sci"><?php selected_lecturer_option($conn, 'SCI', 'Midlands', $current_assigned["Midlands"]["second_supervisor_sci"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_brong_soc"><?php selected_lecturer_option($conn, 'SOC', 'Midlands', $current_assigned["Midlands"]["first_supervisor_soc"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_brong_soc"><?php selected_lecturer_option($conn, 'SOC', 'Midlands', $current_assigned["Midlands"]["second_supervisor_soc"] ?? ''); ?></select></td>
								<td><select class="form-control form-control-adjusted" name="selected_lecturer_1_brong_vet"><?php selected_lecturer_option($conn, 'VET', 'Midlands', $current_assigned["Midlands"]["first_supervisor_vet"] ?? ''); ?></select><select class="form-control form-control-adjusted" name="selected_lecturer_2_brong_vet"><?php selected_lecturer_option($conn, 'VET', 'Midlands', $current_assigned["Midlands"]["second_supervisor_vet"] ?? ''); ?></select></td>
							  </tr>

							  </tbody>
							  
						</table>

						  <div style="float: right">
						<input type="submit" name="btn_assign_lecturers" class="btn btn-primary" value="Assign Supervisors"/>
						</div>
						
						</form>
				 </div>
				</div>
			 </div>

			<?php

				if(isset($_POST["btn_assign_lecturers"])){
					$p = $_POST;
					$fc = array('agr','arts','com','cie','edu','eng','law','med','sci','soc','vet');
					$regions_post = array('accra','central','eastern','western','ashanti','northern','upper_east','upper_west','volta','brong');
					foreach ($regions_post as $r) {
						foreach ($fc as $f) {
							${"lecturer_1_{$r}_{$f}"} = $p["selected_lecturer_1_{$r}_{$f}"] ?? '';
							${"lecturer_2_{$r}_{$f}"} = $p["selected_lecturer_2_{$r}_{$f}"] ?? '';
						}
					}
					$check_database_table_for_assignment = "SELECT * FROM assigned_lecturers WHERE id='1' LIMIT 1";
					$execute_check_database = mysqli_query($conn,$check_database_table_for_assignment);
					$get_check_database_details = mysqli_num_rows($execute_check_database);
					
					if($get_check_database_details == 1){
						
						
					$mysql_update_accra_query = "UPDATE `assigned_lecturers` SET `first_supervisor_agr` = '$lecturer_1_accra_agr', `second_supervisor_agr` = '$lecturer_2_accra_agr', `first_supervisor_arts` = '$lecturer_1_accra_arts', `second_supervisor_arts` = '$lecturer_2_accra_arts', `first_supervisor_com` = '$lecturer_1_accra_com', `second_supervisor_com` = '$lecturer_2_accra_com', `first_supervisor_cie` = '$lecturer_1_accra_cie', `second_supervisor_cie` = '$lecturer_2_accra_cie', `first_supervisor_edu` = '$lecturer_1_accra_edu', `second_supervisor_edu` = '$lecturer_2_accra_edu', `first_supervisor_eng` = '$lecturer_1_accra_eng', `second_supervisor_eng` = '$lecturer_2_accra_eng', `first_supervisor_law` = '$lecturer_1_accra_law', `second_supervisor_law` = '$lecturer_2_accra_law', `first_supervisor_med` = '$lecturer_1_accra_med', `second_supervisor_med` = '$lecturer_2_accra_med', `first_supervisor_sci` = '$lecturer_1_accra_sci', `second_supervisor_sci` = '$lecturer_2_accra_sci', `first_supervisor_soc` = '$lecturer_1_accra_soc', `second_supervisor_soc` = '$lecturer_2_accra_soc', `first_supervisor_vet` = '$lecturer_1_accra_vet', `second_supervisor_vet` = '$lecturer_2_accra_vet' WHERE `regions` = 'Bulawayo'"; 			
					
					$execute_update_for_accra_query = mysqli_query($conn,$mysql_update_accra_query);
						
						//
					
					
					$mysql_update_central_query = "UPDATE `assigned_lecturers` SET `first_supervisor_agr` = '$lecturer_1_central_agr', `second_supervisor_agr` = '$lecturer_2_central_agr', `first_supervisor_arts` = '$lecturer_1_central_arts', `second_supervisor_arts` = '$lecturer_2_central_arts', `first_supervisor_com` = '$lecturer_1_central_com', `second_supervisor_com` = '$lecturer_2_central_com', `first_supervisor_cie` = '$lecturer_1_central_cie', `second_supervisor_cie` = '$lecturer_2_central_cie', `first_supervisor_edu` = '$lecturer_1_central_edu', `second_supervisor_edu` = '$lecturer_2_central_edu', `first_supervisor_eng` = '$lecturer_1_central_eng', `second_supervisor_eng` = '$lecturer_2_central_eng', `first_supervisor_law` = '$lecturer_1_central_law', `second_supervisor_law` = '$lecturer_2_central_law', `first_supervisor_med` = '$lecturer_1_central_med', `second_supervisor_med` = '$lecturer_2_central_med', `first_supervisor_sci` = '$lecturer_1_central_sci', `second_supervisor_sci` = '$lecturer_2_central_sci', `first_supervisor_soc` = '$lecturer_1_central_soc', `second_supervisor_soc` = '$lecturer_2_central_soc', `first_supervisor_vet` = '$lecturer_1_central_vet', `second_supervisor_vet` = '$lecturer_2_central_vet' WHERE `regions` = 'Harare'"; 			
					
					$execute_update_for_central_query = mysqli_query($conn,$mysql_update_central_query);
						
						//
				
					$mysql_update_eastern_query = "UPDATE `assigned_lecturers` SET `first_supervisor_agr` = '$lecturer_1_eastern_agr', `second_supervisor_agr` = '$lecturer_2_eastern_agr', `first_supervisor_arts` = '$lecturer_1_eastern_arts', `second_supervisor_arts` = '$lecturer_2_eastern_arts', `first_supervisor_com` = '$lecturer_1_eastern_com', `second_supervisor_com` = '$lecturer_2_eastern_com', `first_supervisor_cie` = '$lecturer_1_eastern_cie', `second_supervisor_cie` = '$lecturer_2_eastern_cie', `first_supervisor_edu` = '$lecturer_1_eastern_edu', `second_supervisor_edu` = '$lecturer_2_eastern_edu', `first_supervisor_eng` = '$lecturer_1_eastern_eng', `second_supervisor_eng` = '$lecturer_2_eastern_eng', `first_supervisor_law` = '$lecturer_1_eastern_law', `second_supervisor_law` = '$lecturer_2_eastern_law', `first_supervisor_med` = '$lecturer_1_eastern_med', `second_supervisor_med` = '$lecturer_2_eastern_med', `first_supervisor_sci` = '$lecturer_1_eastern_sci', `second_supervisor_sci` = '$lecturer_2_eastern_sci', `first_supervisor_soc` = '$lecturer_1_eastern_soc', `second_supervisor_soc` = '$lecturer_2_eastern_soc', `first_supervisor_vet` = '$lecturer_1_eastern_vet', `second_supervisor_vet` = '$lecturer_2_eastern_vet' WHERE `regions` = 'Manicaland'"; 
					
					$execute_update_for_eastern_query = mysqli_query($conn,$mysql_update_eastern_query);
						
						//
					
					
					$mysql_update_western_query = "UPDATE `assigned_lecturers` SET `first_supervisor_agr` = '$lecturer_1_western_agr', `second_supervisor_agr` = '$lecturer_2_western_agr', `first_supervisor_arts` = '$lecturer_1_western_arts', `second_supervisor_arts` = '$lecturer_2_western_arts', `first_supervisor_com` = '$lecturer_1_western_com', `second_supervisor_com` = '$lecturer_2_western_com', `first_supervisor_cie` = '$lecturer_1_western_cie', `second_supervisor_cie` = '$lecturer_2_western_cie', `first_supervisor_edu` = '$lecturer_1_western_edu', `second_supervisor_edu` = '$lecturer_2_western_edu', `first_supervisor_eng` = '$lecturer_1_western_eng', `second_supervisor_eng` = '$lecturer_2_western_eng', `first_supervisor_law` = '$lecturer_1_western_law', `second_supervisor_law` = '$lecturer_2_western_law', `first_supervisor_med` = '$lecturer_1_western_med', `second_supervisor_med` = '$lecturer_2_western_med', `first_supervisor_sci` = '$lecturer_1_western_sci', `second_supervisor_sci` = '$lecturer_2_western_sci', `first_supervisor_soc` = '$lecturer_1_western_soc', `second_supervisor_soc` = '$lecturer_2_western_soc', `first_supervisor_vet` = '$lecturer_1_western_vet', `second_supervisor_vet` = '$lecturer_2_western_vet' WHERE `regions` = 'Mashonaland Central'"; 
								
					$execute_update_for_western_query = mysqli_query($conn,$mysql_update_western_query);
						
						//
					
					
					$mysql_update_ashanti_query = "UPDATE `assigned_lecturers` SET `first_supervisor_agr` = '$lecturer_1_ashanti_agr', `second_supervisor_agr` = '$lecturer_2_ashanti_agr', `first_supervisor_arts` = '$lecturer_1_ashanti_arts', `second_supervisor_arts` = '$lecturer_2_ashanti_arts', `first_supervisor_com` = '$lecturer_1_ashanti_com', `second_supervisor_com` = '$lecturer_2_ashanti_com', `first_supervisor_cie` = '$lecturer_1_ashanti_cie', `second_supervisor_cie` = '$lecturer_2_ashanti_cie', `first_supervisor_edu` = '$lecturer_1_ashanti_edu', `second_supervisor_edu` = '$lecturer_2_ashanti_edu', `first_supervisor_eng` = '$lecturer_1_ashanti_eng', `second_supervisor_eng` = '$lecturer_2_ashanti_eng', `first_supervisor_law` = '$lecturer_1_ashanti_law', `second_supervisor_law` = '$lecturer_2_ashanti_law', `first_supervisor_med` = '$lecturer_1_ashanti_med', `second_supervisor_med` = '$lecturer_2_ashanti_med', `first_supervisor_sci` = '$lecturer_1_ashanti_sci', `second_supervisor_sci` = '$lecturer_2_ashanti_sci', `first_supervisor_soc` = '$lecturer_1_ashanti_soc', `second_supervisor_soc` = '$lecturer_2_ashanti_soc', `first_supervisor_vet` = '$lecturer_1_ashanti_vet', `second_supervisor_vet` = '$lecturer_2_ashanti_vet' WHERE `regions` = 'Mashonaland East'"; 
								
					$execute_update_for_ashanti_query = mysqli_query($conn,$mysql_update_ashanti_query);
						
						//
					
					
					$mysql_update_northern_query = "UPDATE `assigned_lecturers` SET `first_supervisor_agr` = '$lecturer_1_northern_agr', `second_supervisor_agr` = '$lecturer_2_northern_agr', `first_supervisor_arts` = '$lecturer_1_northern_arts', `second_supervisor_arts` = '$lecturer_2_northern_arts', `first_supervisor_com` = '$lecturer_1_northern_com', `second_supervisor_com` = '$lecturer_2_northern_com', `first_supervisor_cie` = '$lecturer_1_northern_cie', `second_supervisor_cie` = '$lecturer_2_northern_cie', `first_supervisor_edu` = '$lecturer_1_northern_edu', `second_supervisor_edu` = '$lecturer_2_northern_edu', `first_supervisor_eng` = '$lecturer_1_northern_eng', `second_supervisor_eng` = '$lecturer_2_northern_eng', `first_supervisor_law` = '$lecturer_1_northern_law', `second_supervisor_law` = '$lecturer_2_northern_law', `first_supervisor_med` = '$lecturer_1_northern_med', `second_supervisor_med` = '$lecturer_2_northern_med', `first_supervisor_sci` = '$lecturer_1_northern_sci', `second_supervisor_sci` = '$lecturer_2_northern_sci', `first_supervisor_soc` = '$lecturer_1_northern_soc', `second_supervisor_soc` = '$lecturer_2_northern_soc', `first_supervisor_vet` = '$lecturer_1_northern_vet', `second_supervisor_vet` = '$lecturer_2_northern_vet' WHERE `regions` = 'Mashonaland West'"; 
								
					$execute_update_for_northern_query = mysqli_query($conn,$mysql_update_northern_query);
						
						//
					
					
					$mysql_update_upper_east_query = "UPDATE `assigned_lecturers` SET `first_supervisor_agr` = '$lecturer_1_upper_east_agr', `second_supervisor_agr` = '$lecturer_2_upper_east_agr', `first_supervisor_arts` = '$lecturer_1_upper_east_arts', `second_supervisor_arts` = '$lecturer_2_upper_east_arts', `first_supervisor_com` = '$lecturer_1_upper_east_com', `second_supervisor_com` = '$lecturer_2_upper_east_com', `first_supervisor_cie` = '$lecturer_1_upper_east_cie', `second_supervisor_cie` = '$lecturer_2_upper_east_cie', `first_supervisor_edu` = '$lecturer_1_upper_east_edu', `second_supervisor_edu` = '$lecturer_2_upper_east_edu', `first_supervisor_eng` = '$lecturer_1_upper_east_eng', `second_supervisor_eng` = '$lecturer_2_upper_east_eng', `first_supervisor_law` = '$lecturer_1_upper_east_law', `second_supervisor_law` = '$lecturer_2_upper_east_law', `first_supervisor_med` = '$lecturer_1_upper_east_med', `second_supervisor_med` = '$lecturer_2_upper_east_med', `first_supervisor_sci` = '$lecturer_1_upper_east_sci', `second_supervisor_sci` = '$lecturer_2_upper_east_sci', `first_supervisor_soc` = '$lecturer_1_upper_east_soc', `second_supervisor_soc` = '$lecturer_2_upper_east_soc', `first_supervisor_vet` = '$lecturer_1_upper_east_vet', `second_supervisor_vet` = '$lecturer_2_upper_east_vet' WHERE `regions` = 'Masvingo'"; 
								
					$execute_update_for_upper_east_query = mysqli_query($conn,$mysql_update_upper_east_query);
						
						//
					
					
					$mysql_update_upper_west_query = "UPDATE `assigned_lecturers` SET `first_supervisor_agr` = '$lecturer_1_upper_west_agr', `second_supervisor_agr` = '$lecturer_2_upper_west_agr', `first_supervisor_arts` = '$lecturer_1_upper_west_arts', `second_supervisor_arts` = '$lecturer_2_upper_west_arts', `first_supervisor_com` = '$lecturer_1_upper_west_com', `second_supervisor_com` = '$lecturer_2_upper_west_com', `first_supervisor_cie` = '$lecturer_1_upper_west_cie', `second_supervisor_cie` = '$lecturer_2_upper_west_cie', `first_supervisor_edu` = '$lecturer_1_upper_west_edu', `second_supervisor_edu` = '$lecturer_2_upper_west_edu', `first_supervisor_eng` = '$lecturer_1_upper_west_eng', `second_supervisor_eng` = '$lecturer_2_upper_west_eng', `first_supervisor_law` = '$lecturer_1_upper_west_law', `second_supervisor_law` = '$lecturer_2_upper_west_law', `first_supervisor_med` = '$lecturer_1_upper_west_med', `second_supervisor_med` = '$lecturer_2_upper_west_med', `first_supervisor_sci` = '$lecturer_1_upper_west_sci', `second_supervisor_sci` = '$lecturer_2_upper_west_sci', `first_supervisor_soc` = '$lecturer_1_upper_west_soc', `second_supervisor_soc` = '$lecturer_2_upper_west_soc', `first_supervisor_vet` = '$lecturer_1_upper_west_vet', `second_supervisor_vet` = '$lecturer_2_upper_west_vet' WHERE `regions` = 'Matabeleland North'"; 
								
					$execute_update_for_upper_west_query = mysqli_query($conn,$mysql_update_upper_west_query);
						
						//
					
					
					$mysql_update_volta_query = "UPDATE `assigned_lecturers` SET `first_supervisor_agr` = '$lecturer_1_volta_agr', `second_supervisor_agr` = '$lecturer_2_volta_agr', `first_supervisor_arts` = '$lecturer_1_volta_arts', `second_supervisor_arts` = '$lecturer_2_volta_arts', `first_supervisor_com` = '$lecturer_1_volta_com', `second_supervisor_com` = '$lecturer_2_volta_com', `first_supervisor_cie` = '$lecturer_1_volta_cie', `second_supervisor_cie` = '$lecturer_2_volta_cie', `first_supervisor_edu` = '$lecturer_1_volta_edu', `second_supervisor_edu` = '$lecturer_2_volta_edu', `first_supervisor_eng` = '$lecturer_1_volta_eng', `second_supervisor_eng` = '$lecturer_2_volta_eng', `first_supervisor_law` = '$lecturer_1_volta_law', `second_supervisor_law` = '$lecturer_2_volta_law', `first_supervisor_med` = '$lecturer_1_volta_med', `second_supervisor_med` = '$lecturer_2_volta_med', `first_supervisor_sci` = '$lecturer_1_volta_sci', `second_supervisor_sci` = '$lecturer_2_volta_sci', `first_supervisor_soc` = '$lecturer_1_volta_soc', `second_supervisor_soc` = '$lecturer_2_volta_soc', `first_supervisor_vet` = '$lecturer_1_volta_vet', `second_supervisor_vet` = '$lecturer_2_volta_vet' WHERE `regions` = 'Matabeleland South'"; 
							
					$execute_update_for_volta_query = mysqli_query($conn,$mysql_update_volta_query);
						
						//
										
					$mysql_update_brong_query = "UPDATE `assigned_lecturers` SET `first_supervisor_agr` = '$lecturer_1_brong_agr', `second_supervisor_agr` = '$lecturer_2_brong_agr', `first_supervisor_arts` = '$lecturer_1_brong_arts', `second_supervisor_arts` = '$lecturer_2_brong_arts', `first_supervisor_com` = '$lecturer_1_brong_com', `second_supervisor_com` = '$lecturer_2_brong_com', `first_supervisor_cie` = '$lecturer_1_brong_cie', `second_supervisor_cie` = '$lecturer_2_brong_cie', `first_supervisor_edu` = '$lecturer_1_brong_edu', `second_supervisor_edu` = '$lecturer_2_brong_edu', `first_supervisor_eng` = '$lecturer_1_brong_eng', `second_supervisor_eng` = '$lecturer_2_brong_eng', `first_supervisor_law` = '$lecturer_1_brong_law', `second_supervisor_law` = '$lecturer_2_brong_law', `first_supervisor_med` = '$lecturer_1_brong_med', `second_supervisor_med` = '$lecturer_2_brong_med', `first_supervisor_sci` = '$lecturer_1_brong_sci', `second_supervisor_sci` = '$lecturer_2_brong_sci', `first_supervisor_soc` = '$lecturer_1_brong_soc', `second_supervisor_soc` = '$lecturer_2_brong_soc', `first_supervisor_vet` = '$lecturer_1_brong_vet', `second_supervisor_vet` = '$lecturer_2_brong_vet' WHERE `regions` = 'Midlands'"; 
							
					$execute_update_for_brong_query = mysqli_query($conn,$mysql_update_brong_query);
											
											
					}else{


					$mysql_insert_accra_query = "INSERT INTO `assigned_lecturers` (`id`, `regions`, `first_supervisor_agr`, `second_supervisor_agr`, `first_supervisor_arts`, `second_supervisor_arts`, `first_supervisor_com`, `second_supervisor_com`, `first_supervisor_cie`, `second_supervisor_cie`, `first_supervisor_edu`, `second_supervisor_edu`, `first_supervisor_eng`, `second_supervisor_eng`, `first_supervisor_law`, `second_supervisor_law`, `first_supervisor_med`, `second_supervisor_med`, `first_supervisor_sci`, `second_supervisor_sci`, `first_supervisor_soc`, `second_supervisor_soc`, `first_supervisor_vet`, `second_supervisor_vet`, `date`) VALUES (NULL, 'Bulawayo', '$lecturer_1_accra_agr', '$lecturer_2_accra_agr', '$lecturer_1_accra_arts', '$lecturer_2_accra_arts', '$lecturer_1_accra_com', '$lecturer_2_accra_com', '$lecturer_1_accra_cie', '$lecturer_2_accra_cie', '$lecturer_1_accra_edu', '$lecturer_2_accra_edu', '$lecturer_1_accra_eng', '$lecturer_2_accra_eng', '$lecturer_1_accra_law', '$lecturer_2_accra_law', '$lecturer_1_accra_med', '$lecturer_2_accra_med', '$lecturer_1_accra_sci', '$lecturer_2_accra_sci', '$lecturer_1_accra_soc', '$lecturer_2_accra_soc', '$lecturer_1_accra_vet', '$lecturer_2_accra_vet', CURRENT_TIMESTAMP)"; 			
					
					$execute_insert_for_accra_query = mysqli_query($conn,$mysql_insert_accra_query);
						
						//
					
					
					$mysql_insert_central_query = "INSERT INTO `assigned_lecturers` (`id`, `regions`, `first_supervisor_agr`, `second_supervisor_agr`, `first_supervisor_arts`, `second_supervisor_arts`, `first_supervisor_com`, `second_supervisor_com`, `first_supervisor_cie`, `second_supervisor_cie`, `first_supervisor_edu`, `second_supervisor_edu`, `first_supervisor_eng`, `second_supervisor_eng`, `first_supervisor_law`, `second_supervisor_law`, `first_supervisor_med`, `second_supervisor_med`, `first_supervisor_sci`, `second_supervisor_sci`, `first_supervisor_soc`, `second_supervisor_soc`, `first_supervisor_vet`, `second_supervisor_vet`, `date`) VALUES (NULL, 'Harare', '$lecturer_1_central_agr', '$lecturer_2_central_agr', '$lecturer_1_central_arts', '$lecturer_2_central_arts', '$lecturer_1_central_com', '$lecturer_2_central_com', '$lecturer_1_central_cie', '$lecturer_2_central_cie', '$lecturer_1_central_edu', '$lecturer_2_central_edu', '$lecturer_1_central_eng', '$lecturer_2_central_eng', '$lecturer_1_central_law', '$lecturer_2_central_law', '$lecturer_1_central_med', '$lecturer_2_central_med', '$lecturer_1_central_sci', '$lecturer_2_central_sci', '$lecturer_1_central_soc', '$lecturer_2_central_soc', '$lecturer_1_central_vet', '$lecturer_2_central_vet', CURRENT_TIMESTAMP)"; 			
					
					$execute_insert_for_central_query = mysqli_query($conn,$mysql_insert_central_query);
						
						//
					
					
					$mysql_insert_eastern_query = "INSERT INTO `assigned_lecturers` (`id`, `regions`, `first_supervisor_agr`, `second_supervisor_agr`, `first_supervisor_arts`, `second_supervisor_arts`, `first_supervisor_com`, `second_supervisor_com`, `first_supervisor_cie`, `second_supervisor_cie`, `first_supervisor_edu`, `second_supervisor_edu`, `first_supervisor_eng`, `second_supervisor_eng`, `first_supervisor_law`, `second_supervisor_law`, `first_supervisor_med`, `second_supervisor_med`, `first_supervisor_sci`, `second_supervisor_sci`, `first_supervisor_soc`, `second_supervisor_soc`, `first_supervisor_vet`, `second_supervisor_vet`, `date`) VALUES (NULL, 'Manicaland', '$lecturer_1_eastern_agr', '$lecturer_2_eastern_agr', '$lecturer_1_eastern_arts', '$lecturer_2_eastern_arts', '$lecturer_1_eastern_com', '$lecturer_2_eastern_com', '$lecturer_1_eastern_cie', '$lecturer_2_eastern_cie', '$lecturer_1_eastern_edu', '$lecturer_2_eastern_edu', '$lecturer_1_eastern_eng', '$lecturer_2_eastern_eng', '$lecturer_1_eastern_law', '$lecturer_2_eastern_law', '$lecturer_1_eastern_med', '$lecturer_2_eastern_med', '$lecturer_1_eastern_sci', '$lecturer_2_eastern_sci', '$lecturer_1_eastern_soc', '$lecturer_2_eastern_soc', '$lecturer_1_eastern_vet', '$lecturer_2_eastern_vet', CURRENT_TIMESTAMP)"; 
					
					$execute_insert_for_eastern_query = mysqli_query($conn,$mysql_insert_eastern_query);
						
						//
					
					
					$mysql_insert_western_query = "INSERT INTO `assigned_lecturers` (`id`, `regions`, `first_supervisor_agr`, `second_supervisor_agr`, `first_supervisor_arts`, `second_supervisor_arts`, `first_supervisor_com`, `second_supervisor_com`, `first_supervisor_cie`, `second_supervisor_cie`, `first_supervisor_edu`, `second_supervisor_edu`, `first_supervisor_eng`, `second_supervisor_eng`, `first_supervisor_law`, `second_supervisor_law`, `first_supervisor_med`, `second_supervisor_med`, `first_supervisor_sci`, `second_supervisor_sci`, `first_supervisor_soc`, `second_supervisor_soc`, `first_supervisor_vet`, `second_supervisor_vet`, `date`) VALUES (NULL, 'Mashonaland Central', '$lecturer_1_western_agr', '$lecturer_2_western_agr', '$lecturer_1_western_arts', '$lecturer_2_western_arts', '$lecturer_1_western_com', '$lecturer_2_western_com', '$lecturer_1_western_cie', '$lecturer_2_western_cie', '$lecturer_1_western_edu', '$lecturer_2_western_edu', '$lecturer_1_western_eng', '$lecturer_2_western_eng', '$lecturer_1_western_law', '$lecturer_2_western_law', '$lecturer_1_western_med', '$lecturer_2_western_med', '$lecturer_1_western_sci', '$lecturer_2_western_sci', '$lecturer_1_western_soc', '$lecturer_2_western_soc', '$lecturer_1_western_vet', '$lecturer_2_western_vet', CURRENT_TIMESTAMP)"; 
								
					$execute_insert_for_western_query = mysqli_query($conn,$mysql_insert_western_query);
						
						//
					
					
					$mysql_insert_ashanti_query = "INSERT INTO `assigned_lecturers` (`id`, `regions`, `first_supervisor_agr`, `second_supervisor_agr`, `first_supervisor_arts`, `second_supervisor_arts`, `first_supervisor_com`, `second_supervisor_com`, `first_supervisor_cie`, `second_supervisor_cie`, `first_supervisor_edu`, `second_supervisor_edu`, `first_supervisor_eng`, `second_supervisor_eng`, `first_supervisor_law`, `second_supervisor_law`, `first_supervisor_med`, `second_supervisor_med`, `first_supervisor_sci`, `second_supervisor_sci`, `first_supervisor_soc`, `second_supervisor_soc`, `first_supervisor_vet`, `second_supervisor_vet`, `date`) VALUES (NULL, 'Mashonaland East', '$lecturer_1_ashanti_agr', '$lecturer_2_ashanti_agr', '$lecturer_1_ashanti_arts', '$lecturer_2_ashanti_arts', '$lecturer_1_ashanti_com', '$lecturer_2_ashanti_com', '$lecturer_1_ashanti_cie', '$lecturer_2_ashanti_cie', '$lecturer_1_ashanti_edu', '$lecturer_2_ashanti_edu', '$lecturer_1_ashanti_eng', '$lecturer_2_ashanti_eng', '$lecturer_1_ashanti_law', '$lecturer_2_ashanti_law', '$lecturer_1_ashanti_med', '$lecturer_2_ashanti_med', '$lecturer_1_ashanti_sci', '$lecturer_2_ashanti_sci', '$lecturer_1_ashanti_soc', '$lecturer_2_ashanti_soc', '$lecturer_1_ashanti_vet', '$lecturer_2_ashanti_vet', CURRENT_TIMESTAMP)"; 
								
					$execute_insert_for_ashanti_query = mysqli_query($conn,$mysql_insert_ashanti_query);
						
						//
					
					
					$mysql_insert_northern_query = "INSERT INTO `assigned_lecturers` (`id`, `regions`, `first_supervisor_agr`, `second_supervisor_agr`, `first_supervisor_arts`, `second_supervisor_arts`, `first_supervisor_com`, `second_supervisor_com`, `first_supervisor_cie`, `second_supervisor_cie`, `first_supervisor_edu`, `second_supervisor_edu`, `first_supervisor_eng`, `second_supervisor_eng`, `first_supervisor_law`, `second_supervisor_law`, `first_supervisor_med`, `second_supervisor_med`, `first_supervisor_sci`, `second_supervisor_sci`, `first_supervisor_soc`, `second_supervisor_soc`, `first_supervisor_vet`, `second_supervisor_vet`, `date`) VALUES (NULL, 'Mashonaland West', '$lecturer_1_northern_agr', '$lecturer_2_northern_agr', '$lecturer_1_northern_arts', '$lecturer_2_northern_arts', '$lecturer_1_northern_com', '$lecturer_2_northern_com', '$lecturer_1_northern_cie', '$lecturer_2_northern_cie', '$lecturer_1_northern_edu', '$lecturer_2_northern_edu', '$lecturer_1_northern_eng', '$lecturer_2_northern_eng', '$lecturer_1_northern_law', '$lecturer_2_northern_law', '$lecturer_1_northern_med', '$lecturer_2_northern_med', '$lecturer_1_northern_sci', '$lecturer_2_northern_sci', '$lecturer_1_northern_soc', '$lecturer_2_northern_soc', '$lecturer_1_northern_vet', '$lecturer_2_northern_vet', CURRENT_TIMESTAMP)"; 
								
					$execute_insert_for_northern_query = mysqli_query($conn,$mysql_insert_northern_query);
						
						//
					
					
					$mysql_insert_upper_east_query = "INSERT INTO `assigned_lecturers` (`id`, `regions`, `first_supervisor_agr`, `second_supervisor_agr`, `first_supervisor_arts`, `second_supervisor_arts`, `first_supervisor_com`, `second_supervisor_com`, `first_supervisor_cie`, `second_supervisor_cie`, `first_supervisor_edu`, `second_supervisor_edu`, `first_supervisor_eng`, `second_supervisor_eng`, `first_supervisor_law`, `second_supervisor_law`, `first_supervisor_med`, `second_supervisor_med`, `first_supervisor_sci`, `second_supervisor_sci`, `first_supervisor_soc`, `second_supervisor_soc`, `first_supervisor_vet`, `second_supervisor_vet`, `date`) VALUES (NULL, 'Masvingo', '$lecturer_1_upper_east_agr', '$lecturer_2_upper_east_agr', '$lecturer_1_upper_east_arts', '$lecturer_2_upper_east_arts', '$lecturer_1_upper_east_com', '$lecturer_2_upper_east_com', '$lecturer_1_upper_east_cie', '$lecturer_2_upper_east_cie', '$lecturer_1_upper_east_edu', '$lecturer_2_upper_east_edu', '$lecturer_1_upper_east_eng', '$lecturer_2_upper_east_eng', '$lecturer_1_upper_east_law', '$lecturer_2_upper_east_law', '$lecturer_1_upper_east_med', '$lecturer_2_upper_east_med', '$lecturer_1_upper_east_sci', '$lecturer_2_upper_east_sci', '$lecturer_1_upper_east_soc', '$lecturer_2_upper_east_soc', '$lecturer_1_upper_east_vet', '$lecturer_2_upper_east_vet', CURRENT_TIMESTAMP)"; 
								
					$execute_insert_for_upper_east_query = mysqli_query($conn,$mysql_insert_upper_east_query);
						
						//
					
					
					$mysql_insert_upper_west_query = "INSERT INTO `assigned_lecturers` (`id`, `regions`, `first_supervisor_agr`, `second_supervisor_agr`, `first_supervisor_arts`, `second_supervisor_arts`, `first_supervisor_com`, `second_supervisor_com`, `first_supervisor_cie`, `second_supervisor_cie`, `first_supervisor_edu`, `second_supervisor_edu`, `first_supervisor_eng`, `second_supervisor_eng`, `first_supervisor_law`, `second_supervisor_law`, `first_supervisor_med`, `second_supervisor_med`, `first_supervisor_sci`, `second_supervisor_sci`, `first_supervisor_soc`, `second_supervisor_soc`, `first_supervisor_vet`, `second_supervisor_vet`, `date`) VALUES (NULL, 'Matabeleland North', '$lecturer_1_upper_west_agr', '$lecturer_2_upper_west_agr', '$lecturer_1_upper_west_arts', '$lecturer_2_upper_west_arts', '$lecturer_1_upper_west_com', '$lecturer_2_upper_west_com', '$lecturer_1_upper_west_cie', '$lecturer_2_upper_west_cie', '$lecturer_1_upper_west_edu', '$lecturer_2_upper_west_edu', '$lecturer_1_upper_west_eng', '$lecturer_2_upper_west_eng', '$lecturer_1_upper_west_law', '$lecturer_2_upper_west_law', '$lecturer_1_upper_west_med', '$lecturer_2_upper_west_med', '$lecturer_1_upper_west_sci', '$lecturer_2_upper_west_sci', '$lecturer_1_upper_west_soc', '$lecturer_2_upper_west_soc', '$lecturer_1_upper_west_vet', '$lecturer_2_upper_west_vet', CURRENT_TIMESTAMP)"; 
								
					$execute_insert_for_upper_west_query = mysqli_query($conn,$mysql_insert_upper_west_query);
						
						//
					
					
					$mysql_insert_volta_query = "INSERT INTO `assigned_lecturers` (`id`, `regions`, `first_supervisor_agr`, `second_supervisor_agr`, `first_supervisor_arts`, `second_supervisor_arts`, `first_supervisor_com`, `second_supervisor_com`, `first_supervisor_cie`, `second_supervisor_cie`, `first_supervisor_edu`, `second_supervisor_edu`, `first_supervisor_eng`, `second_supervisor_eng`, `first_supervisor_law`, `second_supervisor_law`, `first_supervisor_med`, `second_supervisor_med`, `first_supervisor_sci`, `second_supervisor_sci`, `first_supervisor_soc`, `second_supervisor_soc`, `first_supervisor_vet`, `second_supervisor_vet`, `date`) VALUES (NULL, 'Matabeleland South', '$lecturer_1_volta_agr', '$lecturer_2_volta_agr', '$lecturer_1_volta_arts', '$lecturer_2_volta_arts', '$lecturer_1_volta_com', '$lecturer_2_volta_com', '$lecturer_1_volta_cie', '$lecturer_2_volta_cie', '$lecturer_1_volta_edu', '$lecturer_2_volta_edu', '$lecturer_1_volta_eng', '$lecturer_2_volta_eng', '$lecturer_1_volta_law', '$lecturer_2_volta_law', '$lecturer_1_volta_med', '$lecturer_2_volta_med', '$lecturer_1_volta_sci', '$lecturer_2_volta_sci', '$lecturer_1_volta_soc', '$lecturer_2_volta_soc', '$lecturer_1_volta_vet', '$lecturer_2_volta_vet', CURRENT_TIMESTAMP)"; 
							
					$execute_insert_for_volta_query = mysqli_query($conn,$mysql_insert_volta_query);
						
						//
					
					
					$mysql_insert_brong_query = "INSERT INTO `assigned_lecturers` (`id`, `regions`, `first_supervisor_agr`, `second_supervisor_agr`, `first_supervisor_arts`, `second_supervisor_arts`, `first_supervisor_com`, `second_supervisor_com`, `first_supervisor_cie`, `second_supervisor_cie`, `first_supervisor_edu`, `second_supervisor_edu`, `first_supervisor_eng`, `second_supervisor_eng`, `first_supervisor_law`, `second_supervisor_law`, `first_supervisor_med`, `second_supervisor_med`, `first_supervisor_sci`, `second_supervisor_sci`, `first_supervisor_soc`, `second_supervisor_soc`, `first_supervisor_vet`, `second_supervisor_vet`, `date`) VALUES (NULL, 'Midlands', '$lecturer_1_brong_agr', '$lecturer_2_brong_agr', '$lecturer_1_brong_arts', '$lecturer_2_brong_arts', '$lecturer_1_brong_com', '$lecturer_2_brong_com', '$lecturer_1_brong_cie', '$lecturer_2_brong_cie', '$lecturer_1_brong_edu', '$lecturer_2_brong_edu', '$lecturer_1_brong_eng', '$lecturer_2_brong_eng', '$lecturer_1_brong_law', '$lecturer_2_brong_law', '$lecturer_1_brong_med', '$lecturer_2_brong_med', '$lecturer_1_brong_sci', '$lecturer_2_brong_sci', '$lecturer_1_brong_soc', '$lecturer_2_brong_soc', '$lecturer_1_brong_vet', '$lecturer_2_brong_vet', CURRENT_TIMESTAMP)"; 
							
					$execute_insert_for_brong_query = mysqli_query($conn,$mysql_insert_brong_query);
										
					}
					
					// Assigning lecturers to industrial students (11 faculties × 10 provinces)
					$faculty_db_map = array(
						'AGR'=>array('AGR'), 'ARTS'=>array('ARTS'), 'COM'=>array('COM','FAST'), 'CIE'=>array('CIE'), 'EDU'=>array('EDU'),
						'ENG'=>array('ENG','FOE'), 'LAW'=>array('LAW'), 'MED'=>array('MED'), 'SCI'=>array('SCI','FBNE'),
						'SOC'=>array('SOC','FBMS'), 'VET'=>array('VET','FHAS')
					);
					$region_attachment = array('accra'=>'Bulawayo','central'=>'Harare','eastern'=>'Manicaland','western'=>'Mashonaland Central','ashanti'=>'Mashonaland East','northern'=>'Mashonaland West','upper_east'=>'Masvingo','upper_west'=>'Matabeleland North','volta'=>'Matabeleland South','brong'=>'Midlands');
					$faculties_upper = array('AGR','ARTS','COM','CIE','EDU','ENG','LAW','MED','SCI','SOC','VET');
					foreach ($region_attachment as $r => $region_name) {
						foreach ($fc as $i => $f) {
							$fac = $faculties_upper[$i];
							$name2 = trim(${"lecturer_2_{$r}_{$f}"} ?? '');
							$name1 = trim(${"lecturer_1_{$r}_{$f}"} ?? '');
							// Use second supervisor if selected, otherwise first supervisor
							$name_assign = $name2 !== '' ? ${"lecturer_2_{$r}_{$f}"} : $name1;
							$name_assign_esc = mysqli_real_escape_string($conn, $name_assign);
							$region_esc = mysqli_real_escape_string($conn, $region_name);
							$fac_list = isset($faculty_db_map[$fac]) ? $faculty_db_map[$fac] : array($fac);
							$fac_in = "'" . implode("','", array_map(function($x) use ($conn) { return mysqli_real_escape_string($conn, $x); }, $fac_list)) . "'";
							mysqli_query($conn, "UPDATE industrial_registration SET visiting_supervisor_name = '$name_assign_esc' WHERE faculty IN ($fac_in) AND attachment_region = '$region_esc'");
						}
					}

					// Dashboard uses assigned_lecturers + industrial_registration to show assigned students (no institutional_supervisor_students).
					echo "<script>alert('Supervisors have been assigned successfully.');</script>";
					
				}
			?>
			</body>
			</html>
