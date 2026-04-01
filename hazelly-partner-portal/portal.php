<?php
define('HAZELLY_PORTAL', true);
require_once __DIR__ . '/config.php';
require_partner_session();

$media   = get_media();
$docCount   = count($media['documents'] ?? []);
$photoCount = count($media['photos']    ?? []);
$videoCount = count($media['videos']    ?? []);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?= BRAND_NAME ?> – <?= PORTAL_TITLE ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ── Top navigation bar ──────────────────────────────────── -->
<header class="topbar">
  <div class="topbar-inner">
    <a href="portal.php" class="topbar-brand" aria-label="Accueil <?= BRAND_NAME ?>">
      <div class="brand-dot" aria-hidden="true">🌿</div>
      <div class="brand-text">
        <span class="brand-name"><?= BRAND_NAME ?></span>
        <span class="brand-sub"><?= PORTAL_TITLE ?></span>
      </div>
    </a>
    <div class="topbar-actions">
      <span class="topbar-welcome">Bienvenue, <strong>Partenaire</strong></span>
      <a href="logout.php" class="btn btn-outline btn-sm">
        <span aria-hidden="true">🚪</span> Déconnexion
      </a>
    </div>
  </div>
</header>

<!-- ── Hero banner ─────────────────────────────────────────── -->
<section class="portal-hero" aria-label="Bienvenue">
  <h1>Votre espace ressources</h1>
  <p>Retrouvez ici tous vos outils marketing, fiches produits et visuels <?= BRAND_NAME ?>.</p>
</section>

