const { createApp } = Vue;

// Composants
import NavBar from '../components/NavBar.js';
import RegisterForm from '../components/RegisterForm.js';
import LoginForm from '../components/LoginForm.js';
import ProfileForm from '../components/ProfileForm.js';
import AddTripForm from '../components/AddTripForm.js';
import SearchTrips from '../components/SearchTrips.js';

const app = createApp({
  data() {
    return {
      currentView: 'search-trips',
      state: {
        isConnected: false,
        user: {}
      }
    };
  },

  methods: {
    async checkSession() {
      try {
        const res = await fetch('/api/session.php', {
          method: 'GET',
          credentials: 'include'
        });
        const data = await res.json();
        if (data.connected && data.user) {
          this.state.isConnected = true;
          this.state.user = data.user;
        } else {
          this.state.isConnected = false;
          this.state.user = {};
        }
      } catch (e) {
        console.error('Erreur de session :', e);
        this.state.isConnected = false;
        this.state.user = {};
      }
    },

    async logout() {
      try {
        await fetch('/api/session.php', {
          method: 'DELETE',
          credentials: 'include'
        });
      } catch (e) {
        console.warn('Erreur pendant la déconnexion :', e);
      }

      // Vide l'état côté client
      this.state = { isConnected: false, user: {} };
      this.currentView = 'search-trips';

      // Supprime aussi manuellement le cookie PHP (double sécurité)
      document.cookie = "PHPSESSID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    },

    navigate(view) {
      this.currentView = view;
    },

    refreshSession() {
      this.checkSession(); // appel direct
    }
  },

  async mounted() {
    await this.checkSession();
  },

  components: {
    'nav-bar': NavBar,
    'register-form': RegisterForm,
    'login-form': LoginForm,
    'profile-form': ProfileForm,
    'add-trip-form': AddTripForm,
    'search-trips': SearchTrips
  }
});

app.mount('#app');
