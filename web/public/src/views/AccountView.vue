<template>
  <div class="account-view">
    <!-- Mode Visualisation -->
    <div v-if="!editMode" class="profile-view">
      <!-- En-tête -->
      <div class="profile-header">
        <h1 class="profile-title">Mon profil</h1>
        <button class="btn-edit-outline" @click="enterEditMode">
          Modifier les données
        </button>
      </div>

      <section class="profile-card-pink">
        <div class="profile-card-left">
          <p class="profile-name">{{ form.prenom }} {{ form.nom }}</p>
          <p class="profile-card-number">N° **** {{ maskedCardNumber }}</p>
        </div>
        <div class="profile-card-right">
          <p class="profile-balance-label">Solde actuel</p>
          <div class="profile-balance">
            <span>{{ formatBalance(solde) }}</span>
            <span class="balance-icon">🪙</span>
          </div>
        </div>
      </section>

      <section class="profile-cards-section">
        <h2 class="cards-section-title">Mes cartes</h2>
        <div class="cards-placeholder">
          <p class="cards-placeholder-text">
            NE PAS FAIRE POUR L'INSTANT LA PARTIE CARTES
          </p>
        </div>
      </section>
    </div>

    <div v-else class="profile-edit">
      <p v-if="loadError" class="edit-error">{{ loadError }}</p>

      <template v-else>
        <div class="edit-field">
          <label class="edit-label">Nom</label>
          <input
            v-model="form.nom"
            type="text"
            class="edit-input"
            placeholder="Dupont"
          />
        </div>

        <div class="edit-field">
          <label class="edit-label">Prenom</label>
          <input
            v-model="form.prenom"
            type="text"
            class="edit-input"
            placeholder="Jean"
          />
        </div>

        <div class="edit-password-section">
          <h3 class="password-section-title">Modifier le mot de passe</h3>

          <div class="edit-field">
            <label class="edit-label" for="current-password">Mot de passe actuel</label>
            <input
              id="current-password"
              v-model="passwordForm.mot_de_passe_actuel"
              type="password"
              class="edit-input"
              placeholder="Mot de passe actuel"
              autocomplete="current-password"
            />
          </div>

          <div class="edit-field">
            <label class="edit-label" for="new-password">Nouveau mot de passe</label>
            <input
              id="new-password"
              v-model="passwordForm.nouveau_mot_de_passe"
              type="password"
              class="edit-input"
              placeholder="Nouveau mot de passe (min 6 caractères)"
              autocomplete="new-password"
            />
          </div>

          <div class="edit-field">
            <label class="edit-label" for="confirm-password">Confirmer le nouveau mot de passe</label>
            <input
              id="confirm-password"
              v-model="passwordForm.confirm"
              type="password"
              class="edit-input"
              placeholder="Confirmer"
              autocomplete="new-password"
            />
          </div>
        </div>

        <p v-if="updateMessage" :class="updateSuccess ? 'edit-success' : 'edit-error'">
          {{ updateMessage }}
        </p>

        <div class="edit-buttons">
          <button
            type="button"
            class="btn-cancel"
            @click="cancelEdit"
            :disabled="updateLoading"
          >
            Annuler
          </button>
          <button
            type="button"
            class="btn-validate-outline"
            :disabled="updateLoading"
            @click="saveChanges"
          >
            {{ updateLoading ? 'Envoi...' : 'Valider les modifications' }}
          </button>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { getUser } from '../services/auth.js'
import { getProfile, getSolde, updateProfile, updatePassword } from '../services/account.js'

const editMode = ref(false)
const loadError = ref('')
const solde = ref(0)
const maskedCardNumber = ref('1234')

const form = reactive({
  prenom: '',
  nom: '',
  email: ''
})

const originalForm = reactive({
  prenom: '',
  nom: '',
  email: ''
})

const passwordForm = reactive({
  mot_de_passe_actuel: '',
  nouveau_mot_de_passe: '',
  confirm: '',
})

const updateMessage = ref('')
const updateSuccess = ref(false)
const updateLoading = ref(false)

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

  originalForm.prenom = form.prenom
  originalForm.nom = form.nom
  originalForm.email = form.email

  const soldeData = await getSolde(user.id)
  if (soldeData && typeof soldeData.solde !== 'undefined') {
    solde.value = soldeData.solde
  }

  maskedCardNumber.value = user.id.slice(-4)
})

function formatBalance(amount) {
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  }).format(amount)
}

function enterEditMode() {
  editMode.value = true
  updateMessage.value = ''
}

function cancelEdit() {
  form.prenom = originalForm.prenom
  form.nom = originalForm.nom
  form.email = originalForm.email

  passwordForm.mot_de_passe_actuel = ''
  passwordForm.nouveau_mot_de_passe = ''
  passwordForm.confirm = ''

  updateMessage.value = ''
  editMode.value = false
}

async function saveChanges() {
  updateMessage.value = ''
  updateSuccess.value = false
  const user = getUser()
  if (!user?.id) return

  updateLoading.value = true
  let hasError = false

  if (form.nom !== originalForm.nom || form.prenom !== originalForm.prenom) {
    const profileResult = await updateProfile(user.id, {
      nom: form.nom,
      prenom: form.prenom,
      email: form.email,
    })

    if (!profileResult.success) {
      updateSuccess.value = false
      updateMessage.value = profileResult.error || 'Erreur lors de la mise à jour du profil'
      hasError = true
    } else {
      originalForm.prenom = form.prenom
      originalForm.nom = form.nom
      originalForm.email = form.email
    }
  }

  if (!hasError && passwordForm.nouveau_mot_de_passe) {
    if (passwordForm.nouveau_mot_de_passe !== passwordForm.confirm) {
      updateSuccess.value = false
      updateMessage.value = 'Les deux mots de passe ne correspondent pas.'
      updateLoading.value = false
      return
    }
    if (passwordForm.nouveau_mot_de_passe.length < 6) {
      updateSuccess.value = false
      updateMessage.value = 'Le nouveau mot de passe doit faire au moins 6 caractères.'
      updateLoading.value = false
      return
    }
    if (!passwordForm.mot_de_passe_actuel) {
      updateSuccess.value = false
      updateMessage.value = 'Veuillez saisir votre mot de passe actuel.'
      updateLoading.value = false
      return
    }

    const passwordResult = await updatePassword(user.id, {
      mot_de_passe_actuel: passwordForm.mot_de_passe_actuel,
      nouveau_mot_de_passe: passwordForm.nouveau_mot_de_passe,
    })

    if (!passwordResult.success) {
      updateSuccess.value = false
      updateMessage.value = passwordResult.error || 'Erreur lors du changement de mot de passe'
      hasError = true
    } else {
      passwordForm.mot_de_passe_actuel = ''
      passwordForm.nouveau_mot_de_passe = ''
      passwordForm.confirm = ''
    }
  }

  updateLoading.value = false

  if (!hasError) {
    updateSuccess.value = true
    updateMessage.value = 'Modifications enregistrées avec succès.'

    setTimeout(() => {
      editMode.value = false
      updateMessage.value = ''
    }, 1500)
  }
}
</script>

<style src="../css/AccountView.css" scoped></style>