<!-- ── Main content ────────────────────────────────────────── -->
<main class="portal-container">

  <!-- Category Tabs -->
  <nav class="tabs" role="tablist" aria-label="Catégories de ressources">
    <button class="tab-btn active" role="tab" aria-selected="true"
            aria-controls="panel-docs" id="tab-docs"
            data-tab="docs" onclick="switchTab('docs', this)">
      📄 Documents
      <span class="tab-count"><?= $docCount ?></span>
    </button>
    <button class="tab-btn" role="tab" aria-selected="false"
            aria-controls="panel-photos" id="tab-photos"
            data-tab="photos" onclick="switchTab('photos', this)">
      🖼️ Photos
      <span class="tab-count"><?= $photoCount ?></span>
    </button>
    <button class="tab-btn" role="tab" aria-selected="false"
            aria-controls="panel-videos" id="tab-videos"
            data-tab="videos" onclick="switchTab('videos', this)">
      🎬 Vidéos
      <span class="tab-count"><?= $videoCount ?></span>
    </button>
  </nav>

  <!-- ── DOCUMENTS tab ───────────────────────────────────────── -->
  <section id="panel-docs" class="tab-panel active" role="tabpanel" aria-labelledby="tab-docs">
    <?php if ($docCount === 0): ?>
    <div class="empty-state">
      <div class="empty-icon">📁</div>
      <p>Aucun document disponible pour le moment.<br>
         Revenez bientôt ou contactez votre représentant.</p>
    </div>
    <?php else: ?>
    <div class="doc-grid">
      <?php foreach ($media['documents'] as $doc): ?>
      <article class="doc-card">
        <div class="doc-icon" aria-hidden="true">📄</div>
        <div class="doc-info">
          <h3><?= htmlspecialchars($doc['title']) ?></h3>
          <p class="doc-meta">
            <?php if (!empty($doc['description'])): ?>
            <?= htmlspecialchars($doc['description']) ?> ·
            <?php endif; ?>
            Ajouté le <?= date('d/m/Y', $doc['created_at']) ?>
          </p>
        </div>
        <div class="doc-actions">
          <a href="api/download.php?type=documents&id=<?= urlencode($doc['id']) ?>"
             class="btn btn-primary btn-sm" download>
            ⬇️ Télécharger
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>

  <!-- ── PHOTOS tab ─────────────────────────────────────────── -->
  <section id="panel-photos" class="tab-panel" role="tabpanel" aria-labelledby="tab-photos">
    <?php if ($photoCount === 0): ?>
    <div class="empty-state">
      <div class="empty-icon">🖼️</div>
      <p>Aucune photo disponible pour le moment.</p>
    </div>
    <?php else: ?>
    <div class="photo-grid">
      <?php foreach ($media['photos'] as $photo): ?>
      <article class="photo-card">
        <img
          class="photo-thumb"
          src="uploads/photos/<?= htmlspecialchars($photo['filename']) ?>"
          alt="<?= htmlspecialchars($photo['title']) ?>"
          loading="lazy"
          onclick="openLightbox(this.src, <?= json_encode($photo['title']) ?>)"
          style="cursor:zoom-in"
        >
        <div class="photo-info">
          <h3><?= htmlspecialchars($photo['title']) ?></h3>
          <?php if (!empty($photo['description'])): ?>
          <p class="photo-meta"><?= htmlspecialchars($photo['description']) ?></p>
          <?php endif; ?>
        </div>
        <div class="photo-actions">
          <a href="api/download.php?type=photos&id=<?= urlencode($photo['id']) ?>"
             class="btn btn-outline btn-sm" download>
            ⬇️ Télécharger HD
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>

  <!-- ── VIDEOS tab ─────────────────────────────────────────── -->
  <section id="panel-videos" class="tab-panel" role="tabpanel" aria-labelledby="tab-videos">
    <?php if ($videoCount === 0): ?>
    <div class="empty-state">
      <div class="empty-icon">🎬</div>
      <p>Aucune vidéo disponible pour le moment.</p>
    </div>
    <?php else: ?>
    <div class="video-grid">
      <?php foreach ($media['videos'] as $video): ?>
      <article class="video-card">
        <video
          class="video-player"
          controls
          preload="metadata"
          aria-label="<?= htmlspecialchars($video['title']) ?>"
        >
          <source src="uploads/videos/<?= htmlspecialchars($video['filename']) ?>">
          Votre navigateur ne supporte pas la lecture vidéo.
        </video>
        <div class="video-info">
          <h3><?= htmlspecialchars($video['title']) ?></h3>
          <?php if (!empty($video['description'])): ?>
          <p class="text-muted"><?= htmlspecialchars($video['description']) ?></p>
          <?php endif; ?>
        </div>
        <div class="video-actions">
          <a href="api/download.php?type=videos&id=<?= urlencode($video['id']) ?>"
             class="btn btn-outline btn-sm" download>
            ⬇️ Télécharger
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>

</main>

<!-- ── Lightbox ─────────────────────────────────────────────── -->
<div class="lightbox" id="lightbox" onclick="closeLightbox()" role="dialog" aria-modal="true" aria-label="Aperçu image">
  <div class="lightbox-content" onclick="event.stopPropagation()">
    <button class="lightbox-close" onclick="closeLightbox()" aria-label="Fermer l'aperçu">✕</button>
    <img id="lightbox-img" src="" alt="">
  </div>
</div>

<!-- ── Footer ────────────────────────────────────────────────── -->
<footer class="portal-footer">
  <p>© <?= date('Y') ?> <strong><?= BRAND_NAME ?></strong> — Document confidentiel réservé aux pharmacies partenaires.</p>
</footer>

<script>
// ── Tab switching ──────────────────────────────────────────────
function switchTab(tabId, btn) {
  // Hide all panels
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => {
    b.classList.remove('active');
    b.setAttribute('aria-selected', 'false');
  });
  // Show selected
  document.getElementById('panel-' + tabId).classList.add('active');
  btn.classList.add('active');
  btn.setAttribute('aria-selected', 'true');
}

// ── Lightbox ───────────────────────────────────────────────────
function openLightbox(src, alt) {
  const lb = document.getElementById('lightbox');
  document.getElementById('lightbox-img').src = src;
  document.getElementById('lightbox-img').alt = alt;
  lb.classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeLightbox() {
  document.getElementById('lightbox').classList.remove('open');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeLightbox();
});
</script>
</body>
</html>
