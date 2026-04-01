# Hazelly Hair Love — Espace Partenaire B2B

Portail privé destiné aux pharmacies partenaires de la marque **Hazelly Hair Love**.

## Pages

| Fichier | Rôle |
|---|---|
| `index.html` | Page de connexion partenaire |
| `portal.html` | Médiathèque partenaire (PDFs, Photos, Vidéos) |
| `admin.html` | Panneau d'administration |

## Accès par défaut

| Rôle | Mot de passe |
|---|---|
| Partenaire (pharmacies) | `HazellyPartner2024` |
| Administrateur | `HazellyAdmin2024` |

> **Important :** Changez ces mots de passe dès le déploiement via le panneau d'administration.

## Fonctionnalités

- 🔒 Accès verrouillé par mot de passe (partenaire & admin)
- 🚫 Balise `noindex, nofollow` sur toutes les pages
- 📄 Médiathèque organisée : Documents PDF, Photos HD, Vidéos
- ⬇ Bouton Télécharger sur chaque contenu
- 👁 Aperçu en ligne (photos & vidéos YouTube/embed)
- 🔍 Recherche et filtres par type de contenu
- ⚙ Panneau admin : ajouter / modifier / supprimer du contenu
- 🔐 Changement de mots de passe depuis l'admin
- 📱 Design responsive (mobile, tablette, desktop)

## Lancement local

```bash
# Python 3
python3 -m http.server 8080

# Node.js (npx)
npx serve .
```

Puis ouvrir [http://localhost:8080](http://localhost:8080)

## Architecture technique

- Technologie : HTML5 + CSS3 + JavaScript vanilla (aucune dépendance)
- Stockage : `localStorage` / `sessionStorage` du navigateur
- Pas de serveur backend requis (déployable sur n'importe quel hébergement statique)

## Déploiement recommandé

Le site étant 100 % statique, il peut être hébergé sur :
- **Netlify** (glisser-déposer le dossier)
- **Vercel**
- **GitHub Pages**
- **OVH / o2switch** (FTP upload)

> Pour une solution avec stockage fichiers réel, envisager l'intégration d'un service comme
> **Cloudinary** (images/vidéos) ou **Google Drive API** (PDFs).
