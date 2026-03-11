<?php
// Student top bar: sets display name from cookies and includes shared topbar (no Quick Actions).
$student_fname = isset($student_fname) ? $student_fname : '';
$student_lname = isset($student_lname) ? $student_lname : '';
if (!isset($topbar_logo_src)) {
  $topbar_logo_src = '../img/header_log.png';
}
$topbar_display_name = trim($student_fname . ' ' . $student_lname);
include __DIR__ . '/topbar.php';
