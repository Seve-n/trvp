<?php
/**
 * Hazelly Hair Love — Partner Portal
 * admin.php — Administration panel
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/media.php';
require_admin('index.php?admin=1');

$counts  = media_counts();
$section = trim($_GET['section'] ?? 'dashboard');
$allowed_sections = ['dashboard', 'pdf', 'photo', 'video', 'settings'];
if (!in_array($section, $allowed_sections, true)) $section = 'dashboard';

/* ── Flash message from redirects ───────────────────────────── */
$flash = '';
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

function type_label(string $t): string {
    return ['pdf' => 'Document PDF', 'photo' => 'Photo', 'video' => 'Vidéo'][$t] ?? $t;
}
function format_date(string $iso): string {
    try { return (new DateTime($iso))->format('d M Y'); } catch (Exception $e) { return ''; }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Administration — Hazelly Hair Love</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="admin-layout">

  <!-- ══ Sidebar ═══════════════════════════════════════════════ -->
  <aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar-header">
      <div class="admin-brand">
        <div class="admin-brand-icon">🌿</div>
        <span class="admin-brand-name">Hazelly</span>
      </div>
      <span class="admin-panel-label">Administration</span>
    </div>

    <nav class="admin-nav">
      <div class="admin-nav-section">
        <div class="admin-nav-section-title">Tableau de bord</div>
        <a class="admin-nav-item <?= $section === 'dashboard' ? 'active' : '' ?>"
           href="admin.php?section=dashboard">
          <span class="icon">📊</span> Vue d'ensemble
        </a>
      </div>
      <div class="admin-nav-section">
        <div class="admin-nav-section-title">Médiathèque</div>
        <a class="admin-nav-item <?= $section === 'pdf' ? 'active' : '' ?>"
           href="admin.php?section=pdf">
          <span class="icon">📄</span> Documents PDF
        </a>
        <a class="admin-nav-item <?= $section === 'photo' ? 'active' : '' ?>"
           href="admin.php?section=photo">
          <span class="icon">🖼</span> Photos
        </a>
        <a class="admin-nav-item <?= $section === 'video' ? 'active' : '' ?>"
           href="admin.php?section=video">
          <span class="icon">🎬</span> Vidéos
        </a>
      </div>
      <div class="admin-nav-section">
        <div class="admin-nav-section-title">Paramètres</div>
        <a class="admin-nav-item <?= $section === 'settings' ? 'active' : '' ?>"
           href="admin.php?section=settings">
          <span class="icon">🔐</span> Mots de passe
        </a>
      </div>
    </nav>

    <div class="admin-sidebar-footer">
      <div class="admin-user-info">
        <div class="admin-avatar">A</div>
        <div>
          <div class="admin-user-name">Administrateur</div>
          <div class="admin-user-role">Hazelly Hair Love</div>
        </div>
      </div>
      <a href="logout.php" class="btn btn-ghost btn-sm btn-full" style="color:rgba(255,255,255,.6);">
        🔓 Déconnexion
      </a>
    </div>
  </aside>

  <!-- ══ Main ══════════════════════════════════════════════════ -->
  <div class="admin-main">
    <div class="admin-topbar">
      <button class="admin-mobile-toggle" id="mobile-menu-toggle" aria-label="Menu">☰</button>
      <h2 class="admin-topbar-title">
        <?= ['dashboard' => 'Tableau de bord', 'pdf' => 'Documents PDF',
             'photo' => 'Photos', 'video' => 'Vidéos', 'settings' => 'Mots de passe'][$section] ?? 'Administration' ?>
      </h2>
      <div class="admin-topbar-actions">
        <a href="portal.php" class="btn btn-secondary btn-sm" target="_blank" rel="noopener">
          👁 Voir le portail
        </a>
      </div>
    </div>

    <div class="admin-content">

      <?php if ($flash): ?>
        <div class="alert-success" role="alert" style="
          background:#E8F5E9;border:1.5px solid #A5D6A7;color:#2E7D32;
          padding:.85rem 1.25rem;border-radius:8px;margin-bottom:1.5rem;
          display:flex;align-items:center;gap:.6rem;">
          ✓ <?= htmlspecialchars($flash) ?>
        </div>
      <?php endif; ?>

      <!-- ════ DASHBOARD ════════════════════════════════════════ -->
      <?php if ($section === 'dashboard'): ?>
        <div class="admin-stats-grid">
          <div class="stat-card">
            <div class="stat-card-icon stat-icon-total">📁</div>
            <div class="stat-card-info">
              <div class="value"><?= $counts['total'] ?></div>
              <div class="label">Total contenus</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-card-icon stat-icon-pdf">📄</div>
            <div class="stat-card-info">
              <div class="value"><?= $counts['pdf'] ?></div>
              <div class="label">Documents PDF</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-card-icon stat-icon-photo">🖼</div>
            <div class="stat-card-info">
              <div class="value"><?= $counts['photo'] ?></div>
              <div class="label">Photos</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-card-icon stat-icon-video">🎬</div>
            <div class="stat-card-info">
              <div class="value"><?= $counts['video'] ?></div>
              <div class="label">Vidéos</div>
            </div>
          </div>
        </div>

        <div class="admin-section">
          <div class="admin-section-header">
            <h3 class="admin-section-title">📋 Guide de démarrage rapide</h3>
          </div>
          <div style="padding:1.5rem;">
            <p style="color:var(--color-text-light);font-size:.95rem;line-height:1.7;margin-bottom:1rem;">
              Bienvenue dans le panneau d'administration. Depuis ici vous pouvez :
            </p>
            <ul style="color:var(--color-text-light);font-size:.9rem;line-height:2;padding-left:1.25rem;list-style:disc;">
              <li>Ajouter, modifier et supprimer des contenus dans la <strong>médiathèque</strong></li>
              <li>Uploader des fichiers PDF, photos et vidéos directement sur le serveur</li>
              <li>Modifier le <strong>mot de passe partenaire</strong> partagé avec les pharmacies</li>
              <li>Modifier votre <strong>mot de passe administrateur</strong></li>
            </ul>
            <div style="margin-top:1.5rem;display:flex;gap:.75rem;flex-wrap:wrap;">
              <a href="admin.php?section=pdf" class="btn btn-secondary btn-sm">+ Ajouter un PDF</a>
              <a href="admin.php?section=photo" class="btn btn-secondary btn-sm">+ Ajouter une photo</a>
              <a href="admin.php?section=video" class="btn btn-secondary btn-sm">+ Ajouter une vidéo</a>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- ════ MEDIA TABLES (pdf / photo / video) ════════════════ -->
      <?php if (in_array($section, ['pdf', 'photo', 'video'])): ?>
        <?php $type_items = media_get_by_type($section); ?>

        <!-- Add form -->
        <div class="admin-section" style="margin-bottom:1.5rem;">
          <div class="admin-section-header">
            <h3 class="admin-section-title">+ Ajouter <?= type_label($section) === 'Photo' ? 'une ' : 'un ' ?><?= strtolower(type_label($section)) ?></h3>
          </div>
          <div style="padding:1.5rem;">
            <form method="post" action="api/media.php" enctype="multipart/form-data">
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="type"   value="<?= $section ?>">
              <input type="hidden" name="redirect" value="admin.php?section=<?= $section ?>">

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                  <label for="add-title">Titre <span style="color:var(--color-error);">*</span></label>
                  <input type="text" id="add-title" name="title" class="form-control" required
                    placeholder="Ex : Fiche technique — Sérum Réparateur">
                </div>
                <div class="form-group">
                  <label for="add-size">Taille affichée</label>
                  <input type="text" id="add-size" name="file_size" class="form-control"
                    placeholder="Ex : 2.4 Mo">
                </div>
              </div>
              <div class="form-group">
                <label for="add-desc">Description</label>
                <textarea id="add-desc" name="description" class="form-control" rows="2"
                  placeholder="Courte description visible par les pharmaciens…" style="resize:vertical;"></textarea>
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                  <label for="add-file">Uploader un fichier</label>
                  <input type="file" id="add-file" name="file" class="form-control"
                    accept="<?= $section === 'pdf' ? '.pdf,application/pdf' : ($section === 'photo' ? 'image/*' : 'video/*') ?>"
                    style="padding:.5rem;">
                  <small style="color:var(--color-text-light);font-size:.78rem;">Max 200 Mo</small>
                </div>
                <div class="form-group">
                  <label for="add-url">Ou URL externe</label>
                  <input type="url" id="add-url" name="url" class="form-control"
                    placeholder="https://… (Google Drive, YouTube embed…)">
                </div>
              </div>

              <div class="form-group" style="flex-direction:row;align-items:center;gap:.75rem;">
                <input type="checkbox" name="is_new" id="add-isnew" value="1" checked
                  style="width:18px;height:18px;accent-color:var(--color-primary);">
                <label for="add-isnew" style="margin:0;font-size:.9rem;text-transform:none;letter-spacing:0;">
                  Marquer comme « Nouveau »
                </label>
              </div>
              <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
          </div>
        </div>

        <!-- Table -->
        <div class="admin-section">
          <div class="admin-section-header">
            <h3 class="admin-section-title">
              <?= ['pdf'=>'📄','photo'=>'🖼','video'=>'🎬'][$section] ?> Liste des <?= $section === 'pdf' ? 'documents' : ($section === 'photo' ? 'photos' : 'vidéos') ?>
              <span style="font-size:.85rem;color:var(--color-text-light);font-family:var(--font-body);font-weight:400;">
                (<?= count($type_items) ?> élément<?= count($type_items) > 1 ? 's' : '' ?>)
              </span>
            </h3>
          </div>
          <?php if (empty($type_items)): ?>
            <div style="padding:2rem;text-align:center;color:var(--color-text-light);">
              Aucun contenu. Ajoutez-en un via le formulaire ci-dessus.
            </div>
          <?php else: ?>
            <div style="overflow-x:auto;">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Taille</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($type_items as $item): ?>
                    <tr>
                      <td>
                        <strong><?= htmlspecialchars($item['title'] ?? '') ?></strong>
                        <?php if (!empty($item['is_new'])): ?>
                          <span class="badge badge-new" style="margin-left:.4rem;font-size:.68rem;">Nouveau</span>
                        <?php endif; ?>
                      </td>
                      <td style="color:var(--color-text-light);font-size:.85rem;">
                        <?= htmlspecialchars($item['description'] ?? '') ?>
                      </td>
                      <td style="font-size:.82rem;color:var(--color-text-light);">
                        <?= htmlspecialchars($item['file_size'] ?? '—') ?>
                      </td>
                      <td style="font-size:.82rem;color:var(--color-text-light);">
                        <?= format_date($item['addedAt'] ?? '') ?>
                      </td>
                      <td>
                        <div class="admin-table-actions">
                          <a href="admin.php?section=<?= $section ?>&edit=<?= htmlspecialchars($item['id']) ?>"
                             class="btn btn-secondary btn-sm" title="Modifier">✏️</a>
                          <form method="post" action="api/media.php" style="display:inline;"
                                onsubmit="return confirm('Supprimer ce contenu ?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                            <input type="hidden" name="redirect" value="admin.php?section=<?= $section ?>">
                            <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">🗑</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>

        <!-- Edit form (shown when ?edit=ID) -->
        <?php
          $edit_id = trim($_GET['edit'] ?? '');
          $edit_item = $edit_id ? media_get_by_id($edit_id) : null;
        ?>
        <?php if ($edit_item): ?>
          <div class="admin-section" style="margin-top:1.5rem;">
            <div class="admin-section-header">
              <h3 class="admin-section-title">✏️ Modifier : <?= htmlspecialchars($edit_item['title']) ?></h3>
            </div>
            <div style="padding:1.5rem;">
              <form method="post" action="api/media.php" enctype="multipart/form-data">
                <input type="hidden" name="action"   value="update">
                <input type="hidden" name="id"       value="<?= htmlspecialchars($edit_item['id']) ?>">
                <input type="hidden" name="redirect" value="admin.php?section=<?= $section ?>">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                  <div class="form-group">
                    <label>Titre *</label>
                    <input type="text" name="title" class="form-control" required
                      value="<?= htmlspecialchars($edit_item['title'] ?? '') ?>">
                  </div>
                  <div class="form-group">
                    <label>Taille affichée</label>
                    <input type="text" name="file_size" class="form-control"
                      value="<?= htmlspecialchars($edit_item['file_size'] ?? '') ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label>Description</label>
                  <textarea name="description" class="form-control" rows="2"
                    style="resize:vertical;"><?= htmlspecialchars($edit_item['description'] ?? '') ?></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                  <div class="form-group">
                    <label>Nouveau fichier (optionnel)</label>
                    <input type="file" name="file" class="form-control" style="padding:.5rem;"
                      accept="<?= $section === 'pdf' ? '.pdf,application/pdf' : ($section === 'photo' ? 'image/*' : 'video/*') ?>">
                    <?php if (!empty($edit_item['file_path'])): ?>
                      <small style="color:var(--color-text-light);font-size:.78rem;">
                        Actuel : <?= htmlspecialchars(basename($edit_item['file_path'])) ?>
                      </small>
                    <?php endif; ?>
                  </div>
                  <div class="form-group">
                    <label>URL externe</label>
                    <input type="url" name="url" class="form-control"
                      value="<?= htmlspecialchars($edit_item['url'] ?? '') ?>">
                  </div>
                </div>
                <div class="form-group" style="flex-direction:row;align-items:center;gap:.75rem;">
                  <input type="checkbox" name="is_new" id="edit-isnew" value="1"
                    <?= !empty($edit_item['is_new']) ? 'checked' : '' ?>
                    style="width:18px;height:18px;accent-color:var(--color-primary);">
                  <label for="edit-isnew" style="margin:0;font-size:.9rem;text-transform:none;letter-spacing:0;">
                    Marquer comme « Nouveau »
                  </label>
                </div>
                <div style="display:flex;gap:.75rem;">
                  <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                  <a href="admin.php?section=<?= $section ?>" class="btn btn-secondary">Annuler</a>
                </div>
              </form>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <!-- ════ SETTINGS ══════════════════════════════════════════ -->
      <?php if ($section === 'settings'): ?>
        <div class="settings-card">
          <h3 class="settings-card-title">🔑 Mot de passe partenaire</h3>
          <p style="color:var(--color-text-light);font-size:.9rem;margin-bottom:1.5rem;">
            Ce mot de passe est partagé avec toutes les pharmacies partenaires.
            Communiquez le nouveau mot de passe après chaque modification.
          </p>
          <form method="post" action="api/settings.php">
            <input type="hidden" name="field"    value="partner_password">
            <input type="hidden" name="redirect" value="admin.php?section=settings">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
              <div class="form-group">
                <label for="p-pw-new">Nouveau mot de passe</label>
                <div class="password-input-wrap">
                  <input type="password" id="p-pw-new" name="new_password" class="form-control"
                    placeholder="Minimum 6 caractères" autocomplete="new-password" required>
                  <button type="button" class="toggle-password">👁</button>
                </div>
              </div>
              <div class="form-group">
                <label for="p-pw-confirm">Confirmer</label>
                <div class="password-input-wrap">
                  <input type="password" id="p-pw-confirm" name="confirm_password" class="form-control"
                    placeholder="Répétez le mot de passe" autocomplete="new-password" required>
                  <button type="button" class="toggle-password">👁</button>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer le mot de passe partenaire</button>
          </form>
        </div>

        <div class="settings-card">
          <h3 class="settings-card-title">🛡 Mot de passe administrateur</h3>
          <p style="color:var(--color-text-light);font-size:.9rem;margin-bottom:1.5rem;">
            Accès exclusif au panneau d'administration. Gardez-le confidentiel.
          </p>
          <form method="post" action="api/settings.php">
            <input type="hidden" name="field"    value="admin_password">
            <input type="hidden" name="redirect" value="admin.php?section=settings">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
              <div class="form-group">
                <label for="a-pw-new">Nouveau mot de passe admin</label>
                <div class="password-input-wrap">
                  <input type="password" id="a-pw-new" name="new_password" class="form-control"
                    placeholder="Minimum 6 caractères" autocomplete="new-password" required>
                  <button type="button" class="toggle-password">👁</button>
                </div>
              </div>
              <div class="form-group">
                <label for="a-pw-confirm">Confirmer</label>
                <div class="password-input-wrap">
                  <input type="password" id="a-pw-confirm" name="confirm_password" class="form-control"
                    placeholder="Répétez le mot de passe" autocomplete="new-password" required>
                  <button type="button" class="toggle-password">👁</button>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer le mot de passe admin</button>
          </form>
        </div>
      <?php endif; ?>

    </div><!-- /admin-content -->
  </div><!-- /admin-main -->
</div><!-- /admin-layout -->

<div id="toast-container"></div>
<script src="js/app.js"></script>
</body>
</html>
