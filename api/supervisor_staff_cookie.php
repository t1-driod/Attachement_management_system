<?php
/**
 * Signed cookie for supervisor staff_id so assessment-passwords uses the correct row
 * even when the PHP session is shared or wrong. Only the server can produce a valid cookie.
 */
if (!defined('IASMS_SUPERVISOR_COOKIE_NAME')) {
    define('IASMS_SUPERVISOR_COOKIE_NAME', 'iasms_supervisor_staff_id');
}
if (!defined('IASMS_SUPERVISOR_COOKIE_SECRET')) {
    define('IASMS_SUPERVISOR_COOKIE_SECRET', 'iasms_supervisor_staff_cookie_v1_' . (__DIR__ ?? ''));
}

/**
 * Set the signed cookie with the given staff_id. Call on supervisor login.
 */
function iasms_set_supervisor_staff_cookie(string $staff_id): void {
    $payload = $staff_id . ':' . hash_hmac('sha256', $staff_id, IASMS_SUPERVISOR_COOKIE_SECRET);
    $value = base64_encode($payload);
    $exp = time() + (86400 * 30); // 30 days
    setcookie(IASMS_SUPERVISOR_COOKIE_NAME, $value, [
        'expires' => $exp,
        'path' => '/',
        'samesite' => 'Lax',
        'secure' => false,
    ]);
}

/**
 * Read and verify the cookie; return the staff_id or empty string if missing/invalid.
 */
function iasms_get_supervisor_staff_id_from_cookie(): string {
    $raw = $_COOKIE[IASMS_SUPERVISOR_COOKIE_NAME] ?? '';
    if ($raw === '') {
        return '';
    }
    $decoded = base64_decode($raw, true);
    if ($decoded === false || strpos($decoded, ':') === false) {
        return '';
    }
    $parts = explode(':', $decoded, 2);
    $staff_id = $parts[0];
    $sig = $parts[1] ?? '';
    if ($staff_id === '' || $sig === '') {
        return '';
    }
    $expected = hash_hmac('sha256', $staff_id, IASMS_SUPERVISOR_COOKIE_SECRET);
    if (!hash_equals($expected, $sig)) {
        return '';
    }
    return $staff_id;
}

/**
 * Clear the supervisor staff_id cookie. Call on logout.
 */
function iasms_clear_supervisor_staff_cookie(): void {
    setcookie(IASMS_SUPERVISOR_COOKIE_NAME, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'samesite' => 'Lax',
        'secure' => false,
    ]);
}
