<template>
  <div class="login-page">
    <div class="login-container">
      <!-- Étape 1 : identifiants -->
      <div v-if="!otpStep" class="login-col">
        <h2 class="login-title">Vous avez déjà un compte ?</h2>
        <form class="login-form" @submit.prevent="onSubmitLogin">
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

      <!-- Étape 2 : code e-mail -->
      <div v-else class="login-col login-col-otp">
        <h2 class="login-title">Vérification e-mail</h2>
        <p class="login-otp-hint">
          Un code à 6 chiffres a été envoyé à <strong>{{ emailMasked }}</strong>
        </p>
        <form class="login-form" @submit.prevent="onSubmitOtp">
          <div class="input-wrap">
            <input
              v-model="otpCode"
              type="text"
              inputmode="numeric"
              maxlength="6"
              class="login-input login-input-otp"
              placeholder="000000"
              autocomplete="one-time-code"
              @input="otpCode = otpCode.replace(/\D/g, '').slice(0, 6)"
            />
          </div>
          <p v-if="error" class="login-error">{{ error }}</p>
          <button type="submit" class="btn btn-submit" :disabled="loading || otpCode.length !== 6">
            {{ loading ? 'Vérification...' : 'Valider le code' }}
          </button>
          <button type="button" class="btn btn-back" :disabled="loading" @click="backToLogin">
            Retour
          </button>
        </form>
      </div>

      <!-- Colonne droite : inscription -->
      <div class="login-col login-col-right">
        <h2 class="login-title">Première visite ?</h2>
        <router-link to="/register" class="btn btn-create">
          Créer mon compte
        </router-link>
      </div>
    </div>

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
import { login, verifyLoginOtp } from '../services/auth.js'
import '../css/login.css'

const router = useRouter()
const email = ref('')
const motDePasse = ref('')
const error = ref('')
const loading = ref(false)
const otpStep = ref(false)
const pendingToken = ref('')
const emailMasked = ref('')
const otpCode = ref('')

async function onSubmitLogin() {
  error.value = ''
  loading.value = true
  const result = await login(email.value, motDePasse.value)
  loading.value = false

  if (result.success) {
    router.push('/home')
    return
  }
  if (result.needsOtp && result.pendingToken) {
    pendingToken.value = result.pendingToken
    emailMasked.value = result.emailMasked || 'votre adresse'
    otpCode.value = ''
    otpStep.value = true
    return
  }
  error.value = result.error || 'Erreur'
}

async function onSubmitOtp() {
  error.value = ''
  if (otpCode.value.length !== 6) {
    error.value = 'Saisissez les 6 chiffres du code.'
    return
  }
  loading.value = true
  const result = await verifyLoginOtp(pendingToken.value, otpCode.value)
  loading.value = false
  if (result.success) {
    router.push('/home')
  } else {
    error.value = result.error || 'Code invalide'
  }
}

function backToLogin() {
  otpStep.value = false
  pendingToken.value = ''
  otpCode.value = ''
  error.value = ''
}
</script>

<style scoped>
.login-col-otp {
  max-width: 100%;
}
.login-otp-hint {
  color: #555;
  font-size: 0.95rem;
  margin-bottom: 1rem;
  line-height: 1.4;
}
.login-input-otp {
  font-size: 1.5rem;
  letter-spacing: 0.4em;
  text-align: center;
  font-variant-numeric: tabular-nums;
}
.btn-back {
  margin-top: 0.75rem;
  width: 100%;
  background: transparent;
  border: 1px solid #ccc;
  color: #333;
  cursor: pointer;
  padding: 0.6rem;
  border-radius: 6px;
}
.btn-back:hover:not(:disabled) {
  background: #f5f5f5;
}
</style>
