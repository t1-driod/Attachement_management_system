<?php
// This script redirects old week URLs to the new dynamic system
// Usage: redirect_old_weeks.php?week=X

$week = isset($_GET['week']) ? (int)$_GET['week'] : 1;

// Redirect to the dynamic e-logbook with the specified week
header("Location: elogbook_dynamic.php?week=$week");
exit();
?>