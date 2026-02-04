<template>
  <div class="login-page">
    <div class="login-container">
      <!-- Colonne gauche : connexion -->
      <div class="login-col">
        <h2 class="login-title">Vous avez déjà un compte ?</h2>
        <form class="login-form" @submit.prevent="onSubmit">
          <div class="input-wrap">
            <input
              v-model="email"
              type="email"
              class="login-input"
              placeholder="Email"
              autocomplete="email"
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
          <p v-if="error" class="login-error">{{ error }}</p>
          <button type="submit" class="btn btn-submit" :disabled="loading">
            {{ loading ? 'Connexion...' : 'Se connecter' }}
          </button>
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
import { login } from '../services/auth.js'
import '../css/login.css'

const router = useRouter()
const email = ref('')
const motDePasse = ref('')
const error = ref('')
const loading = ref(false)

async function onSubmit() {
  error.value = ''
  loading.value = true

  const result = await login(email.value, motDePasse.value)

  loading.value = false

  if (result.success) {
    router.push('/home')
  } else {
    error.value = result.error
  }
}
</script>
