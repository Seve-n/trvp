<?php
/**
 * Hazelly Hair Love — Partner Portal
 * config.php — Central configuration
 *
 * Edit PARTNER_PASSWORD and ADMIN_PASSWORD to change access credentials.
 * These can also be changed at runtime through the admin panel (stored in data/settings.json).
 */

define('BASE_DIR', __DIR__);
define('DATA_DIR', BASE_DIR . '/data');
define('UPLOADS_DIR', BASE_DIR . '/uploads');
define('MEDIA_FILE', DATA_DIR . '/media.json');
define('SETTINGS_FILE', DATA_DIR . '/settings.json');

/* ── Default credentials (used if settings.json doesn't exist) ── */
define('DEFAULT_PARTNER_PASSWORD', 'HazellyPartner2024');
define('DEFAULT_ADMIN_PASSWORD',   'HazellyAdmin2024');

/* ── Allowed upload MIME types ───────────────────────────────── */
define('ALLOWED_TYPES', [
    'pdf'   => ['application/pdf'],
    'photo' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    'video' => ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'],
]);

/* ── Max upload size (bytes) ─────────────────────────────────── */
define('MAX_UPLOAD_SIZE', 200 * 1024 * 1024); // 200 MB

/* ── Session name ────────────────────────────────────────────── */
define('SESSION_NAME', 'hazelly_session');

/* ── Load runtime settings ───────────────────────────────────── */
function get_settings(): array {
    if (file_exists(SETTINGS_FILE)) {
        $data = json_decode(file_get_contents(SETTINGS_FILE), true);
        if (is_array($data)) return $data;
    }
    return [
        'partner_password' => DEFAULT_PARTNER_PASSWORD,
        'admin_password'   => DEFAULT_ADMIN_PASSWORD,
    ];
}

function save_settings(array $settings): bool {
    if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
    return file_put_contents(SETTINGS_FILE, json_encode($settings, JSON_PRETTY_PRINT)) !== false;
}
