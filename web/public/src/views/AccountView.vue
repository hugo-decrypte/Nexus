<template>
  <div class="account-view">
    <section class="account-card">
      <h1 class="account-page-title">Mon compte</h1>

      <p v-if="loadError" class="account-error">{{ loadError }}</p>

      <template v-else>
        <div class="account-field">
          <label class="account-label">Nom</label>
          <input
            type="text"
            :value="form.nom"
            class="account-input account-input-readonly"
            readonly
            tabindex="-1"
            aria-readonly="true"
          />
        </div>
        <hr class="account-separator" />
        <div class="account-field">
          <label class="account-label">Prenom</label>
          <input
            type="text"
            :value="form.prenom"
            class="account-input account-input-readonly"
            readonly
            tabindex="-1"
            aria-readonly="true"
          />
        </div>
        <hr class="account-separator" />
        <div class="account-field">
          <label class="account-label">Email</label>
          <input
            type="email"
            :value="form.email"
            class="account-input account-input-readonly"
            readonly
            tabindex="-1"
            aria-readonly="true"
          />
        </div>
        <hr class="account-separator" />

        <div class="account-password-section">
          <label class="account-label" for="current-password">Mot de passe actuel</label>
          <input
            id="current-password"
            v-model="passwordForm.mot_de_passe_actuel"
            type="password"
            class="account-input"
            placeholder="Mot de passe actuel"
            autocomplete="current-password"
          />
        </div>
        <div class="account-field">
          <label class="account-label" for="new-password">Nouveau mot de passe</label>
          <input
            id="new-password"
            v-model="passwordForm.nouveau_mot_de_passe"
            type="password"
            class="account-input"
            placeholder="Nouveau mot de passe"
            autocomplete="new-password"
          />
        </div>
        <div class="account-field">
          <label class="account-label" for="confirm-password">Confirmer le nouveau mot de passe</label>
          <input
            id="confirm-password"
            v-model="passwordForm.confirm"
            type="password"
            class="account-input"
            placeholder="Confirmer"
            autocomplete="new-password"
          />
        </div>
        <p v-if="passwordMessage" :class="passwordSuccess ? 'account-success' : 'account-error'">
          {{ passwordMessage }}
        </p>
        <div class="account-btn-wrap">
          <button
            type="button"
            class="account-btn-submit"
            :disabled="passwordLoading"
            @click="changePassword"
          >
            {{ passwordLoading ? 'Envoi...' : 'Valider les modifications' }}
          </button>
        </div>
      </template>
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
