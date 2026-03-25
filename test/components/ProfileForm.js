export default {
  template: `
    <div class="form-container mt-5 bg-white p-4 shadow rounded">
      <h2 class="text-center mb-4">Modifier mon profil</h2>
      <form @submit.prevent="updateProfile">
        <div class="mb-3"><input v-model="name" type="text" class="form-control" placeholder="Nom" required></div>
        <div class="mb-3"><input v-model="email" type="email" class="form-control" placeholder="Email" required></div>
        <div class="mb-3"><input v-model="old_password" type="password" class="form-control" placeholder="Mot de passe actuel"></div>
        <div class="mb-3"><input v-model="new_password" type="password" class="form-control" placeholder="Nouveau mot de passe"></div>
        <div class="mb-3"><input v-model="confirm_new_password" type="password" class="form-control" placeholder="Confirmer nouveau mot de passe"></div>
        <div class="form-check mb-3">
          <input v-model="proposeTrajets" type="checkbox" class="form-check-input" id="hasVehicle">
          <label class="form-check-label" for="hasVehicle">Je vais proposer des trajets</label>
        </div>
        <div v-if="proposeTrajets">
          <div class="mb-3"><input v-model="vehicle_brand" type="text" class="form-control" placeholder="Marque"></div>
          <div class="mb-3"><input v-model="vehicle_model" type="text" class="form-control" placeholder="Modèle"></div>
          <div class="mb-3"><input v-model="license_plate" type="text" class="form-control" placeholder="Plaque d'immatriculation"></div>
        </div>
        <button class="btn btn-primary d-block mx-auto">Mettre à jour</button>
      </form>
    </div>
  `,
  data() {
    return {
      name: '', email: '', old_password: '', new_password: '', confirm_new_password: '',
      proposeTrajets: false,
      vehicle_brand: '', vehicle_model: '', license_plate: ''
    };
  },
  methods: {
    async updateProfile() {
      const res = await fetch("/api/update_profile.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name: this.name,
          email: this.email,
          old_password: this.old_password,
          new_password: this.new_password,
          confirm_new_password: this.confirm_new_password,
          proposeTrajets: this.proposeTrajets,
          vehicle_brand: this.vehicle_brand,
          vehicle_model: this.vehicle_model,
          license_plate: this.license_plate
        })
      });
      const data = await res.json();
      alert(data.message);
    }
  },
  mounted() {
    fetch('/api/user_profile.php')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          this.name = data.user.name;
          this.email = data.user.email;
          if (data.vehicule) {
            this.proposeTrajets = true;
            this.vehicle_brand = data.vehicule.brand;
            this.vehicle_model = data.vehicule.model;
            this.license_plate = data.vehicule.license_number;
          }
        }
      });
  }
};
