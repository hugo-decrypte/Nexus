<template>
  <div class="envoie-po-view">
    <header class="envoie-header">
      <RouterLink to="/home" class="envoie-back">
        <span class="material-icons envoie-back-icon">arrow_back</span>
        <span>Retour page accueil</span>
      </RouterLink>
    </header>

    <div class="envoie-cards">
      <section class="envoie-card">
        <h2 class="envoie-card-title">Destinataire</h2>
        <p class="envoie-hint">Saisissez l’adresse email du destinataire</p>
        <div class="envoie-input-wrap">
          <input
            v-model="email"
            type="email"
            class="envoie-input"
            placeholder="Email du destinataire"
            @keyup.enter="rechercher"
          />
          <button
            type="button"
            class="envoie-input-icon-btn"
            aria-label="Rechercher"
            :disabled="searchLoading"
            @click="rechercher"
          >
            <span class="material-icons envoie-input-icon">search</span>
          </button>
        </div>
        <p v-if="searchError" class="envoie-error">{{ searchError }}</p>
        <p v-else-if="recipient" class="envoie-recipient-name">
          {{ recipient.prenom }} {{ recipient.nom }}
        </p>
      </section>

      <section class="envoie-card">
        <h2 class="envoie-card-title">Montant</h2>
        <div class="envoie-montant-row">
          <input
            v-model.number="montant"
            type="number"
            min="0"
            step="1"
            class="envoie-montant-input"
            placeholder="0"
          />
          <img src="/img/PO.png" alt="PO" class="envoie-po-badge" />
        </div>
      </section>

      <section class="envoie-card">
        <h2 class="envoie-card-title envoie-card-title-optional">Message (optionnel)</h2>
        <textarea
          v-model="message"
          class="envoie-textarea"
          placeholder="Message (optionnel)"
          rows="4"
        ></textarea>
      </section>

      <p v-if="sendError" class="envoie-error">{{ sendError }}</p>
      <p v-if="sendSuccess" class="envoie-success">{{ sendSuccess }}</p>
      <div class="envoie-btn-valider-wrap">
        <button
          type="button"
          class="envoie-btn envoie-btn-valider"
          :disabled="!canSend || sendLoading"
          @click="envoyer"
        >
          {{ sendLoading ? 'Envoi en cours...' : 'Envoyer' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { getUser } from '../services/auth.js'
import { searchUserByEmail, createTransaction } from '../services/transaction.js'
import '../css/EnvoiePOView.css'

const email = ref('')
const recipient = ref(null)
const searchLoading = ref(false)
const searchError = ref('')
const montant = ref(0)
const message = ref('')
const sendLoading = ref(false)
const sendError = ref('')
const sendSuccess = ref('')

watch(email, () => {
  recipient.value = null
  searchError.value = ''
})

async function rechercher() {
  const e = (email.value || '').trim()
  if (!e) {
    searchError.value = 'Saisissez un email.'
    return
  }
  searchError.value = ''
  searchLoading.value = true
  const result = await searchUserByEmail(e)
  searchLoading.value = false
  if (result.error) {
    searchError.value = result.error
    recipient.value = null
  } else {
    recipient.value = result.user
  }
}

const canSend = computed(() => {
  return recipient.value?.id && montant.value > 0
})

async function envoyer() {
  if (!canSend.value) return
  const user = getUser()
  if (!user?.id) {
    sendError.value = 'Vous devez être connecté.'
    return
  }
  sendError.value = ''
  sendSuccess.value = ''
  sendLoading.value = true
  const result = await createTransaction(
    user.id,
    recipient.value.id,
    montant.value,
    message.value
  )
  sendLoading.value = false
  if (result.success) {
    sendSuccess.value = `${montant.value} PO envoyés à ${recipient.value.prenom} ${recipient.value.nom}.`
    message.value = ''
    montant.value = 0
  } else {
    sendError.value = result.error || 'Erreur lors de l\'envoi.'
  }
}
</script>
