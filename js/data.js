/**
 * Hazelly Hair Love — Media data store
 * data.js
 *
 * All media items are stored in localStorage under "hazelly_media".
 * Each item: { id, type, title, description, url, fileName, fileSize, addedAt, isNew }
 */

const MediaStore = (() => {
  const KEY = 'hazelly_media';

  /* ── Sample demo content ───────────────────────────────── */
  const DEMO_ITEMS = [
    {
      id: 'demo-pdf-1',
      type: 'pdf',
      title: 'Fiche technique — Sérum Réparateur',
      description: 'Composition, bénéfices et protocole d\'application du sérum réparateur Hazelly.',
      url: '#',
      fileName: 'fiche-technique-serum-reparateur.pdf',
      fileSize: '1.2 Mo',
      addedAt: new Date(Date.now() - 86400000 * 2).toISOString(),
      isNew: true,
    },
    {
      id: 'demo-pdf-2',
      type: 'pdf',
      title: 'Protocole de vente — Printemps 2025',
      description: 'Guide complet des arguments de vente et objections courantes en pharmacie.',
      url: '#',
      fileName: 'protocole-vente-printemps-2025.pdf',
      fileSize: '850 Ko',
      addedAt: new Date(Date.now() - 86400000 * 7).toISOString(),
      isNew: false,
    },
    {
      id: 'demo-pdf-3',
      type: 'pdf',
      title: 'Bon de commande — Collection Été 2025',
      description: 'Formulaire de commande pour les produits de la collection été 2025.',
      url: '#',
      fileName: 'bon-commande-ete-2025.pdf',
      fileSize: '320 Ko',
      addedAt: new Date(Date.now() - 86400000 * 14).toISOString(),
      isNew: false,
    },
    {
      id: 'demo-photo-1',
      type: 'photo',
      title: 'Packshot — Gamme Réparation',
      description: 'Visuels produits haute définition sur fond blanc pour communication digitale.',
      url: 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&q=80',
      fileName: 'packshot-gamme-reparation.jpg',
      fileSize: '4.2 Mo',
      addedAt: new Date(Date.now() - 86400000 * 1).toISOString(),
      isNew: true,
    },
    {
      id: 'demo-photo-2',
      type: 'photo',
      title: 'Visuel Réseaux Sociaux — Carré Instagram',
      description: 'Pack de 6 visuels optimisés 1080×1080 pour les publications Instagram.',
      url: 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=800&q=80',
      fileName: 'visuels-instagram-pack.jpg',
      fileSize: '6.8 Mo',
      addedAt: new Date(Date.now() - 86400000 * 5).toISOString(),
      isNew: false,
    },
    {
      id: 'demo-photo-3',
      type: 'photo',
      title: 'Photo Mannequin — Résultats',
      description: 'Série de photos before/after avec mannequin professionnel.',
      url: 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=800&q=80',
      fileName: 'photos-mannequin-resultats.jpg',
      fileSize: '8.1 Mo',
      addedAt: new Date(Date.now() - 86400000 * 10).toISOString(),
      isNew: false,
    },
    {
      id: 'demo-video-1',
      type: 'video',
      title: 'Tutoriel — Application Masque Nutritif',
      description: 'Vidéo de démonstration pour conseiller le masque nutritif en officine.',
      url: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
      fileName: 'tuto-masque-nutritif.mp4',
      fileSize: '124 Mo',
      addedAt: new Date(Date.now() - 86400000 * 3).toISOString(),
      isNew: true,
    },
    {
      id: 'demo-video-2',
      type: 'video',
      title: 'Vidéo Promotionnelle — Écran Vitrine',
      description: 'Spot 30 secondes pour diffusion sur écrans en pharmacie. Format 16:9.',
      url: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
      fileName: 'spot-ecran-vitrine-30s.mp4',
      fileSize: '87 Mo',
      addedAt: new Date(Date.now() - 86400000 * 8).toISOString(),
      isNew: false,
    },
  ];

  /* ── Init ────────────────────────────────────────────────── */
  function init() {
    if (!localStorage.getItem(KEY)) {
      localStorage.setItem(KEY, JSON.stringify(DEMO_ITEMS));
    }
  }

  /* ── CRUD ────────────────────────────────────────────────── */
  function getAll() {
    init();
    try {
      return JSON.parse(localStorage.getItem(KEY)) || [];
    } catch { return []; }
  }

  function getByType(type) {
    if (!type || type === 'all') return getAll();
    return getAll().filter(item => item.type === type);
  }

  function getById(id) {
    return getAll().find(item => item.id === id) || null;
  }

  function add(item) {
    const items = getAll();
    const newItem = {
      id: `item-${Date.now()}`,
      addedAt: new Date().toISOString(),
      isNew: true,
      ...item,
    };
    items.unshift(newItem);
    localStorage.setItem(KEY, JSON.stringify(items));
    return newItem;
  }

  function update(id, updates) {
    const items = getAll();
    const idx = items.findIndex(i => i.id === id);
    if (idx === -1) return null;
    items[idx] = { ...items[idx], ...updates };
    localStorage.setItem(KEY, JSON.stringify(items));
    return items[idx];
  }

  function remove(id) {
    const items = getAll().filter(i => i.id !== id);
    localStorage.setItem(KEY, JSON.stringify(items));
  }

  function search(query, type) {
    const q = query.toLowerCase().trim();
    return getByType(type).filter(item =>
      item.title.toLowerCase().includes(q) ||
      item.description.toLowerCase().includes(q)
    );
  }

  function getCounts() {
    const items = getAll();
    return {
      total: items.length,
      pdf:   items.filter(i => i.type === 'pdf').length,
      photo: items.filter(i => i.type === 'photo').length,
      video: items.filter(i => i.type === 'video').length,
    };
  }

  function reset() {
    localStorage.setItem(KEY, JSON.stringify(DEMO_ITEMS));
  }

  return { init, getAll, getByType, getById, add, update, remove, search, getCounts, reset };
})();
