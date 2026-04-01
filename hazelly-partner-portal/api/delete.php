<?php
/**
 * Delete media API – admin only
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
    redirect_with_flash('error', 'Méthode non autorisée.');
}

$category = $_POST['type'] ?? '';
$id       = $_POST['id']   ?? '';

if (!in_array($category, ['documents', 'photos', 'videos'], true)) {
    redirect_with_flash('error', 'Catégorie invalide.');
}

if (empty($id)) {
    redirect_with_flash('error', 'Identifiant manquant.');
}

$media = get_media();

// Find the item
$found = null;
$index = null;
foreach ($media[$category] as $i => $item) {
    if ($item['id'] === $id) {
        $found = $item;
        $index = $i;
        break;
    }
}

if ($found === null) {
    redirect_with_flash('error', 'Média introuvable.');
}

// Remove file from disk
$filePath = UPLOADS_DIR . '/' . $category . '/' . $found['filename'];
if (file_exists($filePath)) {
    @unlink($filePath);
}

// Remove from metadata
array_splice($media[$category], $index, 1);
save_media($media);

redirect_with_flash('success', '🗑️ « ' . htmlspecialchars($found['title']) . ' » supprimé.', 'manage');
