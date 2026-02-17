<template>
  <div class="recevoir-view">
    <header class="recevoir-header">
      <RouterLink to="/home" class="recevoir-back">
        <span class="material-icons recevoir-back-icon">arrow_back</span>
        <span>Retour page accueil</span>
      </RouterLink>
    </header>

    <div class="recevoir-cards">
      <section class="recevoir-card">
        <h2 class="recevoir-card-title">Recevoir des PO</h2>
        <p class="recevoir-hint">
          Générez un QR code pour qu'un autre utilisateur puisse vous envoyer des PO.
        </p>

        <div class="recevoir-options">
          <label class="recevoir-option">
            <input v-model="montantMode" type="radio" value="libre" />
            <span>Montant libre</span>
          </label>
          <p class="recevoir-option-desc">L'autre personne choisit le montant.</p>

          <label class="recevoir-option">
            <input v-model="montantMode" type="radio" value="fixe" />
            <span>Montant fixe</span>
          </label>
          <div v-if="montantMode === 'fixe'" class="recevoir-montant-wrap">
            <input
              v-model.number="montantFixe"
              type="number"
              min="1"
              step="1"
              class="recevoir-montant-input"
              placeholder="Montant en PO"
            />
            <img src="/img/PO.png" alt="PO" class="recevoir-po-badge" />
          </div>
        </div>

        <p v-if="qrError" class="recevoir-error">{{ qrError }}</p>
        <button
          type="button"
          class="recevoir-btn-qr recevoir-btn-qr-mobile-only"
          @click="genererQR"
        >
          <span class="material-icons">qr_code_2</span>
          Générer un QrCode
        </button>
      </section>
    </div>

    <Teleport to="body">
      <div
        v-if="showQrModal"
        class="recevoir-qr-overlay"
        @click.self="showQrModal = false"
      >
        <div class="recevoir-qr-modal">
          <h3 class="recevoir-qr-title">Scannez pour m'envoyer des PO</h3>
          <p class="recevoir-qr-desc">
            {{
              montantMode === 'fixe'
                ? `Montant : ${montantFixe || 0} PO`
                : "L'autre personne choisit le montant."
            }}
          </p>
          <div class="recevoir-qr-canvas-wrap">
            <qrcode-vue
              v-if="qrUrl"
              :value="qrUrl"
              :size="280"
              :margin="2"
              level="M"
              render-as="svg"
              class="recevoir-qr-img"
            />
          </div>
          <button type="button" class="recevoir-btn-close" @click="showQrModal = false">
            Fermer
          </button>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import QrcodeVue from 'qrcode.vue'
import { getUser } from '../services/auth.js'
import '../css/RecevoirView.css'

const montantMode = ref('libre')
const montantFixe = ref(0)
const showQrModal = ref(false)
const qrUrl = ref('')
const qrError = ref('')

function genererQR() {
  qrError.value = ''
  const user = getUser()
  if (!user?.id) {
    qrError.value = 'Vous devez être connecté.'
    return
  }
  if (montantMode.value === 'fixe' && (!montantFixe.value || montantFixe.value < 1)) {
    qrError.value = 'Saisissez un montant valide (≥ 1 PO).'
    return
  }
  const base = typeof window !== 'undefined' ? window.location.origin : ''
  const path = '/envoiePO'
  let url = `${base}${path}?dest=${user.id}`
  if (montantMode.value === 'fixe' && montantFixe.value) {
    url += `&montant=${Number(montantFixe.value)}`
  }
  qrUrl.value = url
  showQrModal.value = true
}
</script>
