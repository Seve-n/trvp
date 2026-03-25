import VideoBubble from './VideoBubble.js';

export default {
  name: 'SearchTrips',
  components: { 'video-bubble': VideoBubble },
  emits: ['refreshsession', 'navigate'],
  data() {
    return {
      depart: '',
      destination: '',
      date: '',
      results: [],
      error: '',
      message: ''
    };
  },
  methods: {
    async search() {
      const params = new URLSearchParams({
        depart: this.depart,
        destination: this.destination,
        date: this.date
      });

      try {
        const res = await fetch(`/api/book.php?${params.toString()}`);
        const data = await res.json();

        if (Array.isArray(data)) {
          this.results = data;
          this.error = '';
        } else {
          this.results = [];
          this.error = data.message || "Aucun résultat.";
        }
      } catch (e) {
        this.error = "Erreur de recherche.";
        this.results = [];
      }
    },

    async reserve(id_course) {
      try {
        const res = await fetch("/api/book.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          credentials: "include",
          body: new URLSearchParams({ id_course })
        });

        const data = await res.json();
        if (data.success) {
          this.message = data.message;
        } else {
          this.error = data.message || "Réservation impossible.";
        }
      } catch (e) {
        this.error = "Erreur réseau pendant la réservation.";
      }
    }
  },
  template: `
<section class="hero-banner text-white text-center py-5 mb-5" id="hero" style="background:  center/cover no-repeat;">
  <div class="bg-dark bg-opacity-75 p-5 rounded shadow">
    <h1 class="display-5 fw-bold">Voyagez ensemble avec CoRide</h1>
    <p class="lead">Trouvez ou proposez un trajet en toute simplicité.</p>
    <a href="#recherche" class="btn btn-warning btn-lg mt-3">Voir les trajets</a>
  </div>
</section>

<section class="container my-5">
  <div class="row align-items-center">
    <div class="col-md-6 mb-4 mb-md-0">
      <img src="/assets/images/img2.webp" alt="Covoiturage" class="img-fluid rounded shadow">
    </div>
    <div class="col-md-6">
      <h2 class="mb-3">Pourquoi choisir CoRide ?</h2>
      <ul class="list-group list-group-flush fs-5">
        <li class="list-group-item">🚗 Réduis tes frais de transport</li>
        <li class="list-group-item">🌱 Réduis ton empreinte carbone</li>
        <li class="list-group-item">🤝 Fais de nouvelles rencontres</li>
        <li class="list-group-item">📱 Plateforme simple et rapide</li>
      </ul>
    </div>
  </div>
</section>

<section class="container my-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold">Comment ça marche ?</h2>
    <p class="text-muted">Réserve ou propose un trajet en quelques clics</p>
  </div>
  <div class="row text-center">
    <div class="col-md-3">
      <img src="../assets/images/img1.webp" loading="lazy" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
      <h5>1. Inscris-toi</h5>
      <p class="small">Crée ton compte gratuitement</p>
    </div>
    <div class="col-md-3">
      <img src="../assets/images/img2.webp" loading="lazy" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
      <h5>2. Recherche</h5>
      <p class="small">Trouve un trajet ou ajoute le tien</p>
    </div>
    <div class="col-md-3">
      <img src="../assets/images/img3.webp" loading="lazy" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
      <h5>3. Réserve</h5>
      <p class="small">Choisis une offre et confirme</p>
    </div>
    <div class="col-md-3">
      <img src="../assets/images/img3.webp" loading="lazy" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
      <h5>4. Voyage !</h5>
      <p class="small">Rejoins ton conducteur à l'heure convenue</p>
    </div>
  </div>
</section>

<section class="container my-5">
  <div class="video-bubbles-section">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Découvrez CoRide en vidéo</h2>
      <p class="text-muted">Clique sur une bulle pour regarder notre histoire</p>
    </div>
    <div class="row justify-content-center text-center g-4">
      <div class="col-6 col-md-4">
        <video-bubble
          title="CoRide, c'est quoi ?"
          description="Découvrez le concept du covoiturage simplifié."
          video-url="https://www.youtube.com/embed/xNRJwmlRBNU"
          thumbnail="https://img.youtube.com/vi/xNRJwmlRBNU/hqdefault.jpg"
          :step="1"
        />
      </div>
      <div class="col-6 col-md-4">
        <video-bubble
          title="Proposer un trajet"
          description="En quelques clics, partagez votre voiture."
          video-url="https://www.youtube.com/embed/x9MusSp-0Dg"
          thumbnail="https://img.youtube.com/vi/x9MusSp-0Dg/hqdefault.jpg"
          :step="2"
        />
      </div>
      <div class="col-6 col-md-4">
        <video-bubble
          title="Réserver une place"
          description="Trouvez et réservez votre siège en temps réel."
          video-url="https://www.youtube.com/embed/EngW7tLk6R8"
          thumbnail="https://img.youtube.com/vi/EngW7tLk6R8/hqdefault.jpg"
          :step="3"
        />
      </div>
    </div>
  </div>
</section>


    <div class="container py-5">
      <h2 class="text-center mb-4">Rechercher un trajet</h2>

      <div class="filter-box bg-white p-4 rounded shadow mb-4" id="recherche">
        <form class="row g-3" @submit.prevent="search">
          <div class="col-md-4">
            <label class="form-label">Ville de départ</label>
            <input v-model="depart" type="text" class="form-control" placeholder="Ex: Liège">
          </div>
          <div class="col-md-4">
            <label class="form-label">Ville d'arrivée</label>
            <input v-model="destination" type="text" class="form-control" placeholder="Ex: Namur">
          </div>
          <div class="col-md-4">
            <label class="form-label">Date</label>
            <input v-model="date" type="date" class="form-control">
          </div>
          <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary mt-2">Rechercher</button>
          </div>
        </form>
      </div>

      <div v-if="error" class="alert alert-warning text-center">{{ error }}</div>
      <div v-if="message" class="alert alert-success text-center">{{ message }}</div>
      <div v-if="results.length === 0 && !error" class="text-muted text-center">Aucun trajet trouvé.</div>

      <div v-for="trajet in results" :key="trajet.id" class="trajet-card bg-white p-4 rounded shadow mb-3">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h5>{{ trajet.leaving }} → {{ trajet.destination }}</h5>
            <p class="mb-1 text-muted">Départ : {{ new Date(trajet.leaving_date).toLocaleString() }}</p>
            <p class="mb-1">Conducteur : <strong>{{ trajet.name }}</strong></p>
            <p class="mb-1">Véhicule : {{ trajet.brand }} {{ trajet.model }} — {{ trajet.license_number }}</p>
            <p class="mb-1">Description : {{ trajet.description }}</p>
          </div>
          <div class="col-md-4 text-end">
            <p class="fs-4 fw-bold text-primary">{{ parseFloat(trajet.price).toFixed(2) }} €</p>
            <button class="btn btn-outline-primary" @click="reserve(trajet.id)">Réserver</button>
          </div>
        </div>
      </div>
    </div>
  `
};
