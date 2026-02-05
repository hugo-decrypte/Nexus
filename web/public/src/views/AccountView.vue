<template>
  <div class="account-view">
    <h1 class="page-title">Mon compte</h1>

    <section class="account-card">
      <h2 class="section-title">Informations personnelles</h2>
      <p v-if="loadError" class="account-error">{{ loadError }}</p>
      <div v-else class="account-info-list">
        <div class="form-row">
          <span class="form-label">Prénom</span>
          <span class="form-value">{{ form.prenom || '—' }}</span>
        </div>
        <div class="form-row">
          <span class="form-label">Nom</span>
          <span class="form-value">{{ form.nom || '—' }}</span>
        </div>
        <div class="form-row">
          <span class="form-label">Email</span>
          <span class="form-value">{{ form.email || '—' }}</span>
        </div>
      </div>
    </section>

    <section class="account-card account-card-password">
      <h2 class="section-title">Modifier le mot de passe</h2>
      <p class="section-desc">Entrez votre mot de passe actuel puis le nouveau.</p>
      <form class="account-form" @submit.prevent="changePassword">
        <div class="form-row">
          <label class="form-label" for="current-password">Mot de passe actuel</label>
          <input
            id="current-password"
            v-model="passwordForm.mot_de_passe_actuel"
            type="password"
            class="form-input"
            placeholder="Mot de passe actuel"
            autocomplete="current-password"
          />
        </div>
        <div class="form-row">
          <label class="form-label" for="new-password">Nouveau mot de passe</label>
          <input
            id="new-password"
            v-model="passwordForm.nouveau_mot_de_passe"
            type="password"
            class="form-input"
            placeholder="Nouveau mot de passe"
            autocomplete="new-password"
          />
        </div>
        <div class="form-row">
          <label class="form-label" for="confirm-password">Confirmer le nouveau mot de passe</label>
          <input
            id="confirm-password"
            v-model="passwordForm.confirm"
            type="password"
            class="form-input"
            placeholder="Confirmer"
            autocomplete="new-password"
          />
        </div>
        <p v-if="passwordMessage" :class="passwordSuccess ? 'account-success' : 'account-error'">
          {{ passwordMessage }}
        </p>
        <button type="submit" class="btn btn-primary" :disabled="passwordLoading">
          {{ passwordLoading ? 'Envoi...' : 'Changer le mot de passe' }}
        </button>
      </form>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { getUser } from '../services/auth.js'
import { getProfile, updatePassword } from '../services/account.js'

const loadError = ref('')
const form = reactive({ prenom: '', nom: '', email: '' })

const passwordForm = reactive({
  mot_de_passe_actuel: '',
  nouveau_mot_de_passe: '',
  confirm: '',
})
const passwordMessage = ref('')
const passwordSuccess = ref(false)
const passwordLoading = ref(false)

onMounted(async () => {
  const user = getUser()
  if (!user?.id) {
    loadError.value = 'Vous devez être connecté.'
    return
  }
  const profile = await getProfile(user.id)
  if (!profile) {
    loadError.value = 'Impossible de charger le profil.'
    return
  }
  form.prenom = profile.prenom ?? ''
  form.nom = profile.nom ?? ''
  form.email = profile.email ?? ''
})

async function changePassword() {
  passwordMessage.value = ''
  if (passwordForm.nouveau_mot_de_passe !== passwordForm.confirm) {
    passwordSuccess.value = false
    passwordMessage.value = 'Les deux mots de passe ne correspondent pas.'
    return
  }
  if (passwordForm.nouveau_mot_de_passe.length < 6) {
    passwordSuccess.value = false
    passwordMessage.value = 'Le nouveau mot de passe doit faire au moins 6 caractères.'
    return
  }
  const user = getUser()
  if (!user?.id) return
  passwordLoading.value = true
  const result = await updatePassword(user.id, {
    mot_de_passe_actuel: passwordForm.mot_de_passe_actuel,
    nouveau_mot_de_passe: passwordForm.nouveau_mot_de_passe,
  })
  passwordLoading.value = false
  passwordSuccess.value = result.success
  passwordMessage.value = result.success ? 'Mot de passe modifié.' : result.error
  if (result.success) {
    passwordForm.mot_de_passe_actuel = ''
    passwordForm.nouveau_mot_de_passe = ''
    passwordForm.confirm = ''
  }
}
</script>

<style src="../css/AccountView.css" scoped></style>
