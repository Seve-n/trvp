<?php
/**
 * Hazelly Hair Love — Partner Portal
 * api/media.php — Create / Update / Delete media items
 * All actions require admin authentication.
 */
require_once dirname(__DIR__) . '/auth.php';
require_once dirname(__DIR__) . '/media.php';

require_admin('index.php?admin=1');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$action   = trim($_POST['action'] ?? '');
$redirect = trim($_POST['redirect'] ?? 'admin.php');

/* ── Whitelist of allowed redirect targets ─────────────────── */
const ALLOWED_REDIRECTS = [
    'admin.php',
    'admin.php?section=dashboard',
    'admin.php?section=pdf',
    'admin.php?section=photo',
    'admin.php?section=video',
    'admin.php?section=settings',
];
if (!in_array($redirect, ALLOWED_REDIRECTS, true)) {
    $redirect = 'admin.php';
}

function flash_and_redirect(string $message, string $redirect): void {
    $_SESSION['flash'] = $message;
    header('Location: ' . $redirect);
    exit;
}

/* ── Handle file upload ─────────────────────────────────────── */
function handle_upload(string $type): ?array {
    if (empty($_FILES['file']['name'])) return null;
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) return null;

    $allowed = ALLOWED_TYPES[$type] ?? [];
    $mime    = mime_content_type($_FILES['file']['tmp_name']);

    if (!in_array($mime, $allowed, true)) {
        return ['error' => 'Type de fichier non autorisé : ' . htmlspecialchars($mime)];
    }

    if ($_FILES['file']['size'] > MAX_UPLOAD_SIZE) {
        return ['error' => 'Fichier trop volumineux (max 200 Mo).'];
    }

    $dir_map = ['pdf' => 'documents', 'photo' => 'photos', 'video' => 'videos'];
    $subdir  = $dir_map[$type] ?? $type;
    $dir     = UPLOADS_DIR . '/' . $subdir;
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $orig_name  = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
    $ext        = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    $safe_name  = preg_replace('/[^a-zA-Z0-9_-]/', '_', $orig_name);
    $file_name  = $safe_name . '_' . uniqid() . '.' . $ext;
    $dest       = $dir . '/' . $file_name;

    if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
        return ['error' => 'Impossible de déplacer le fichier uploadé.'];
    }

    return [
        'file_path' => 'uploads/' . $subdir . '/' . $file_name,
        'file_name' => $_FILES['file']['name'],
        'file_size' => format_size($_FILES['file']['size']),
    ];
}

function format_size(int $bytes): string {
    if ($bytes < 1024)        return $bytes . ' o';
    if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' Ko';
    return round($bytes / (1024 * 1024), 1) . ' Mo';
}

/* ══ Actions ════════════════════════════════════════════════ */

if ($action === 'add') {
    $type  = trim($_POST['type'] ?? '');
    $title = trim($_POST['title'] ?? '');

    if (!$title) {
        flash_and_redirect('Le titre est obligatoire.', $redirect);
    }

    $upload = handle_upload($type);
    if (isset($upload['error'])) {
        flash_and_redirect($upload['error'], $redirect);
    }

    $data = [
        'type'        => $type,
        'title'       => $title,
        'description' => trim($_POST['description'] ?? ''),
        'url'         => trim($_POST['url'] ?? ''),
        'file_size'   => trim($_POST['file_size'] ?? ''),
        'is_new'      => isset($_POST['is_new']),
    ];

    if ($upload) {
        $data = array_merge($data, $upload);
    }

    media_add($data);
    flash_and_redirect('Contenu ajouté avec succès.', $redirect);
}

if ($action === 'update') {
    $id    = trim($_POST['id'] ?? '');
    $title = trim($_POST['title'] ?? '');

    if (!$id || !$title) {
        flash_and_redirect('Données manquantes.', $redirect);
    }

    $type   = media_get_by_id($id)['type'] ?? 'pdf';
    $upload = handle_upload($type);
    if (isset($upload['error'])) {
        flash_and_redirect($upload['error'], $redirect);
    }

    $updates = [
        'title'       => $title,
        'description' => trim($_POST['description'] ?? ''),
        'url'         => trim($_POST['url'] ?? ''),
        'file_size'   => trim($_POST['file_size'] ?? ''),
        'is_new'      => isset($_POST['is_new']),
    ];

    if ($upload) {
        // Remove old file
        $existing = media_get_by_id($id);
        if ($existing && !empty($existing['file_path'])) {
            $old = BASE_DIR . '/' . ltrim($existing['file_path'], '/');
            if (file_exists($old)) @unlink($old);
        }
        $updates = array_merge($updates, $upload);
    }

    media_update($id, $updates);
    flash_and_redirect('Contenu mis à jour avec succès.', $redirect);
}

if ($action === 'delete') {
    $id = trim($_POST['id'] ?? '');
    if (!$id) {
        flash_and_redirect('ID manquant.', $redirect);
    }
    media_delete($id);
    flash_and_redirect('Contenu supprimé.', $redirect);
}

flash_and_redirect('Action inconnue.', $redirect);
