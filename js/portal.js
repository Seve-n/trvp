/**
 * Hazelly Hair Love — Partner Portal Logic
 * portal.js
 */

document.addEventListener('DOMContentLoaded', () => {
  /* ── Auth guard ─────────────────────────────────────────── */
  if (!Auth.requireAuth('partner', 'index.html')) return;

  /* ── DOM refs ────────────────────────────────────────────── */
  const $grid        = document.getElementById('media-grid');
  const $filterTabs  = document.querySelectorAll('.filter-tab');
  const $searchInput = document.getElementById('search-input');
  const $navBtns     = document.querySelectorAll('.portal-nav-btn[data-section]');
  const $sections    = document.querySelectorAll('.tab-section');
  const $logoutBtn   = document.getElementById('btn-logout');
  const $heroStats   = document.querySelectorAll('[data-stat]');
  const $previewOverlay = document.getElementById('preview-overlay');
  const $previewClose   = document.getElementById('preview-close');
  const $previewContent = document.getElementById('preview-content');

  /* ── State ───────────────────────────────────────────────── */
  let currentType = 'all';
  let searchQuery = '';

  /* ── Init stats ─────────────────────────────────────────── */
  (function updateStats() {
    const counts = MediaStore.getCounts();
    $heroStats.forEach(el => {
      const key = el.dataset.stat;
      if (counts[key] !== undefined) el.textContent = counts[key];
    });
  })();

  /* ── Render grid ─────────────────────────────────────────── */
  function renderGrid() {
    const items = searchQuery
      ? MediaStore.search(searchQuery, currentType)
      : MediaStore.getByType(currentType);

    $grid.innerHTML = '';

    if (items.length === 0) {
      $grid.innerHTML = `
        <div class="empty-state">
          <div class="empty-state-icon">🔍</div>
          <h3>Aucun contenu trouvé</h3>
          <p>Essayez un autre mot-clé ou une autre catégorie.</p>
        </div>`;
      return;
    }

    items.forEach(item => {
      $grid.insertAdjacentHTML('beforeend', buildCard(item));
    });

    // Attach card events
    $grid.querySelectorAll('[data-action="download"]').forEach(btn => {
      btn.addEventListener('click', () => handleDownload(btn.dataset.id));
    });
    $grid.querySelectorAll('[data-action="preview"]').forEach(btn => {
      btn.addEventListener('click', () => openPreview(btn.dataset.id));
    });
  }

  /* ── Build card HTML ─────────────────────────────────────── */
  function buildCard(item) {
    const thumbClass = `media-card-thumb-${item.type}`;
    const badge      = buildBadge(item.type);
    const isNew      = item.isNew ? '<span class="badge badge-new">Nouveau</span>' : '';
    const date       = formatDate(item.addedAt);

    let thumbContent = '';
    if (item.type === 'photo' && item.url && item.url !== '#') {
      thumbContent = `<img src="${escapeHtml(item.url)}" alt="${escapeHtml(item.title)}" loading="lazy">`;
    } else if (item.type === 'video') {
      thumbContent = `
        <div class="media-card-thumb-icon">${typeIcon(item.type)}</div>
        <div class="play-overlay"><div class="play-btn">▶</div></div>`;
    } else {
      thumbContent = `<div class="media-card-thumb-icon">${typeIcon(item.type)}</div>`;
    }

    return `
      <div class="media-card card" data-id="${escapeHtml(item.id)}">
        <div class="media-card-thumb ${thumbClass}">${thumbContent}</div>
        <div class="media-card-body">
          <div class="media-card-meta">
            ${badge}${isNew}
          </div>
          <div class="media-card-title">${escapeHtml(item.title)}</div>
          <div class="media-card-desc">${escapeHtml(item.description)}</div>
          <div class="media-card-date">${date}${item.fileSize ? ' · ' + escapeHtml(item.fileSize) : ''}</div>
        </div>
        <div class="media-card-actions">
          <button class="btn-download" data-action="download" data-id="${escapeHtml(item.id)}">
            ⬇ Télécharger
          </button>
          <button class="btn-preview" data-action="preview" data-id="${escapeHtml(item.id)}" title="Aperçu">
            👁
          </button>
        </div>
      </div>`;
  }

  /* ── Badges / icons ──────────────────────────────────────── */
  function buildBadge(type) {
    const map = {
      pdf:   '<span class="badge badge-pdf">📄 PDF</span>',
      photo: '<span class="badge badge-photo">🖼 Photo</span>',
      video: '<span class="badge badge-video">🎬 Vidéo</span>',
    };
    return map[type] || '';
  }
  function typeIcon(type) {
    return { pdf: '📄', photo: '🖼', video: '🎬' }[type] || '📁';
  }

  /* ── Filter tabs ─────────────────────────────────────────── */
  $filterTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      $filterTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      currentType = tab.dataset.type || 'all';
      renderGrid();
    });
  });

  /* ── Search ──────────────────────────────────────────────── */
  $searchInput?.addEventListener('input', (e) => {
    searchQuery = e.target.value.trim();
    renderGrid();
  });

  /* ── Navigation sections ─────────────────────────────────── */
  $navBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.dataset.section;
      $navBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      $sections.forEach(s => {
        s.classList.toggle('active', s.dataset.section === target);
      });
    });
  });

  /* ── Download ────────────────────────────────────────────── */
  function handleDownload(id) {
    const item = MediaStore.getById(id);
    if (!item) return;

    if (!item.url || item.url === '#') {
      showToast('Ce fichier de démonstration n\'est pas téléchargeable.', 'default');
      return;
    }

    const link = document.createElement('a');
    link.href     = item.url;
    link.download = item.fileName || item.title;
    link.target   = '_blank';
    link.rel      = 'noopener noreferrer';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast(`Téléchargement de « ${item.title} » lancé.`, 'success');
  }

  /* ── Preview modal ───────────────────────────────────────── */
  function openPreview(id) {
    const item = MediaStore.getById(id);
    if (!item || !$previewOverlay) return;

    let contentHtml = '';

    if (item.type === 'video' && item.url && item.url !== '#') {
      contentHtml = `
        <div class="preview-embed">
          <iframe src="${escapeHtml(item.url)}" allowfullscreen
            allow="autoplay; encrypted-media" title="${escapeHtml(item.title)}"></iframe>
        </div>`;
    } else if (item.type === 'photo' && item.url && item.url !== '#') {
      contentHtml = `
        <div class="preview-img-wrap">
          <img src="${escapeHtml(item.url)}" alt="${escapeHtml(item.title)}">
        </div>`;
    } else {
      contentHtml = `
        <div class="empty-state" style="padding:3rem 1rem;">
          <div class="empty-state-icon">${typeIcon(item.type)}</div>
          <h3>${escapeHtml(item.title)}</h3>
          <p>${escapeHtml(item.description)}</p>
        </div>`;
    }

    contentHtml += `
      <div class="preview-meta">
        <div class="preview-meta-item"><span class="key">Fichier</span><span class="value">${escapeHtml(item.fileName || '—')}</span></div>
        <div class="preview-meta-item"><span class="key">Taille</span><span class="value">${escapeHtml(item.fileSize || '—')}</span></div>
        <div class="preview-meta-item"><span class="key">Ajouté le</span><span class="value">${formatDate(item.addedAt)}</span></div>
      </div>`;

    document.getElementById('preview-title').textContent = item.title;
    $previewContent.innerHTML = contentHtml;
    $previewOverlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    const downloadBtn = document.getElementById('preview-download');
    if (downloadBtn) {
      downloadBtn.onclick = () => handleDownload(id);
    }
  }

  function closePreview() {
    if (!$previewOverlay) return;
    $previewOverlay.classList.add('hidden');
    $previewContent.innerHTML = '';
    document.body.style.overflow = '';
  }

  $previewClose?.addEventListener('click', closePreview);
  $previewOverlay?.addEventListener('click', (e) => {
    if (e.target === $previewOverlay) closePreview();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closePreview();
  });

  /* ── Logout ──────────────────────────────────────────────── */
  $logoutBtn?.addEventListener('click', () => {
    Auth.logout();
    window.location.href = 'index.html';
  });

  /* ── Utils ───────────────────────────────────────────────── */
  function formatDate(iso) {
    try {
      return new Date(iso).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
    } catch { return ''; }
  }

  function escapeHtml(str) {
    if (typeof str !== 'string') return str;
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  /* ── Initial render ──────────────────────────────────────── */
  renderGrid();
});
