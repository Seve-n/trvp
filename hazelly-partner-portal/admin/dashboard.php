<?php
define('HAZELLY_PORTAL', true);
require_once __DIR__ . '/../config.php';
require_admin_session();

$media   = get_media();
$docCount   = count($media['documents'] ?? []);
$photoCount = count($media['photos']    ?? []);
$videoCount = count($media['videos']    ?? []);
$total      = $docCount + $photoCount + $videoCount;

// Flash message handling
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
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
<body>

<div class="admin-layout">

  <!-- ── Sidebar ───────────────────────────────────────────── -->
  <aside class="admin-sidebar" aria-label="Navigation admin">
    <div class="admin-sidebar-logo">
      <div class="brand-dot" aria-hidden="true">⚙️</div>
      <h2><?= BRAND_NAME ?></h2>
      <p>Administration</p>
    </div>

    <nav class="admin-nav" aria-label="Menu">
      <button class="admin-nav-item active" onclick="showSection('overview', this)">
        📊 Tableau de bord
      </button>
      <button class="admin-nav-item" onclick="showSection('upload', this)">
        ⬆️ Ajouter des médias
      </button>
      <button class="admin-nav-item" onclick="showSection('manage', this)">
        📁 Gérer le contenu
      </button>
      <div class="admin-nav-divider"></div>
      <button class="admin-nav-item" onclick="showSection('settings', this)">
        🔒 Mots de passe
      </button>
    </nav>

    <div class="admin-sidebar-footer">
      <a href="logout.php" class="btn btn-outline btn-sm" style="width:100%; justify-content:center;">
        🚪 Déconnexion
      </a>
    </div>
  </aside>

  <!-- ── Main content ──────────────────────────────────────── -->
  <div class="admin-main">

    <header class="admin-header">
      <h1 id="section-title">Tableau de bord</h1>
      <a href="../portal.php" target="_blank" class="btn btn-outline btn-sm">
        👁️ Voir le portail
      </a>
    </header>

    <div class="admin-content">

      <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['message']) ?>
      </div>
      <?php endif; ?>

      <!-- ══ OVERVIEW ══ -->
      <section id="section-overview" class="admin-section active">
        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-icon">📄</div>
            <div class="stat-info">
              <div class="stat-value"><?= $docCount ?></div>
              <div class="stat-label">Documents</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">🖼️</div>
            <div class="stat-info">
              <div class="stat-value"><?= $photoCount ?></div>
              <div class="stat-label">Photos</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">🎬</div>
            <div class="stat-info">
              <div class="stat-value"><?= $videoCount ?></div>
              <div class="stat-label">Vidéos</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
              <div class="stat-value"><?= $total ?></div>
              <div class="stat-label">Total</div>
            </div>
          </div>
        </div>

        <div class="panel-card">
          <h2>🚀 Actions rapides</h2>
          <div style="display:flex; flex-wrap:wrap; gap:0.75rem;">
            <button onclick="showSection('upload', document.querySelector('[onclick*=upload]'))"
                    class="btn btn-primary">
              ⬆️ Ajouter un média
            </button>
            <button onclick="showSection('manage', document.querySelector('[onclick*=manage]'))"
                    class="btn btn-outline">
              📁 Gérer le contenu
            </button>
            <button onclick="showSection('settings', document.querySelector('[onclick*=settings]'))"
                    class="btn btn-outline">
              🔒 Changer les mots de passe
            </button>
          </div>
        </div>

        <div class="panel-card">
          <h2>ℹ️ Aide rapide</h2>
          <ul style="font-size:0.9rem; padding-left:1.25rem; color:var(--clr-text-light); line-height:1.9;">
            <li>Utilisez <strong>Ajouter des médias</strong> pour uploader PDF, photos ou vidéos.</li>
            <li>Chaque fichier peut avoir un titre et une description personnalisés.</li>
            <li>Depuis <strong>Gérer le contenu</strong>, supprimez les fichiers obsolètes.</li>
            <li>Le mot de passe partenaire peut être changé dans <strong>Mots de passe</strong>.</li>
          </ul>
        </div>
      </section>

      <!-- ══ UPLOAD ══ -->
      <section id="section-upload" class="admin-section">
        <div class="panel-card">
          <h2>⬆️ Ajouter un nouveau média</h2>
          <form id="upload-form" method="POST" action="../api/upload.php" enctype="multipart/form-data">

            <div class="form-group">
              <label class="form-label" for="media-type">Catégorie</label>
              <select class="form-select" id="media-type" name="type" required>
                <option value="">— Sélectionner —</option>
                <option value="documents">📄 Document (PDF)</option>
                <option value="photos">🖼️ Photo (JPG, PNG, WEBP)</option>
                <option value="videos">🎬 Vidéo (MP4, WEBM)</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="media-title">Titre</label>
              <input class="form-input" type="text" id="media-title" name="title"
                     placeholder="Ex : Fiche produit Shampooing Éclat" required maxlength="120">
            </div>

            <div class="form-group">
              <label class="form-label" for="media-desc">Description (optionnelle)</label>
              <textarea class="form-textarea" id="media-desc" name="description"
                        placeholder="Courte description du document ou de l'image..."
                        maxlength="300"></textarea>
            </div>

            <div class="form-group">
              <label class="form-label" for="media-file">Fichier</label>
              <div class="upload-zone" id="upload-zone"
                   onclick="document.getElementById('media-file').click()"
                   ondragover="handleDragOver(event)" ondrop="handleDrop(event)">
                <div class="upload-icon" aria-hidden="true">📂</div>
                <p id="upload-zone-label">
                  <strong>Cliquez ici</strong> ou glissez un fichier (max 100 Mo)
                </p>
              </div>
              <input type="file" id="media-file" name="file" required style="display:none"
                     onchange="updateUploadZone(this)">
            </div>

            <div id="upload-progress" style="display:none; margin-bottom:1rem;">
              <p style="font-size:0.85rem; color:var(--clr-text-light); margin-bottom:0.5rem;">
                Upload en cours…
              </p>
              <div style="background:var(--clr-border); border-radius:4px; height:6px; overflow:hidden;">
                <div id="progress-bar" style="width:0; height:100%; background:var(--clr-gold); transition:width 0.3s;"></div>
              </div>
            </div>

            <button type="submit" class="btn btn-primary" id="upload-btn">
              ⬆️ Envoyer le fichier
            </button>
          </form>
        </div>
      </section>

      <!-- ══ MANAGE ══ -->
      <section id="section-manage" class="admin-section">
        <?php
        $cats = [
          'documents' => ['label'=>'Documents', 'icon'=>'📄'],
          'photos'    => ['label'=>'Photos',    'icon'=>'🖼️'],
          'videos'    => ['label'=>'Vidéos',    'icon'=>'🎬'],
        ];
        foreach ($cats as $catKey => $catInfo):
          $items = $media[$catKey] ?? [];
        ?>
        <div class="panel-card">
          <h2><?= $catInfo['icon'] ?> <?= $catInfo['label'] ?>
            <span style="font-size:0.9rem; color:var(--clr-text-light); font-weight:400;">
              (<?= count($items) ?> fichier<?= count($items) > 1 ? 's' : '' ?>)
            </span>
          </h2>

          <?php if (empty($items)): ?>
          <p class="text-muted">Aucun fichier dans cette catégorie.</p>
          <?php else: ?>
          <div class="admin-table-wrap">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Titre</th>
                  <th>Fichier</th>
                  <th>Date d'ajout</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($item['title']) ?></strong>
                    <?php if (!empty($item['description'])): ?>
                    <br><small class="text-muted"><?= htmlspecialchars($item['description']) ?></small>
                    <?php endif; ?>
                  </td>
                  <td><code style="font-size:0.8rem;"><?= htmlspecialchars($item['filename']) ?></code></td>
                  <td><?= date('d/m/Y', $item['created_at']) ?></td>
                  <td>
                    <form method="POST" action="../api/delete.php"
                          onsubmit="return confirm('Supprimer ' + <?= json_encode('« ' . $item['title'] . ' »') ?> + ' ?')">
                      <input type="hidden" name="type" value="<?= $catKey ?>">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                      <button type="submit" class="btn btn-danger btn-sm">🗑️ Supprimer</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </section>

      <!-- ══ SETTINGS ══ -->
      <section id="section-settings" class="admin-section">
        <div class="panel-card">
          <h2>🔑 Mot de passe partenaire</h2>
          <p class="text-muted mb-2">Ce mot de passe est partagé avec toutes les pharmacies partenaires.</p>
          <form method="POST" action="../api/change_password.php" id="form-partner-pw">
            <input type="hidden" name="target" value="partner">
            <div class="form-group">
              <label class="form-label" for="p-new">Nouveau mot de passe</label>
              <input class="form-input" type="password" id="p-new" name="new_password"
                     minlength="6" required autocomplete="new-password"
                     oninput="checkStrength(this, 'p-bar')">
              <div class="pw-strength-bar">
                <div class="pw-strength-fill" id="p-bar"></div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="p-confirm">Confirmer le mot de passe</label>
              <input class="form-input" type="password" id="p-confirm" name="confirm_password"
                     minlength="6" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
          </form>
        </div>

        <div class="panel-card">
          <h2>🛡️ Mot de passe administrateur</h2>
          <p class="text-muted mb-2">Ce mot de passe protège l'accès au panneau d'administration.</p>
          <form method="POST" action="../api/change_password.php" id="form-admin-pw">
            <input type="hidden" name="target" value="admin">
            <div class="form-group">
              <label class="form-label" for="a-current">Mot de passe actuel</label>
              <input class="form-input" type="password" id="a-current" name="current_password"
                     required autocomplete="current-password">
            </div>
            <div class="form-group">
              <label class="form-label" for="a-new">Nouveau mot de passe</label>
              <input class="form-input" type="password" id="a-new" name="new_password"
                     minlength="8" required autocomplete="new-password"
                     oninput="checkStrength(this, 'a-bar')">
              <div class="pw-strength-bar">
                <div class="pw-strength-fill" id="a-bar"></div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="a-confirm">Confirmer le mot de passe</label>
              <input class="form-input" type="password" id="a-confirm" name="confirm_password"
                     minlength="8" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
          </form>
        </div>
      </section>

    </div><!-- /admin-content -->
  </div><!-- /admin-main -->
