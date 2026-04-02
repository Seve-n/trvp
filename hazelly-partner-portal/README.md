# Hazelly Hair Love — Espace Partenaire B2B

Portail privé destiné aux pharmacies partenaires de la marque **Hazelly Hair Love**.
Application PHP 8+ autonome avec authentification par session, stockage JSON et upload de fichiers.

## Structure du projet

```
hazelly-partner-portal/
├── config.php          # Configuration centrale (chemins, MIME types, taille max)
├── auth.php            # Helpers d'authentification (sessions PHP)
├── media.php           # Couche données CRUD (lecture/écriture de data/media.json)
├── index.php           # Page de connexion (partenaire & admin)
├── portal.php          # Médiathèque partenaire
├── admin.php           # Panneau d'administration
├── logout.php          # Déconnexion
├── api/
│   ├── media.php       # API CRUD médias (POST uniquement, admin requis)
│   └── settings.php    # API changement de mots de passe (admin requis)
├── data/
│   ├── media.json      # Métadonnées des médias
│   └── settings.json   # Mots de passe (créé à la première modification)
├── uploads/
│   ├── documents/      # PDFs uploadés
│   ├── photos/         # Images uploadées
│   └── videos/         # Vidéos uploadées
├── css/                # Feuilles de style (Hazelly brand design)
├── js/
│   └── app.js          # JS minimal (UI only — auth côté serveur)
├── .htaccess           # Protection des répertoires sensibles
└── README.md
```

## Accès par défaut

| Rôle | Mot de passe |
|---|---|
| Partenaire (pharmacies) | `HazellyPartner2024` |
| Administrateur | `HazellyAdmin2024` |

> **Important :** Changez ces mots de passe dès le déploiement via Admin → Mots de passe.

## Fonctionnalités

- 🔒 Authentification par session PHP (côté serveur — aucun secret côté client)
- 🚫 Balise `noindex, nofollow` sur toutes les pages
- 📄 Upload de PDF, photos, vidéos directement sur le serveur
- 🔗 Support des URL externes (Google Drive, YouTube embed, Dropbox…)
- ⬇ Bouton Télécharger sur chaque contenu
- 👁 Aperçu en ligne (photos, vidéos YouTube)
- 🔍 Recherche et filtres par type (Tout / PDF / Photo / Vidéo)
- ⚙ CMS admin : ajouter / modifier / supprimer des contenus sans coder
- 🔐 Changement des mots de passe depuis le panneau admin
- 📱 Design responsive — mobile, tablette, desktop

## Installation

### Prérequis

- PHP 8.0+
- Serveur web Apache (avec `mod_rewrite`) ou Nginx
- Droits d'écriture sur `data/` et `uploads/`

### Déploiement rapide (Apache / o2switch / OVH)

1. Uploader le dossier `hazelly-partner-portal/` sur votre hébergement via FTP
2. S'assurer que `data/` et `uploads/` sont accessibles en écriture :
   ```bash
   chmod 755 data/ uploads/ uploads/documents/ uploads/photos/ uploads/videos/
   ```
3. Accéder à `https://votredomaine.fr/hazelly-partner-portal/`
4. Se connecter avec les identifiants par défaut
5. **Changer immédiatement les mots de passe** via Admin → Mots de passe

### Développement local (PHP built-in server)

```bash
cd hazelly-partner-portal
php -S localhost:8080
```

Puis ouvrir [http://localhost:8080](http://localhost:8080)

## Sécurité

- Les sessions PHP ont un nom personnalisé (`hazelly_session`)
- `session_regenerate_id(true)` est appelé à chaque connexion
- Les mots de passe sont comparés avec `hash_equals()` (protection contre les timing attacks)
- Le répertoire `data/` est protégé par `.htaccess` (accès direct refusé)
- Les fichiers uploadés sont renommés avec un identifiant unique
- Les types MIME sont vérifiés côté serveur avant tout upload
- Les redirects sont validés par regex avant utilisation
- Toutes les sorties HTML utilisent `htmlspecialchars()`
