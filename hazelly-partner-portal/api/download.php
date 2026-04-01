<?php
/**
 * Download API – partner session required
 * Serves files securely (never exposes real path)
 */
define('HAZELLY_PORTAL', true);
require_once __DIR__ . '/../config.php';
require_partner_session();

$type = $_GET['type'] ?? '';
$id   = $_GET['id']   ?? '';

if (!in_array($type, ['documents', 'photos', 'videos'], true) || empty($id)) {
    http_response_code(400);
    exit('Requête invalide.');
}

$media = get_media();
$found = null;
foreach ($media[$type] as $item) {
    if ($item['id'] === $id) {
        $found = $item;
        break;
    }
}

if ($found === null) {
    http_response_code(404);
    exit('Fichier introuvable.');
}

$filePath = UPLOADS_DIR . '/' . $type . '/' . $found['filename'];

// Resolve real path and verify it stays inside UPLOADS_DIR
$realFile    = realpath($filePath);
$realUploads = realpath(UPLOADS_DIR);

if ($realFile === false || strpos($realFile, $realUploads) !== 0 || !is_file($realFile)) {
    http_response_code(404);
    exit('Fichier non disponible.');
}

// Determine MIME type
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($realFile);

// Sanitize filename for Content-Disposition
$downloadName = preg_replace('/[^\w\-. ]/', '_', $found['title']);
$ext          = pathinfo($found['filename'], PATHINFO_EXTENSION);
if (!str_ends_with($downloadName, '.' . $ext)) {
    $downloadName .= '.' . $ext;
}

// Stream the file
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($realFile));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
readfile($realFile);
exit;
