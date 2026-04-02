/**
 * Hazelly Hair Love — Authentication & Shared utilities
 * auth.js
 *
 * Credentials are stored in localStorage under the key "hazelly_config".
 * Default partner password : HazellyPartner2024
 * Default admin   password : HazellyAdmin2024
 */

const Auth = (() => {
  const CONFIG_KEY   = 'hazelly_config';
  const SESSION_KEY  = 'hazelly_session';

  /* ── Default config (first run) ────────────────────────── */
  const DEFAULT_CONFIG = {
    partnerPassword : 'HazellyPartner2024',
    adminPassword   : 'HazellyAdmin2024',
    brandName       : 'Hazelly Hair Love',
    tagline         : 'Espace Partenaire Pharmacies',
  };

  /* ── Helpers ────────────────────────────────────────────── */
  function getConfig() {
    try {
      const raw = localStorage.getItem(CONFIG_KEY);
      return raw ? { ...DEFAULT_CONFIG, ...JSON.parse(raw) } : { ...DEFAULT_CONFIG };
    } catch {
      return { ...DEFAULT_CONFIG };
    }
  }

  function saveConfig(updates) {
    const cfg = getConfig();
    localStorage.setItem(CONFIG_KEY, JSON.stringify({ ...cfg, ...updates }));
  }

  /* ── Session ─────────────────────────────────────────────── */
  function getSession() {
    try {
      const raw = sessionStorage.getItem(SESSION_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch {
      return null;
    }
  }

  function setSession(role) {
    sessionStorage.setItem(SESSION_KEY, JSON.stringify({ role, ts: Date.now() }));
  }

  function clearSession() {
    sessionStorage.removeItem(SESSION_KEY);
  }

  /* ── Public API ─────────────────────────────────────────── */
  function loginPartner(password) {
    const cfg = getConfig();
    if (password === cfg.partnerPassword) {
      setSession('partner');
      return true;
    }
    return false;
  }

  function loginAdmin(password) {
    const cfg = getConfig();
    if (password === cfg.adminPassword) {
      setSession('admin');
      return true;
    }
    return false;
  }

  function logout() {
    clearSession();
  }

  function isAuthenticated(requiredRole) {
    const session = getSession();
    if (!session) return false;
    if (requiredRole === 'admin') return session.role === 'admin';
    return session.role === 'partner' || session.role === 'admin';
  }

  function requireAuth(requiredRole, redirectTo) {
    if (!isAuthenticated(requiredRole)) {
      window.location.href = redirectTo || 'index.html';
      return false;
    }
    return true;
  }

  function updatePartnerPassword(newPassword) {
    if (!newPassword || newPassword.length < 6) return false;
    saveConfig({ partnerPassword: newPassword });
    return true;
  }

  function updateAdminPassword(newPassword) {
    if (!newPassword || newPassword.length < 6) return false;
    saveConfig({ adminPassword: newPassword });
    return true;
  }

  function getPublicConfig() {
    const cfg = getConfig();
    return { brandName: cfg.brandName, tagline: cfg.tagline };
  }

  return {
    loginPartner, loginAdmin, logout,
    isAuthenticated, requireAuth,
    updatePartnerPassword, updateAdminPassword,
    getPublicConfig, getConfig, saveConfig,
  };
})();

/* ── Toast helper (global) ───────────────────────────────── */
function showToast(message, type = 'default', duration = 3200) {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }
  const icons = { success: '✓', error: '✕', default: 'ℹ' };
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `<span>${icons[type] || icons.default}</span><span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => toast.remove(), duration);
}

/* ── Password visibility toggle ─────────────────────────── */
function initPasswordToggles() {
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.closest('.password-input-wrap')?.querySelector('input');
      if (!input) return;
      const isVisible = input.type === 'text';
      input.type = isVisible ? 'password' : 'text';
      btn.textContent = isVisible ? '👁' : '🙈';
    });
  });
}

/* ── Password strength indicator ────────────────────────── */
function checkPasswordStrength(password) {
  let score = 0;
  if (password.length >= 8)   score++;
  if (password.length >= 12)  score++;
  if (/[A-Z]/.test(password)) score++;
  if (/[0-9]/.test(password)) score++;
  if (/[^A-Za-z0-9]/.test(password)) score++;
  return score; // 0-5
}
