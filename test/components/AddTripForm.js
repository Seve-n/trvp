export default {
template: `
    <div class="container mt-5" style="max-width: 600px;">
      <h2 class="text-center mb-4">Ajouter un trajet</h2>

      <div v-if="message" class="alert alert-success text-center">{{ message }}</div>
      <div v-if="error" class="alert alert-danger text-center">{{ error }}</div>

      <form @submit.prevent="addTrip" class="bg-white p-4 rounded shadow">
        <div class="mb-3">
          <label>Départ</label>
          <input v-model="leaving" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Destination</label>
          <input v-model="destination" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Date et heure de départ</label>
          <input v-model="leaving_date" type="datetime-local" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Prix (€)</label>
          <input v-model="price" type="number" step="0.01" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Description</label>
          <textarea v-model="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
          <label>Véhicule utilisé</label>
          <select v-model="id_vehicule" class="form-select" required>
            <option value="" disabled selected>Sélectionner votre véhicule</option>
            <option v-for="v in vehicules" :value="v.id">{{ v.brand }} {{ v.model }}</option>
          </select>
        </div>
        <button class="btn btn-primary d-block mx-auto">Ajouter</button>
      </form>
    </div>
  `,
  data() {
    return {
      leaving: '',
      destination: '',
      leaving_date: '',
      price: '',
      description: '',
      id_vehicule: '',
      vehicules: [],
      message: '',
      error: ''
    };
  },
  mounted() {
    fetch('/api/user_vehicules.php')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          this.vehicules = data.vehicules;
        } else {
          this.error = "Impossible de charger vos véhicules.";
        }
      })
      .catch(() => {
        this.error = "Erreur réseau.";
      });
  },
  methods: {
    async addTrip() {
      this.message = '';
      this.error = '';

      const res = await fetch("/api/add.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          leaving: this.leaving,
          destination: this.destination,
          leaving_date: this.leaving_date,
          price: this.price,
          description: this.description,
          id_vehicule: this.id_vehicule
        })
      });

      const data = await res.json();
      if (data.success) {
        this.message = data.message;
        this.leaving = '';
        this.destination = '';
        this.leaving_date = '';
        this.price = '';
        this.description = '';
        this.id_vehicule = '';
      } else {
        this.error = data.message || "Erreur serveur.";
      }
    }
  },
};
