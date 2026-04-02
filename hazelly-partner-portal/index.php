<?php
/**
 * Hazelly Hair Love — Partner Portal
 * index.php — Login page (partner & admin)
 */
require_once __DIR__ . '/auth.php';

$mode  = isset($_GET['admin']) ? 'admin' : 'partner';
$error = '';

/* ── Handle POST login ───────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password'] ?? '');
    $mode     = trim($_POST['mode'] ?? 'partner');

    if ($mode === 'admin') {
        if (login_admin($password)) {
            header('Location: admin.php');
            exit;
        }
    } else {
        if (login_partner($password)) {
            header('Location: portal.php');
            exit;
        }
    }
    $error = 'Mot de passe incorrect. Veuillez réessayer.';
}

/* ── Redirect if already logged in ──────────────────────────── */
if (is_authenticated('admin') && $mode === 'admin') {
    header('Location: admin.php'); exit;
}
if (is_authenticated('partner') && $mode === 'partner') {
    header('Location: portal.php'); exit;
}

$is_admin_mode = $mode === 'admin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <meta name="description" content="Espace partenaire Hazelly Hair Love — Accès réservé aux pharmacies partenaires.">
  <title><?= $is_admin_mode ? 'Administration' : 'Connexion' ?> — Hazelly Hair Love</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/login.css">
</head>
<body class="login-page">

  <!-- ══ Left decorative panel ════════════════════════════════ -->
  <aside class="login-visual" aria-hidden="true">
    <div class="login-visual-content">
      <div class="login-brand-logo">🌿</div>
      <h1 class="login-brand-name">Hazelly<br>Hair Love</h1>
      <p class="login-brand-tagline">Espace Partenaire Pharmacies</p>

      <?php if (!$is_admin_mode): ?>
      <ul class="login-features">
        <li class="login-feature-item">
          <span class="login-feature-icon">📄</span>
          <span>Fiches techniques &amp; bons de commande</span>
        </li>
        <li class="login-feature-item">
          <span class="login-feature-icon">🖼</span>
          <span>Visuels haute définition &amp; packshots</span>
        </li>
        <li class="login-feature-item">
          <span class="login-feature-icon">🎬</span>
          <span>Tutoriels vidéo &amp; contenus promotionnels</span>
        </li>
        <li class="login-feature-item">
          <span class="login-feature-icon">⬇</span>
          <span>Téléchargement direct de tous les fichiers</span>
        </li>
      </ul>
      <?php else: ?>
      <ul class="login-features">
        <li class="login-feature-item">
          <span class="login-feature-icon">📊</span>
          <span>Tableau de bord &amp; statistiques</span>
        </li>
        <li class="login-feature-item">
          <span class="login-feature-icon">📁</span>
          <span>Gestion de la médiathèque</span>
        </li>
        <li class="login-feature-item">
          <span class="login-feature-icon">⬆</span>
          <span>Upload de fichiers PDF, photos, vidéos</span>
        </li>
        <li class="login-feature-item">
          <span class="login-feature-icon">🔐</span>
          <span>Gestion des mots de passe</span>
        </li>
      </ul>
      <?php endif; ?>
    </div>
    <p class="login-visual-footer">© 2025 Hazelly Hair Love · Accès réservé</p>
  </aside>

  <!-- ══ Right form panel ══════════════════════════════════════ -->
  <main class="login-form-panel">
    <div class="login-form-wrap">

      <h2 class="login-title">
        <?= $is_admin_mode ? 'Connexion administrateur' : 'Connexion partenaire' ?>
      </h2>
      <p class="login-subtitle">
        <?= $is_admin_mode
          ? 'Accès réservé à l\'équipe Hazelly Hair Love.'
          : 'Veuillez saisir le mot de passe fourni par Hazelly Hair Love.' ?>
      </p>

      <?php if ($error): ?>
      <div class="login-error visible" role="alert">
        <span>⚠</span><span><?= htmlspecialchars($error) ?></span>
      </div>
      <?php endif; ?>

      <form class="login-form" method="post" action="index.php" novalidate>
        <input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <div class="password-input-wrap">
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              placeholder="Saisissez votre mot de passe"
              autocomplete="current-password"
              required
              autofocus
            >
            <button type="button" class="toggle-password" aria-label="Afficher/Masquer le mot de passe">👁</button>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-full btn-lg">
          <?= $is_admin_mode ? 'Accéder à l\'administration →' : 'Accéder à l\'espace partenaire →' ?>
        </button>
      </form>

      <p class="login-footer-note">
        <?php if ($is_admin_mode): ?>
          <a href="index.php">← Retour à la connexion partenaire</a>
        <?php else: ?>
          Administrateur ? <a href="index.php?admin=1">Accès administration</a>
        <?php endif; ?>
      </p>
    </div>
  </main>

  <script src="js/app.js"></script>
</body>
</html>
