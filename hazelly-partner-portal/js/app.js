/**
 * Hazelly Hair Love — Partner Portal
 * js/app.js — Client-side UI utilities
 * Authentication is handled server-side (PHP sessions).
 */

document.addEventListener('DOMContentLoaded', function () {

  /* ── Password visibility toggle ─────────────────────────── */
  document.querySelectorAll('.toggle-password').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var wrap  = btn.closest('.password-input-wrap');
      var input = wrap && wrap.querySelector('input[type="password"], input[type="text"]');
      if (!input) return;
      var hidden = input.type === 'password';
      input.type = hidden ? 'text' : 'password';
      btn.textContent = hidden ? '🙈' : '👁';
    });
  });

  /* ── Mobile sidebar toggle (admin panel) ─────────────────── */
  var mobileToggle = document.getElementById('mobile-menu-toggle');
  var sidebar      = document.getElementById('admin-sidebar');
  if (mobileToggle && sidebar) {
    mobileToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
    // Close on outside click
    document.addEventListener('click', function (e) {
      if (!sidebar.contains(e.target) && e.target !== mobileToggle) {
        sidebar.classList.remove('open');
      }
    });
  }

  /* ── Photo preview modal ─────────────────────────────────── */
  var overlay      = document.getElementById('preview-overlay');
  var previewBody  = document.getElementById('preview-body');
  var previewTitle = document.getElementById('preview-title');
  var closeBtn     = document.getElementById('preview-close');
  var closeBtnAlt  = document.getElementById('preview-close-btn');

  function openPreview(html, title) {
    if (!overlay) return;
    previewBody.innerHTML  = html;
    if (previewTitle) previewTitle.textContent = title || 'Aperçu';
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }

  function closePreview() {
    if (!overlay) return;
    overlay.classList.add('hidden');
    previewBody.innerHTML = '';
    document.body.style.overflow = '';
  }

  if (closeBtn)    closeBtn.addEventListener('click', closePreview);
  if (closeBtnAlt) closeBtnAlt.addEventListener('click', closePreview);
  if (overlay) {
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closePreview();
    });
  }
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closePreview();
  });

  // Photo preview buttons
  document.querySelectorAll('.btn-preview[data-type="photo"]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var src   = btn.dataset.src   || '';
      var alt   = btn.dataset.alt   || 'Aperçu';
      if (!src) return;
      openPreview(
        '<div class="preview-img-wrap"><img src="' + escapeHtml(src) + '" alt="' + escapeHtml(alt) + '"></div>',
        alt
      );
    });
  });

  // Video preview buttons (play overlay on card)
  document.querySelectorAll('.play-overlay[data-video-url]').forEach(function (el) {
    el.addEventListener('click', function () {
      var url   = el.dataset.videoUrl   || '';
      var title = el.dataset.videoTitle || 'Vidéo';
      if (!url) return;
      openPreview(
        '<div class="preview-embed"><iframe src="' + escapeHtml(url) +
        '" allowfullscreen allow="autoplay; encrypted-media" title="' + escapeHtml(title) + '"></iframe></div>',
        title
      );
    });
  });

  // Video preview buttons on cards
  document.querySelectorAll('.btn-preview[data-type="video"]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var url   = btn.dataset.videoUrl   || '';
      var title = btn.dataset.videoTitle || 'Vidéo';
      if (!url) return;
      openPreview(
        '<div class="preview-embed"><iframe src="' + escapeHtml(url) +
        '" allowfullscreen allow="autoplay; encrypted-media" title="' + escapeHtml(title) + '"></iframe></div>',
        title
      );
    });
  });

  /* ── Toast notifications (for flash-message polyfill) ────── */
  // Flash messages in PHP already render inline; this is a bonus UX layer
  // for future fetch-based flows.
  window.showToast = function (message, type) {
    var container = document.getElementById('toast-container');
    if (!container) return;
    var icons = { success: '✓', error: '✕', default: 'ℹ' };
    var toast = document.createElement('div');
    toast.className = 'toast toast-' + (type || 'default');
    toast.innerHTML = '<span>' + (icons[type] || icons.default) + '</span><span>' + escapeHtml(message) + '</span>';
    container.appendChild(toast);
    setTimeout(function () { toast.remove(); }, 3200);
  };

  /* ── Utilities ───────────────────────────────────────────── */
  function escapeHtml(str) {
    if (typeof str !== 'string') return str;
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
});
