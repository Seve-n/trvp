<?php
/**
 * Hazelly Hair Love — Partner Portal
 * api/settings.php — Update partner or admin password
 */
require_once dirname(__DIR__) . '/auth.php';

require_admin('index.php?admin=1');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); exit('Method Not Allowed');
}

$field    = trim($_POST['field']           ?? '');
$new_pw   = trim($_POST['new_password']    ?? '');
$confirm  = trim($_POST['confirm_password'] ?? '');
$redirect = trim($_POST['redirect']        ?? '../admin.php?section=settings');

/* ── Sanitise redirect ─────────────────────────────────────── */
if (!preg_match('#^[a-zA-Z0-9_./?=&-]+$#', $redirect)) {
    $redirect = '../admin.php?section=settings';
}

function pw_flash_redirect(string $msg, string $redirect): void {
    $_SESSION['flash'] = $msg;
    header('Location: ../' . ltrim($redirect, './'));
    exit;
}

if (!in_array($field, ['partner_password', 'admin_password'], true)) {
    pw_flash_redirect('Champ invalide.', $redirect);
}

if (strlen($new_pw) < 6) {
    pw_flash_redirect('Le mot de passe doit contenir au moins 6 caractères.', $redirect);
}

if (!hash_equals($new_pw, $confirm)) {
    pw_flash_redirect('Les mots de passe ne correspondent pas.', $redirect);
}

$settings         = get_settings();
$settings[$field] = $new_pw;
save_settings($settings);

$label = $field === 'partner_password' ? 'partenaire' : 'administrateur';
pw_flash_redirect("Mot de passe {$label} mis à jour avec succès.", $redirect);
