# Hazelly Hair Love – B2B Partner Portal

A private, password-protected B2B portal for pharmacy partners of **Hazelly Hair Love**.

---

## Architecture

```
hazelly-partner-portal/
├── index.php              ← Partner login page
├── portal.php             ← Partner gallery (Documents / Photos / Videos)
├── logout.php             ← Partner session logout
├── config.php             ← Global configuration & helpers
├── .htaccess              ← Security rules, NoIndex header
│
├── admin/
│   ├── index.php          ← Admin login
│   ├── dashboard.php      ← Admin panel (upload, delete, change passwords)
│   └── logout.php
│
├── api/
│   ├── upload.php         ← Upload media (admin)
│   ├── delete.php         ← Delete media (admin)
│   ├── download.php       ← Download media (partner)
│   └── change_password.php← Change partner/admin password (admin)
│
├── assets/
│   └── css/style.css      ← Shared stylesheet (all pages)
│
├── uploads/               ← Media files (not indexed, no PHP execution)
│   ├── documents/
│   ├── photos/
│   └── videos/
│
└── data/                  ← JSON data files (not web-accessible)
    ├── media.json          ← Media metadata
    └── settings.json       ← Hashed passwords
```

---

## Default credentials

| Role     | Default password     |
|----------|----------------------|
| Partner  | `hazelly2024`        |
| Admin    | `HazellyAdmin2024!`  |

> **Change both passwords immediately** via the admin panel after first deployment.

---

## Setup

1. Upload the `hazelly-partner-portal/` folder to your web server (Apache + PHP 8+).
2. Make sure the `data/` and `uploads/` directories are **writable** by the web server.
3. Navigate to `/hazelly-partner-portal/admin/` and log in with the default admin password.
4. Change both passwords from the **Mots de passe** section.
5. Upload your documents, photos and videos.
6. Share the partner URL and password with your pharmacies.

---

## Security features

- 🔒 Session-based authentication (1-hour timeout, secure session regeneration)
- 🤖 `noindex, nofollow` on all pages + `X-Robots-Tag` HTTP header
- 🛡️ SHA-256 hashed passwords stored in `data/settings.json` (never plaintext)
- 📂 `data/` directory blocked from public access (`.htaccess`)
- 🚫 PHP execution disabled in `uploads/` directory
- 🧩 Brute-force mitigation: 400ms artificial delay on wrong passwords
- 📥 Download API validates session + performs path traversal check
- 🔑 MIME-type validation on uploads (only PDF / images / videos accepted)
