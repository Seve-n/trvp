<?php
/**
 * Hazelly Hair Love - B2B Partner Portal Configuration
 * 
 * IMPORTANT: Keep this file secure. Do not expose it publicly.
 * Change the default passwords immediately via the admin panel after first deployment.
 */

// Prevent direct access if accessed outside of PHP include
if (!defined('HAZELLY_PORTAL')) {
    http_response_code(403);
    exit('Access denied.');
}

// ── Data file paths ──────────────────────────────────────────────
define('DATA_DIR',     __DIR__ . '/data');
define('UPLOADS_DIR',  __DIR__ . '/uploads');
define('MEDIA_FILE',   DATA_DIR . '/media.json');
define('CONFIG_FILE',  DATA_DIR . '/settings.json');

// ── Default passwords (SHA-256 hashed) ──────────────────────────
// Partner password default: hazelly2024
// Admin password default:   HazellyAdmin2024!
define('DEFAULT_PARTNER_HASH', hash('sha256', 'hazelly2024'));
define('DEFAULT_ADMIN_HASH',   hash('sha256', 'HazellyAdmin2024!'));

// ── Session configuration ────────────────────────────────────────
define('SESSION_LIFETIME', 3600); // 1 hour

// ── Upload limits ────────────────────────────────────────────────
define('MAX_FILE_SIZE',        100 * 1024 * 1024); // 100 MB
define('MAX_FILENAME_LENGTH',  80);
define('MAX_DESCRIPTION_LENGTH', 300);

// ── Allowed MIME types per media category ───────────────────────
define('ALLOWED_MIMES', [
    'documents' => ['application/pdf'],
    'photos'    => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    'videos'    => ['video/mp4', 'video/webm', 'video/quicktime', 'video/avi'],
]);

// ── Branding ─────────────────────────────────────────────────────
define('BRAND_NAME',    'Hazelly Hair Love');
define('PORTAL_TITLE',  'Espace Partenaire');

// ── Helper: load settings from JSON ─────────────────────────────
function get_settings(): array {
    if (!file_exists(CONFIG_FILE)) {
        return [
            'partner_hash' => DEFAULT_PARTNER_HASH,
            'admin_hash'   => DEFAULT_ADMIN_HASH,
        ];
    }
    $data = json_decode(file_get_contents(CONFIG_FILE), true);
    return is_array($data) ? $data : [
        'partner_hash' => DEFAULT_PARTNER_HASH,
        'admin_hash'   => DEFAULT_ADMIN_HASH,
    ];
}

// ── Helper: save settings ────────────────────────────────────────
function save_settings(array $settings): bool {
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0750, true);
    }
    return file_put_contents(CONFIG_FILE, json_encode($settings, JSON_PRETTY_PRINT)) !== false;
}

// ── Helper: load media list ──────────────────────────────────────
function get_media(): array {
    if (!file_exists(MEDIA_FILE)) {
        return ['documents' => [], 'photos' => [], 'videos' => []];
    }
    $data = json_decode(file_get_contents(MEDIA_FILE), true);
    return is_array($data) ? $data : ['documents' => [], 'photos' => [], 'videos' => []];
}

// ── Helper: save media list ──────────────────────────────────────
function save_media(array $media): bool {
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0750, true);
    }
    return file_put_contents(MEDIA_FILE, json_encode($media, JSON_PRETTY_PRINT)) !== false;
}

// ── Helper: generate a safe filename ────────────────────────────
function safe_filename(string $original): string {
    $name = pathinfo($original, PATHINFO_FILENAME);
    $ext  = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
    $name = substr($name, 0, MAX_FILENAME_LENGTH);
    return $name . '_' . uniqid() . '.' . $ext;
}

// ── Helper: check partner session ───────────────────────────────
function require_partner_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['partner_logged_in']) || empty($_SESSION['login_time'])) {
        header('Location: /hazelly-partner-portal/index.php?expired=1');
        exit;
    }
    if (time() - $_SESSION['login_time'] > SESSION_LIFETIME) {
        session_destroy();
        header('Location: /hazelly-partner-portal/index.php?expired=1');
        exit;
    }
    // Refresh login time on each request
    $_SESSION['login_time'] = time();
}

// ── Helper: check admin session ─────────────────────────────────
function require_admin_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['admin_logged_in']) || empty($_SESSION['admin_login_time'])) {
        header('Location: /hazelly-partner-portal/admin/index.php?expired=1');
        exit;
    }
    if (time() - $_SESSION['admin_login_time'] > SESSION_LIFETIME) {
        session_destroy();
        header('Location: /hazelly-partner-portal/admin/index.php?expired=1');
        exit;
    }
    $_SESSION['admin_login_time'] = time();
}
