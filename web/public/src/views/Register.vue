<template>
  <div class="register-page">
    <div class="register-container">
      <h2 class="register-title">Créer un compte</h2>
      <form class="register-form" @submit.prevent="onSubmit">
        <div class="input-wrap">
          <input
            v-model="email"
            type="email"
            class="register-input"
            placeholder="Email"
            autocomplete="email"
          />
        </div>
        <div class="input-wrap">
          <input
            v-model="motDePasse"
            type="password"
            class="register-input"
            placeholder="Mot de passe"
            autocomplete="new-password"
          />
        </div>
        <div class="input-wrap">
          <input
            v-model="motDePasseConfirm"
            type="password"
            class="register-input"
            placeholder="Confirmer le mot de passe"
            autocomplete="new-password"
          />
        </div>
        <p v-if="error" class="register-error">{{ error }}</p>
        <button type="submit" class="btn btn-submit" :disabled="loading">
          {{ loading ? 'Inscription...' : "S'inscrire" }}
        </button>
      </form>
      <p class="register-link">
        Déjà un compte ?
        <router-link to="/login">Se connecter</router-link>
      </p>
    </div>

    <section class="social-section">
      <h2 class="social-title">Autres moyens de connexion</h2>
      <div class="social-buttons">
        <button type="button" class="btn btn-facebook" aria-label="S'inscrire avec Facebook">
          <span class="fb-icon">f</span>
        </button>
        <button type="button" class="btn btn-google" aria-label="S'inscrire avec Google">
          <span class="google-icon">G</span>
        </button>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { register } from '../services/auth.js'
import '../css/register.css'

const router = useRouter()
const email = ref('')
const motDePasse = ref('')
const motDePasseConfirm = ref('')
const error = ref('')
const loading = ref(false)

async function onSubmit() {
  error.value = ''
  if (motDePasse.value !== motDePasseConfirm.value) {
    error.value = 'Les mots de passe ne correspondent pas.'
    return
  }
  if (motDePasse.value.length < 6) {
    error.value = 'Le mot de passe doit contenir au moins 6 caractères.'
    return
  }
  loading.value = true
  const result = await register(email.value, motDePasse.value)
  loading.value = false
  if (result.success) {
    router.push('/login')
  } else {
    error.value = result.error
  }
}
</script>
