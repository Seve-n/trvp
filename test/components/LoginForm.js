export default {
template: `
  <div class="form-container mt-5 bg-white p-4 shadow rounded">
      <h2 class="text-center mb-4">Connexion</h2>
      <form @submit.prevent="login">
        <div class="mb-3"><input v-model="email" type="email" class="form-control" placeholder="Email" required></div>
        <div class="mb-3"><input v-model="password" type="password" class="form-control" placeholder="Mot de passe" required></div>
        <div class="form-check mb-3">
          <input type="checkbox" class="form-check-input" v-model="remember">
          <label class="form-check-label">Rester connecté</label>
        </div>
        <button class="btn btn-primary d-block mx-auto">Connexion</button>
      </form>
  </div>
`,
  data() {
    return {
      email: '', password: '', remember: false
    };
  },
  methods: {
  async login() {
    try {
      const res = await fetch("/api/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          email: this.email,
          password: this.password
        })
      });
      const data = await res.json();
      if (data.success) {
        console.log("Connecté !");
        this.$emit('navigate', 'search-trips');
        this.$emit('refreshSession');
      } else {
        alert(data.message);
      }
    } catch (err) {
      alert("Erreur réseau");
    }
  }
}

};
