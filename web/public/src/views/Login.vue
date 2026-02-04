<template>
  <div class="login-page">
    <div class="login-container">
      <!-- Colonne gauche : connexion -->
      <div class="login-col">
        <h2 class="login-title">Vous avez déjà un compte ?</h2>
        <form class="login-form" @submit.prevent="onSubmit">
          <div class="input-wrap">
            <input
              v-model="identifiant"
              type="text"
              class="login-input"
              placeholder="Identifiant"
              autocomplete="username"
            />
          </div>
          <div class="input-wrap">
            <input
              v-model="motDePasse"
              type="password"
              class="login-input"
              placeholder="Mot de passe"
              autocomplete="current-password"
            />
          </div>
        </form>
      </div>

      <!-- Colonne droite : inscription -->
      <div class="login-col login-col-right">
        <h2 class="login-title">Première visite ?</h2>
        <router-link to="/home" class="btn btn-create">
          Créer mon compte
        </router-link>
      </div>
    </div>

    <!-- Section réseaux sociaux -->
    <section class="social-section">
      <h2 class="social-title">Autres moyens de connexion</h2>
      <div class="social-buttons">
        <button type="button" class="btn btn-facebook" aria-label="Se connecter avec Facebook">
          <span class="fb-icon">f</span>
        </button>
        <button type="button" class="btn btn-google" aria-label="Se connecter avec Google">
          <span class="google-icon">G</span>
        </button>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import '../css/login.css'

const router = useRouter()
const identifiant = ref('')
const motDePasse = ref('')

function onSubmit() {
  // TODO: appel API auth/login
  router.push('/home')
}
</script>
