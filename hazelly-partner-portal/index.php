<?php
define('HAZELLY_PORTAL', true);
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Already logged in → redirect
if (!empty($_SESSION['partner_logged_in'])) {
    header('Location: portal.php');
    exit;
}

$error   = '';
$expired = !empty($_GET['expired']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $settings = get_settings();

    if (hash_equals($settings['partner_hash'], hash('sha256', $password))) {
        session_regenerate_id(true);
        $_SESSION['partner_logged_in'] = true;
        $_SESSION['login_time']        = time();
        header('Location: portal.php');
        exit;
    } else {
        // Artificial delay to slow brute-force
        usleep(400000);
        $error = 'Mot de passe incorrect. Veuillez réessayer.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Connexion – <?= BRAND_NAME ?> | <?= PORTAL_TITLE ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Decorative hair-wave SVG background */
    .login-page {
      background-image:
        url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 40 Q20 20 40 40 Q60 60 80 40' fill='none' stroke='%23C9A96E' stroke-width='1' stroke-opacity='0.12'/%3E%3C/svg%3E"),
        linear-gradient(135deg, rgba(201,169,110,0.10) 0%25, transparent 60%25),
        linear-gradient(225deg, rgba(232,201,192,0.15) 0%25, transparent 60%25),
        #FAF6F1;
    }
  </style>
</head>
<body class="login-page">

  <main class="login-card" role="main">

    <!-- Logo / Branding -->
    <div class="login-logo">
      <div class="brand-icon" aria-hidden="true">🌿</div>
      <h1><?= BRAND_NAME ?></h1>
      <p class="tagline"><?= PORTAL_TITLE ?></p>
    </div>

    <!-- Expiry notice -->
    <?php if ($expired): ?>
    <div class="alert alert-info" role="alert">
      <span>🔒</span>
      Votre session a expiré. Veuillez vous reconnecter.
    </div>
    <?php endif; ?>

    <!-- Error message -->
    <?php if ($error): ?>
    <div class="alert alert-error" role="alert">
      <span>⚠️</span>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Login form -->
    <form method="POST" action="index.php" novalidate>
      <div class="form-group">
        <label class="form-label" for="password">Mot de passe partenaire</label>
        <input
          class="form-input"
          type="password"
          id="password"
          name="password"
          autocomplete="current-password"
          placeholder="••••••••••"
          required
          autofocus
        >
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
        <span>🔑</span> Accéder à l'espace partenaire
      </button>
    </form>

    <div class="login-footer">
      <p>Accès réservé aux pharmacies partenaires.<br>
      Pour obtenir votre code d'accès, contactez votre représentant
      <strong><?= BRAND_NAME ?></strong>.</p>
    </div>

  </main>

</body>
</html>
