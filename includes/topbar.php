<?php
// Shared top bar for students, admin, and institutional supervisors.
// Expects: $topbar_display_name (string), $topbar_logo_src (path to logo image).
// No Quick Actions button.
$topbar_display_name = isset($topbar_display_name) ? $topbar_display_name : '';
$topbar_logo_src = isset($topbar_logo_src) ? $topbar_logo_src : '../img/header_log.png';
$topbar_display_esc = htmlspecialchars(trim($topbar_display_name));
$first_word = preg_match('/^\s*(\S)/u', $topbar_display_name, $m) ? $m[1] : '?';
$topbar_initial = $topbar_display_esc !== '' ? strtoupper(mb_substr($first_word, 0, 1)) : '?';
?>
<div id="top-navigation">
  <div id="header_logo">
    <img src="<?php echo htmlspecialchars($topbar_logo_src); ?>" class="img-responsive" alt="Logo">
  </div>
  <div class="top-bar-center">
    <div class="top-search">
      <span class="top-search-icon" aria-hidden="true">&#8981;</span>
      <input type="text" placeholder="Search logbook, forms, and more" class="top-search-input" />
    </div>
  </div>
  <div class="top-bar-right">
    <button type="button" class="top-icon-button" title="Notifications" aria-label="Notifications">&#128276;</button>
    <button type="button" class="top-icon-button" title="Help" aria-label="Help">&#63;</button>
    <div class="top-account">
      <div class="avatar-circle"><?php echo $topbar_initial; ?></div>
      <span class="top-account-name"><?php echo $topbar_display_esc; ?></span>
    </div>
  </div>
</div>
