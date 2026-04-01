<?php
define('HAZELLY_PORTAL', true);
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Already logged in as admin → redirect
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$error   = '';
$expired = !empty($_GET['expired']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $settings = get_settings();

    if (hash_equals($settings['admin_hash'], hash('sha256', $password))) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in']  = true;
        $_SESSION['admin_login_time'] = time();
        header('Location: dashboard.php');
        exit;
    } else {
        usleep(400000);
        $error = 'Mot de passe administrateur incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Admin – <?= BRAND_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-page">

  <main class="login-card" role="main">
    <div class="login-logo">
      <div class="brand-icon" aria-hidden="true">⚙️</div>
      <h1><?= BRAND_NAME ?></h1>
      <p class="tagline">Panneau d'administration</p>
    </div>

    <?php if ($expired): ?>
    <div class="alert alert-info"><span>🔒</span> Session expirée.</div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-error"><span>⚠️</span> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php" novalidate>
      <div class="form-group">
        <label class="form-label" for="password">Mot de passe administrateur</label>
        <input class="form-input" type="password" id="password" name="password"
               autocomplete="current-password" placeholder="••••••••••" required autofocus>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
        <span>🔑</span> Connexion Admin
      </button>
    </form>

    <div class="login-footer">
      <a href="../index.php">← Retour à l'espace partenaire</a>
    </div>
  </main>

</body>
</html>
