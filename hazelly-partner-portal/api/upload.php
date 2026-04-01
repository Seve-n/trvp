<?php
/**
 * Upload API – admin only
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
    redirect_with_flash('error', 'Méthode non autorisée.', 'upload');
}

$type        = $_POST['type']        ?? '';
$title       = trim($_POST['title']       ?? '');
$description = trim($_POST['description'] ?? '');

// Validate category
if (!in_array($type, ['documents', 'photos', 'videos'], true)) {
    redirect_with_flash('error', 'Catégorie invalide.', 'upload');
}

// Validate title
if ($title === '' || mb_strlen($title) > 120) {
    redirect_with_flash('error', 'Le titre est requis (120 caractères max).', 'upload');
}

// Validate file upload
if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE   => 'Fichier trop volumineux (limite serveur).',
        UPLOAD_ERR_FORM_SIZE  => 'Fichier trop volumineux.',
        UPLOAD_ERR_PARTIAL    => 'Upload incomplet.',
        UPLOAD_ERR_NO_FILE    => 'Aucun fichier sélectionné.',
    ];
    $code    = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;
    $errMsg  = $uploadErrors[$code] ?? 'Erreur lors de l\'upload.';
    redirect_with_flash('error', $errMsg, 'upload');
}

$uploadedFile = $_FILES['file'];

// Check file size
if ($uploadedFile['size'] > MAX_FILE_SIZE) {
    redirect_with_flash('error', 'Le fichier dépasse la limite de 100 Mo.', 'upload');
}

// Validate MIME type
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($uploadedFile['tmp_name']);
$allowed  = ALLOWED_MIMES[$type];

if (!in_array($mimeType, $allowed, true)) {
    redirect_with_flash('error',
        'Type de fichier non autorisé pour cette catégorie. Types acceptés : ' . implode(', ', $allowed),
        'upload'
    );
}

// Generate safe filename and build destination
$safeFilename = safe_filename($uploadedFile['name']);
$destDir      = UPLOADS_DIR . '/' . $type;

if (!is_dir($destDir)) {
    mkdir($destDir, 0750, true);
}

$destPath = $destDir . '/' . $safeFilename;

if (!move_uploaded_file($uploadedFile['tmp_name'], $destPath)) {
    redirect_with_flash('error', 'Impossible de déplacer le fichier. Vérifiez les permissions.', 'upload');
}

// Save metadata
$media = get_media();
$media[$type][] = [
    'id'          => uniqid('media_', true),
    'title'       => $title,
    'description' => mb_substr($description, 0, MAX_DESCRIPTION_LENGTH),
    'filename'    => $safeFilename,
    'created_at'  => time(),
];

if (!save_media($media)) {
    // Remove uploaded file on metadata save failure
    @unlink($destPath);
    redirect_with_flash('error', 'Erreur lors de la sauvegarde des métadonnées.', 'upload');
}

redirect_with_flash('success', '✅ « ' . htmlspecialchars($title) . ' » a été ajouté avec succès !', 'manage');
