export default {
  props: ['isConnected', 'user'],
  emits: ['logout', 'navigate'],
    computed: {
    connected() {
      return this.isConnected;
    },
    currentUser() {
      return this.user;
    }
  },
  template: `
    <nav class="navbar navbar-expand-lg fixed-top bg-dark navbar-dark">
      <div class="container-fluid">
        <a class="navbar-brand me-auto fw-bold text-success" href="#" @click.prevent="$emit('navigate', 'search-trips')">
          CoRide
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasNavbar">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title">CoRide</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
          </div>

          <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
              <li class="nav-item"><a class="nav-link mx-lg-2 active" href="#" @click.prevent="$emit('navigate', 'search-trips')">Home</a></li>
              <li class="nav-item">
                <a class="nav-link mx-lg-2" href="#" @click.prevent="$emit('navigate', isConnected ? 'add-trip-form' : 'login-form')">Trajet</a>
              </li>
              <li class="nav-item"><a class="nav-link mx-lg-2 disabled" href="#">Notre histoire</a></li>
              <li class="nav-item"><a class="nav-link mx-lg-2 disabled" href="#">Contact</a></li>
            </ul>

            <div class="d-flex align-items-center gap-2 mt-4">
              <template v-if="isConnected">
                <span class="text-white">Bonjour {{ user.name }}</span>
                <button @click="$emit('logout')" class="btn btn-outline-success px-3">Déconnexion</button>
                <button @click.prevent="$emit('navigate', 'profile-form')" class="btn btn-success px-3">Modifier mes données</button>
              </template>
              <template v-else>
                <button class="btn btn-outline-success px-3" @click.prevent="$emit('navigate', 'login-form')">Connexion</button>
                <button class="btn btn-success px-3" @click.prevent="$emit('navigate', 'register-form')">Inscription</button>
              </template>
            </div>
          </div>
        </div>
      </div>
    </nav>
  `
};
