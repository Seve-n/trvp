<?php
/**
 * Change password API – admin only
 */
define('HAZELLY_PORTAL', true);
require_once __DIR__ . '/../config.php';
require_admin_session();

function redirect_with_flash(string $type, string $message, string $anchor = ''): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    header('Location: ../admin/dashboard.php' . ($anchor ? '#' . $anchor : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_flash('error', 'Méthode non autorisée.', 'settings');
}

$target         = $_POST['target']           ?? '';
$newPassword    = $_POST['new_password']     ?? '';
$confirmPassword= $_POST['confirm_password'] ?? '';
$currentPassword= $_POST['current_password'] ?? '';

if (!in_array($target, ['partner', 'admin'], true)) {
    redirect_with_flash('error', 'Cible invalide.', 'settings');
}

// Validate new password length
$minLen = ($target === 'admin') ? 8 : 6;
if (strlen($newPassword) < $minLen) {
    redirect_with_flash('error', "Le nouveau mot de passe doit faire au moins {$minLen} caractères.", 'settings');
}

// Confirm passwords match
if (!hash_equals($newPassword, $confirmPassword)) {
    redirect_with_flash('error', 'Les mots de passe ne correspondent pas.', 'settings');
}

$settings = get_settings();

// For admin password change, verify current password first
if ($target === 'admin') {
    if (!hash_equals($settings['admin_hash'], hash('sha256', $currentPassword))) {
        usleep(400000);
        redirect_with_flash('error', 'Mot de passe actuel incorrect.', 'settings');
    }
}

// Update hash
$settings[$target . '_hash'] = hash('sha256', $newPassword);

if (!save_settings($settings)) {
    redirect_with_flash('error', 'Erreur lors de la sauvegarde. Vérifiez les permissions du dossier.', 'settings');
}

$label = ($target === 'admin') ? 'administrateur' : 'partenaire';
redirect_with_flash('success', "✅ Mot de passe {$label} mis à jour avec succès.", 'settings');
