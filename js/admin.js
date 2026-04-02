/**
 * Hazelly Hair Love — Admin Panel Logic
 * admin.js
 *
 * initAdminPanel() is called either on DOMContentLoaded (if already
 * authenticated) or explicitly after a successful admin login.
 */

function initAdminPanel() {

  /* ── Sidebar nav ─────────────────────────────────────────── */
  const $navItems   = document.querySelectorAll('.admin-nav-item[data-section]');
  const $adminSections = document.querySelectorAll('.admin-tab-section');

  $navItems.forEach(item => {
    item.addEventListener('click', () => {
      $navItems.forEach(i => i.classList.remove('active'));
      item.classList.add('active');
      const target = item.dataset.section;
      $adminSections.forEach(s => {
        s.classList.toggle('active', s.dataset.section === target);
      });
      document.getElementById('admin-topbar-title').textContent = item.dataset.label || 'Tableau de bord';
      // Close sidebar on mobile
      document.getElementById('admin-sidebar')?.classList.remove('open');
    });
  });

  /* ── Mobile sidebar toggle ───────────────────────────────── */
  document.getElementById('mobile-menu-toggle')?.addEventListener('click', () => {
    document.getElementById('admin-sidebar')?.classList.toggle('open');
  });

  /* ── Stat counts ─────────────────────────────────────────── */
  function refreshStats() {
    const counts = MediaStore.getCounts();
    Object.entries(counts).forEach(([key, val]) => {
      const el = document.getElementById(`stat-${key}`);
      if (el) el.textContent = val;
    });
  }
  refreshStats();

  /* ── Render media table ──────────────────────────────────── */
  function renderMediaTable(type) {
    const tableId = {
      pdf: 'pdf-table-body', photo: 'photo-table-body', video: 'video-table-body',
    }[type];
    const tbody = document.getElementById(tableId);
    if (!tbody) return;

    const items = MediaStore.getByType(type);
    if (items.length === 0) {
      tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:var(--color-text-light);padding:2rem;">
        Aucun contenu. Ajoutez un élément via le bouton ci-dessus.</td></tr>`;
      return;
    }

    tbody.innerHTML = items.map(item => `
      <tr data-id="${escapeHtml(item.id)}">
        <td>
          <span style="font-weight:600;">${escapeHtml(item.title)}</span>
          ${item.isNew ? '<span class="badge badge-new" style="margin-left:.4rem;font-size:.68rem;">Nouveau</span>' : ''}
        </td>
        <td style="color:var(--color-text-light);font-size:.85rem;">${escapeHtml(item.description)}</td>
        <td style="font-size:.82rem;color:var(--color-text-light);">${escapeHtml(item.fileSize || '—')}</td>
        <td style="font-size:.82rem;color:var(--color-text-light);">${formatDate(item.addedAt)}</td>
        <td>
          <div class="admin-table-actions">
            <button class="btn btn-secondary btn-sm" data-action="edit" data-id="${escapeHtml(item.id)}" title="Modifier">✏️</button>
            <button class="btn btn-danger btn-sm" data-action="delete" data-id="${escapeHtml(item.id)}" title="Supprimer">🗑</button>
          </div>
        </td>
      </tr>`).join('');

    // Edit/delete handlers
    tbody.querySelectorAll('[data-action="edit"]').forEach(btn => {
      btn.addEventListener('click', () => openEditModal(btn.dataset.id));
    });
    tbody.querySelectorAll('[data-action="delete"]').forEach(btn => {
      btn.addEventListener('click', () => confirmDelete(btn.dataset.id, type));
    });
  }

  function refreshAllTables() {
    renderMediaTable('pdf');
    renderMediaTable('photo');
    renderMediaTable('video');
    refreshStats();
  }
  refreshAllTables();

  /* ── Add media modal ─────────────────────────────────────── */
  function openAddModal(type) {
    const modal = document.getElementById('media-modal');
    document.getElementById('media-modal-title').textContent = 'Ajouter un contenu';
    document.getElementById('media-form-id').value = '';
    document.getElementById('media-form-type').value = type;
    document.getElementById('media-form-type-display').textContent =
      { pdf: 'Document PDF', photo: 'Photo', video: 'Vidéo' }[type] || type;
    document.getElementById('media-form-title').value = '';
    document.getElementById('media-form-desc').value = '';
    document.getElementById('media-form-url').value = '';
    document.getElementById('media-form-filename').value = '';
    document.getElementById('media-form-size').value = '';
    document.getElementById('media-form-isnew').checked = true;
    modal.classList.remove('hidden');
  }

  function openEditModal(id) {
    const item = MediaStore.getById(id);
    if (!item) return;
    const modal = document.getElementById('media-modal');
    document.getElementById('media-modal-title').textContent = 'Modifier le contenu';
    document.getElementById('media-form-id').value = item.id;
    document.getElementById('media-form-type').value = item.type;
    document.getElementById('media-form-type-display').textContent =
      { pdf: 'Document PDF', photo: 'Photo', video: 'Vidéo' }[item.type] || item.type;
    document.getElementById('media-form-title').value = item.title;
    document.getElementById('media-form-desc').value = item.description;
    document.getElementById('media-form-url').value = item.url;
    document.getElementById('media-form-filename').value = item.fileName || '';
    document.getElementById('media-form-size').value = item.fileSize || '';
    document.getElementById('media-form-isnew').checked = !!item.isNew;
    modal.classList.remove('hidden');
  }

  // Add buttons for each type
  ['pdf', 'photo', 'video'].forEach(type => {
    document.getElementById(`btn-add-${type}`)?.addEventListener('click', () => openAddModal(type));
  });

  // Close modal
  document.getElementById('media-modal-close')?.addEventListener('click', () => {
    document.getElementById('media-modal').classList.add('hidden');
  });
  document.getElementById('media-modal-cancel')?.addEventListener('click', () => {
    document.getElementById('media-modal').classList.add('hidden');
  });
  document.getElementById('media-modal')?.addEventListener('click', (e) => {
    if (e.target.id === 'media-modal') document.getElementById('media-modal').classList.add('hidden');
  });

  // Save
  document.getElementById('media-modal-save')?.addEventListener('click', () => {
    const id       = document.getElementById('media-form-id').value.trim();
    const type     = document.getElementById('media-form-type').value;
    const title    = document.getElementById('media-form-title').value.trim();
    const desc     = document.getElementById('media-form-desc').value.trim();
    const url      = document.getElementById('media-form-url').value.trim();
    const fileName = document.getElementById('media-form-filename').value.trim();
    const fileSize = document.getElementById('media-form-size').value.trim();
    const isNew    = document.getElementById('media-form-isnew').checked;

    if (!title) { showToast('Le titre est obligatoire.', 'error'); return; }

    if (id) {
      MediaStore.update(id, { title, description: desc, url, fileName, fileSize, isNew });
      showToast('Contenu mis à jour.', 'success');
    } else {
      MediaStore.add({ type, title, description: desc, url, fileName, fileSize, isNew });
      showToast('Contenu ajouté avec succès.', 'success');
    }

    document.getElementById('media-modal').classList.add('hidden');
    refreshAllTables();
  });

  /* ── Delete ──────────────────────────────────────────────── */
  let pendingDeleteId = null;
  let pendingDeleteType = null;

  function confirmDelete(id, type) {
    pendingDeleteId   = id;
    pendingDeleteType = type;
    const item = MediaStore.getById(id);
    document.getElementById('delete-item-name').textContent = item ? item.title : id;
    document.getElementById('delete-modal').classList.remove('hidden');
  }

  document.getElementById('delete-modal-cancel')?.addEventListener('click', () => {
    document.getElementById('delete-modal').classList.add('hidden');
    pendingDeleteId = null;
  });
  document.getElementById('delete-modal-close')?.addEventListener('click', () => {
    document.getElementById('delete-modal').classList.add('hidden');
    pendingDeleteId = null;
  });
  document.getElementById('delete-modal-confirm')?.addEventListener('click', () => {
    if (pendingDeleteId) {
      MediaStore.remove(pendingDeleteId);
      showToast('Contenu supprimé.', 'success');
      refreshAllTables();
      pendingDeleteId = null;
    }
    document.getElementById('delete-modal').classList.add('hidden');
  });

  /* ── Password settings ───────────────────────────────────── */
  function initPasswordForm(formId, strengthBarId, updateFn) {
    const form        = document.getElementById(formId);
    const newPwInput  = form?.querySelector('[data-field="new-password"]');
    const confirmInput = form?.querySelector('[data-field="confirm-password"]');
    const strengthBar = document.getElementById(strengthBarId);
    const strengthColors = ['#E53E3E','#ED8936','#ECC94B','#68D391','#48BB78'];

    newPwInput?.addEventListener('input', () => {
      const score = checkPasswordStrength(newPwInput.value);
      if (strengthBar) {
        const fill = strengthBar.querySelector('.password-strength-fill');
        if (fill) {
          fill.style.width = `${(score / 5) * 100}%`;
          fill.style.background = strengthColors[score - 1] || '#E8D5C0';
        }
      }
    });

    form?.addEventListener('submit', (e) => {
      e.preventDefault();
      const newPw = newPwInput?.value.trim();
      const confirm = confirmInput?.value.trim();
      if (!newPw || newPw.length < 6) {
        showToast('Le mot de passe doit contenir au moins 6 caractères.', 'error'); return;
      }
      if (newPw !== confirm) {
        showToast('Les mots de passe ne correspondent pas.', 'error'); return;
      }
      if (updateFn(newPw)) {
        showToast('Mot de passe mis à jour avec succès.', 'success');
        form.reset();
        if (strengthBar) {
          const fill = strengthBar.querySelector('.password-strength-fill');
          if (fill) { fill.style.width = '0%'; }
        }
      }
    });

    initPasswordToggles();
  }

  initPasswordForm('partner-password-form', 'partner-pw-strength', Auth.updatePartnerPassword);
  initPasswordForm('admin-password-form',   'admin-pw-strength',   Auth.updateAdminPassword);

  /* ── Reset demo data ─────────────────────────────────────── */
  document.getElementById('btn-reset-demo')?.addEventListener('click', () => {
    if (confirm('Réinitialiser les données de démonstration ? Tout le contenu actuel sera remplacé.')) {
      MediaStore.reset();
      refreshAllTables();
      showToast('Données de démonstration restaurées.', 'success');
    }
  });

  /* ── Logout ──────────────────────────────────────────────── */
  document.getElementById('btn-logout')?.addEventListener('click', () => {
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
}

/* ── Auto-init if already authenticated ──────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  if (Auth.isAuthenticated('admin')) {
    initAdminPanel();
  }
});
