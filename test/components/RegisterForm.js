export default {
  template: `
    <div class="form-container mt-5 bg-white p-4 shadow rounded">
      <h2 class="text-center mb-4">Inscription</h2>
      <form @submit.prevent="submitForm">
        <div class="mb-3"><input v-model="name" type="text" class="form-control" placeholder="Nom" required></div>
        <div class="mb-3"><input v-model="email" type="email" class="form-control" placeholder="Email" required></div>
        <div class="mb-3"><input v-model="password" type="password" class="form-control" placeholder="Mot de passe" required></div>
        <div class="mb-3"><input v-model="confirm_password" type="password" class="form-control" placeholder="Confirmer mot de passe" required></div>
        <div class="form-check mb-3">
          <input v-model="proposeTrajets" type="checkbox" class="form-check-input" id="hasVehicle">
          <label class="form-check-label" for="hasVehicle">Je vais proposer des trajets</label>
        </div>
        <div v-if="proposeTrajets">
          <div class="mb-3"><input v-model="vehicle_brand" type="text" class="form-control" placeholder="Marque"></div>
          <div class="mb-3"><input v-model="vehicle_model" type="text" class="form-control" placeholder="Modèle"></div>
          <div class="mb-3"><input v-model="license_plate" type="text" class="form-control" placeholder="Plaque d'immatriculation"></div>
        </div>
        <button class="btn btn-primary d-block mx-auto">S'inscrire</button>
      </form>
    </div>
  `,
  data() {
    return {
      name: '', email: '', password: '', confirm_password: '',
      proposeTrajets: false,
      vehicle_brand: '', vehicle_model: '', license_plate: ''
    };
  },
  methods: {
    async submitForm() {
      try {
        const res = await fetch("/api/signup.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: 'include',
          body: JSON.stringify({
            name: this.name,
            email: this.email,
            password: this.password,
            confirm_password: this.confirm_password,
            proposeTrajets: this.proposeTrajets,
            vehicle_brand: this.vehicle_brand,
            vehicle_model: this.vehicle_model,
            license_plate: this.license_plate
          })
        });
        const data = await res.json();
        if (data.success) {
          alert("Inscription réussie !");
          this.$emit('navigate', 'search-trips');
        } else {
          alert(data.message);
        }
      } catch (err) {
        alert("Erreur serveur");
      }
    }
  }
};