</div><!-- /admin-layout -->

<script>
// ── Section switching ──────────────────────────────────────────
const sectionTitles = {
  overview: 'Tableau de bord',
  upload:   'Ajouter des médias',
  manage:   'Gérer le contenu',
  settings: 'Mots de passe',
};
function showSection(id, btn) {
  document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.admin-nav-item').forEach(b => b.classList.remove('active'));
  document.getElementById('section-' + id).classList.add('active');
  if (btn) btn.classList.add('active');
  document.getElementById('section-title').textContent = sectionTitles[id] || id;
}

// ── Upload UX ──────────────────────────────────────────────────
function updateUploadZone(input) {
  if (input.files && input.files[0]) {
    document.getElementById('upload-zone-label').innerHTML =
      '📎 <strong>' + input.files[0].name + '</strong>';
  }
}
function handleDragOver(e) {
  e.preventDefault();
  document.getElementById('upload-zone').classList.add('dragover');
}
function handleDrop(e) {
  e.preventDefault();
  document.getElementById('upload-zone').classList.remove('dragover');
  const files = e.dataTransfer.files;
  if (files.length) {
    const input = document.getElementById('media-file');
    const dt = new DataTransfer();
    dt.items.add(files[0]);
    input.files = dt.files;
    updateUploadZone(input);
  }
}

