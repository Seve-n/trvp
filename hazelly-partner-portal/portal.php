<?php
/**
 * Hazelly Hair Love — Partner Portal
 * portal.php — Partner media gallery
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/media.php';
require_auth('partner', 'index.php');

$type   = trim($_GET['type']   ?? 'all');
$search = trim($_GET['search'] ?? '');

$allowed_types = ['all', 'pdf', 'photo', 'video'];
if (!in_array($type, $allowed_types, true)) $type = 'all';

$items  = $search ? media_search($search, $type) : media_get_by_type($type);
$counts = media_counts();

/* ── Helpers ────────────────────────────────────────────────── */
function badge_html(string $t): string {
    $map = [
        'pdf'   => '<span class="badge badge-pdf">📄 PDF</span>',
        'photo' => '<span class="badge badge-photo">🖼 Photo</span>',
        'video' => '<span class="badge badge-video">🎬 Vidéo</span>',
    ];
    return $map[$t] ?? '';
}

function type_icon(string $t): string {
    return ['pdf' => '📄', 'photo' => '🖼', 'video' => '🎬'][$t] ?? '📁';
}

function format_date(string $iso): string {
    try {
        return (new DateTime($iso))->format('d M Y');
    } catch (Exception $e) {
        return '';
    }
}

function build_url(string $base, array $params): string {
    return $base . '?' . http_build_query(array_filter($params, fn($v) => $v !== ''));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Espace Partenaire — Hazelly Hair Love</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/portal.css">
</head>
<body>
<div class="portal-layout">

  <!-- ══ Header ═══════════════════════════════════════════════ -->
  <header class="portal-header" role="banner">
    <div class="portal-header-inner">
      <div class="portal-brand">
        <div class="portal-brand-icon">🌿</div>
        <span>Hazelly Hair Love</span>
      </div>
      <nav class="portal-nav" aria-label="Navigation principale">
        <a class="portal-nav-btn active" href="portal.php">📁 Médiathèque</a>
        <a class="portal-nav-btn" href="portal.php#contact">📞 Contact</a>
      </nav>
      <div class="portal-header-actions">
        <a href="logout.php" class="btn btn-ghost btn-sm">🔓 Déconnexion</a>
        <div class="portal-avatar" title="Pharmacie partenaire">💊</div>
      </div>
    </div>
  </header>

  <!-- ══ Main ══════════════════════════════════════════════════ -->
  <main class="portal-main">

    <!-- Hero -->
    <div class="portal-hero">
      <div class="portal-hero-text">
        <h2>Bonjour, bienvenue dans votre espace 👋</h2>
        <p>Retrouvez ici l'ensemble des ressources marketing et documentaires<br>
           mises à votre disposition par Hazelly Hair Love.</p>
      </div>
      <div class="portal-hero-stats">
        <div class="portal-hero-stat">
          <div class="number"><?= $counts['pdf'] ?></div>
          <div class="label">Documents</div>
        </div>
        <div class="portal-hero-stat">
          <div class="number"><?= $counts['photo'] ?></div>
          <div class="label">Photos</div>
        </div>
        <div class="portal-hero-stat">
          <div class="number"><?= $counts['video'] ?></div>
          <div class="label">Vidéos</div>
        </div>
      </div>
    </div>

    <!-- Section header -->
    <div class="section-header">
      <div>
        <h2 class="section-title">📁 Médiathèque</h2>
        <p class="section-subtitle">Tous les contenus disponibles pour votre pharmacie</p>
      </div>
    </div>

    <!-- Filters -->
    <form class="filter-bar" method="get" action="portal.php">
      <div class="filter-search-wrap">
        <span class="icon">🔍</span>
        <input
          type="search"
          name="search"
          class="form-control filter-search"
          placeholder="Rechercher un document, une photo…"
          value="<?= htmlspecialchars($search) ?>"
        >
        <?php if ($type !== 'all'): ?>
          <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
        <?php endif; ?>
      </div>
      <div class="filter-tabs">
        <?php foreach (['all' => 'Tout', 'pdf' => '📄 Documents', 'photo' => '🖼 Photos', 'video' => '🎬 Vidéos'] as $t => $label): ?>
          <a
            href="<?= build_url('portal.php', ['type' => $t !== 'all' ? $t : '', 'search' => $search]) ?>"
            class="filter-tab<?= $type === $t ? ' active' : '' ?>"
          ><?= $label ?></a>
        <?php endforeach; ?>
      </div>
    </form>

    <!-- Media grid -->
    <div class="media-grid" role="list">
      <?php if (empty($items)): ?>
        <div class="empty-state">
          <div class="empty-state-icon">🔍</div>
          <h3>Aucun contenu trouvé</h3>
          <p>Essayez un autre mot-clé ou une autre catégorie.</p>
        </div>
      <?php else: ?>
        <?php foreach ($items as $item): ?>
          <?php
            $id         = htmlspecialchars($item['id']);
            $title      = htmlspecialchars($item['title'] ?? '');
            $desc       = htmlspecialchars($item['description'] ?? '');
            $file_name  = htmlspecialchars($item['file_name'] ?? '');
            $file_size  = htmlspecialchars($item['file_size'] ?? '');
            $url        = htmlspecialchars($item['url'] ?? '');
            $file_path  = htmlspecialchars($item['file_path'] ?? '');
            $date       = format_date($item['addedAt'] ?? '');
            $is_new     = !empty($item['is_new']);
            $t          = $item['type'] ?? 'pdf';
            $thumb_class = "media-card-thumb-{$t}";
            $download_href = $file_path ? htmlspecialchars($item['file_path']) : $url;
          ?>
          <div class="media-card card" role="listitem">
            <div class="media-card-thumb <?= $thumb_class ?>">
              <?php if ($t === 'photo' && $file_path): ?>
                <img src="<?= htmlspecialchars($item['file_path']) ?>"
                     alt="<?= $title ?>" loading="lazy">
              <?php elseif ($t === 'photo' && $url): ?>
                <img src="<?= $url ?>" alt="<?= $title ?>" loading="lazy">
              <?php elseif ($t === 'video'): ?>
                <div class="media-card-thumb-icon"><?= type_icon($t) ?></div>
                <?php if ($url): ?>
                <div class="play-overlay" data-video-url="<?= $url ?>" data-video-title="<?= $title ?>">
                  <div class="play-btn">▶</div>
                </div>
                <?php endif; ?>
              <?php else: ?>
                <div class="media-card-thumb-icon"><?= type_icon($t) ?></div>
              <?php endif; ?>
            </div>

            <div class="media-card-body">
              <div class="media-card-meta">
                <?= badge_html($t) ?>
                <?php if ($is_new): ?><span class="badge badge-new">Nouveau</span><?php endif; ?>
              </div>
              <div class="media-card-title"><?= $title ?></div>
              <div class="media-card-desc"><?= $desc ?></div>
              <div class="media-card-date">
                <?= $date ?><?= $file_size ? ' · ' . $file_size : '' ?>
              </div>
            </div>

            <div class="media-card-actions">
              <?php if ($download_href && $download_href !== '#'): ?>
                <a
                  href="<?= $download_href ?>"
                  download="<?= $file_name ?: $title ?>"
                  class="btn-download"
                  target="_blank"
                  rel="noopener noreferrer"
                >⬇ Télécharger</a>
              <?php else: ?>
                <button class="btn-download" disabled style="opacity:.5;cursor:not-allowed;">
                  ⬇ Non disponible
                </button>
              <?php endif; ?>

              <?php if ($t !== 'pdf'): ?>
                <button
                  class="btn-preview"
                  data-type="<?= $t ?>"
                  <?php if ($t === 'photo'): ?>
                    data-src="<?= $file_path ?: $url ?>"
                    data-alt="<?= $title ?>"
                  <?php elseif ($t === 'video' && $url): ?>
                    data-video-url="<?= $url ?>"
                    data-video-title="<?= $title ?>"
                  <?php endif; ?>
                  title="Aperçu"
                >👁</button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Contact section -->
    <section id="contact" style="margin-top:3rem;">
      <div class="section-header" style="margin-bottom:1.25rem;">
        <div>
          <h2 class="section-title">📞 Contact &amp; Support</h2>
          <p class="section-subtitle">Votre équipe Hazelly Hair Love est à votre disposition</p>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;">
        <div class="card" style="padding:1.75rem;">
          <div style="font-size:2rem;margin-bottom:1rem;">👩‍💼</div>
          <h3 style="font-family:var(--font-heading);margin-bottom:.4rem;">Responsable Comptes Clés</h3>
          <p style="color:var(--color-text-light);font-size:.9rem;margin-bottom:1rem;">Commandes, disponibilité produits, protocoles de vente.</p>
          <p style="font-size:.9rem;"><strong>📧</strong> partenaires@hazellyhairlove.fr</p>
          <p style="font-size:.9rem;margin-top:.3rem;"><strong>📱</strong> +33 (0)1 00 00 00 00</p>
        </div>
        <div class="card" style="padding:1.75rem;">
          <div style="font-size:2rem;margin-bottom:1rem;">🎨</div>
          <h3 style="font-family:var(--font-heading);margin-bottom:.4rem;">Équipe Communication</h3>
          <p style="color:var(--color-text-light);font-size:.9rem;margin-bottom:1rem;">Visuels spécifiques, adaptations de format, campagnes locales.</p>
          <p style="font-size:.9rem;"><strong>📧</strong> marketing@hazellyhairlove.fr</p>
        </div>
        <div class="card" style="padding:1.75rem;">
          <div style="font-size:2rem;margin-bottom:1rem;">🔧</div>
          <h3 style="font-family:var(--font-heading);margin-bottom:.4rem;">Support Technique</h3>
          <p style="color:var(--color-text-light);font-size:.9rem;margin-bottom:1rem;">Problème d'accès, fichier manquant, lien défaillant.</p>
          <p style="font-size:.9rem;"><strong>📧</strong> support@hazellyhairlove.fr</p>
        </div>
      </div>
    </section>

  </main>
</div>

<!-- Preview modal -->
<div class="modal-overlay hidden" id="preview-overlay" role="dialog" aria-modal="true" aria-labelledby="preview-title">
  <div class="modal preview-modal">
    <div class="modal-header">
      <h3 class="modal-title" id="preview-title">Aperçu</h3>
      <button class="modal-close" id="preview-close" aria-label="Fermer">✕</button>
    </div>
    <div class="modal-body" id="preview-body"></div>
    <div class="modal-footer">
      <button class="btn btn-secondary btn-sm" id="preview-close-btn">Fermer</button>
    </div>
  </div>
</div>

<div id="toast-container"></div>
<script src="js/app.js"></script>
</body>
</html>
