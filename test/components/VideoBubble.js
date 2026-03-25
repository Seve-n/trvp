export default {
  name: 'VideoBubble',
  props: {
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      default: ''
    },
    videoUrl: {
      type: String,
      required: true
    },
    thumbnail: {
      type: String,
      default: ''
    },
    step: {
      type: Number,
      default: null
    }
  },
  data() {
    return {
      showModal: false
    };
  },
  computed: {
    embedUrl() {
      return this.videoUrl + (this.videoUrl.includes('?') ? '&' : '?') + 'autoplay=1&rel=0';
    },
    bubbleStyle() {
      if (this.thumbnail) {
        return { backgroundImage: `url(${this.thumbnail})` };
      }
      return {};
    }
  },
  methods: {
    openModal() {
      this.showModal = true;
      document.body.style.overflow = 'hidden';
    },
    closeModal() {
      this.showModal = false;
      document.body.style.overflow = '';
    }
  },
  template: `
    <div class="video-bubble-wrapper text-center">
      <div class="video-bubble" :style="bubbleStyle" @click="openModal" role="button" :aria-label="'Lire la vidéo : ' + title">
        <div class="video-bubble-overlay">
          <i class="bi bi-play-circle-fill"></i>
        </div>
        <span v-if="step !== null" class="video-bubble-step">{{ step }}</span>
      </div>
      <h5 class="mt-3 fw-semibold">{{ title }}</h5>
      <p class="small text-muted">{{ description }}</p>

      <teleport to="body">
        <div v-if="showModal" class="video-modal-backdrop" @click.self="closeModal" role="dialog" aria-modal="true" :aria-label="title">
          <div class="video-modal-content">
            <button class="video-modal-close" @click="closeModal" aria-label="Fermer">
              <i class="bi bi-x-lg"></i>
            </button>
            <div class="ratio ratio-16x9">
              <iframe
                :src="embedUrl"
                allow="autoplay; fullscreen; picture-in-picture"
                allowfullscreen
                :title="title"
              ></iframe>
            </div>
          </div>
        </div>
      </teleport>
    </div>
  `
};