// ── Password strength ──────────────────────────────────────────
function checkStrength(input, barId) {
  const val = input.value;
  const bar  = document.getElementById(barId);
  let score  = 0;
  if (val.length >= 8)  score++;
  if (val.length >= 12) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const pct   = (score / 5) * 100;
  const color = score <= 1 ? '#C0392B' : score <= 3 ? '#E67E22' : '#2E7D52';
  bar.style.width    = pct + '%';
  bar.style.background = color;
}

// ── Form validation (partner PW match) ───────────────────────
document.getElementById('form-partner-pw').addEventListener('submit', function(e) {
  const np = document.getElementById('p-new').value;
  const cp = document.getElementById('p-confirm').value;
  if (np !== cp) { e.preventDefault(); alert('Les mots de passe ne correspondent pas.'); }
});
document.getElementById('form-admin-pw').addEventListener('submit', function(e) {
  const np = document.getElementById('a-new').value;
  const cp = document.getElementById('a-confirm').value;
  if (np !== cp) { e.preventDefault(); alert('Les mots de passe ne correspondent pas.'); }
});

// ── Restore section from URL hash ────────────────────────────
const hash = location.hash.replace('#', '');
if (['overview','upload','manage','settings'].includes(hash)) {
  const btn = document.querySelector(`[onclick*="${hash}"]`);
  showSection(hash, btn);
}
</script>
</body>
</html>
