<?php
/**
 * Hazelly Hair Love — Partner Portal
 * auth.php — Session-based authentication helpers
 */

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

/* ── Login ───────────────────────────────────────────────────── */
function login_partner(string $password): bool {
    $settings = get_settings();
    if (hash_equals($settings['partner_password'], $password)) {
        session_regenerate_id(true);
        $_SESSION['role'] = 'partner';
        $_SESSION['ts']   = time();
        return true;
    }
    return false;
}

function login_admin(string $password): bool {
    $settings = get_settings();
    if (hash_equals($settings['admin_password'], $password)) {
        session_regenerate_id(true);
        $_SESSION['role'] = 'admin';
        $_SESSION['ts']   = time();
        return true;
    }
    return false;
}

/* ── Session checks ──────────────────────────────────────────── */
function is_authenticated(string $required_role = 'partner'): bool {
    if (!isset($_SESSION['role'])) return false;
    if ($required_role === 'admin') return $_SESSION['role'] === 'admin';
    return in_array($_SESSION['role'], ['partner', 'admin'], true);
}

function require_auth(string $role = 'partner', string $redirect = 'index.php'): void {
    if (!is_authenticated($role)) {
        header('Location: ' . $redirect);
        exit;
    }
}

function require_admin(string $redirect = 'index.php?admin=1'): void {
    require_auth('admin', $redirect);
}

/* ── Logout ──────────────────────────────────────────────────── */
function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
