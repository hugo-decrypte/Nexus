<template>
  <div class="rechargement-view">
    <section class="recharge-card">
      <div class="recharge-card-left">
        <h1 class="recharge-title">Acheter des PO</h1>
        <p class="recharge-subtitle">Rechargez votre compte en PO</p>
      </div>
    </section>

    <section class="balance-display">
      <div class="balance-info">
        <span class="balance-label">Solde actuel</span>
        <div class="balance-amount">
          <span class="balance-value">{{ formatAmount(currentBalance) }}</span>
          <img src="/img/PO.png" alt="PO" class="balance-po-badge" />
        </div>
      </div>
    </section>

    <section class="preset-amounts-section">
      <h2 class="section-title">Choisir un montant</h2>

      <div class="preset-amounts-grid">
        <button
          v-for="preset in presetAmounts"
          :key="preset"
          type="button"
          class="preset-amount-card"
          :class="{ active: selectedAmount === preset }"
          @click="selectAmount(preset)"
        >
          <span class="preset-value">{{ preset }}</span>
          <img src="/img/PO.png" alt="PO" class="preset-po-badge" />
        </button>
      </div>

      <!-- Montant personnalisé -->
      <div class="custom-amount-wrapper">
        <label for="custom-amount" class="custom-amount-label">Ou saisissez un montant personnalisé</label>
        <div class="custom-amount-input-wrapper">
          <input
            id="custom-amount"
            v-model.number="customAmount"
            type="number"
            min="1"
            step="1"
            class="custom-amount-input"
            placeholder="Montant personnalisé"
            @input="onCustomAmountInput"
          />
          <img src="/img/PO.png" alt="PO" class="custom-po-badge" />
        </div>
      </div>
    </section>

    <section class="payment-section">
      <h2 class="section-title">Résumé de la commande</h2>

      <div class="order-summary">
        <div class="summary-row">
          <span class="summary-label">Montant de PO</span>
          <div class="summary-value">
            <span>{{ formatAmount(totalAmount) }}</span>
            <img src="/img/PO.png" alt="PO" class="summary-po-badge" />
          </div>
        </div>
        <div class="summary-row total-row">
          <span class="summary-label">Total à payer</span>
          <span class="summary-total">{{ formatAmount(totalAmount) }} €</span>
        </div>
      </div>

      <div class="payment-methods">
        <p class="payment-methods-label">Méthode de paiement</p>
        <div class="payment-method-card">
          <span class="material-icons payment-icon">credit_card</span>
          <span class="payment-method-name">Carte bancaire</span>
        </div>
      </div>

      <button
        type="button"
        class="btn-pay"
        :disabled="totalAmount <= 0 || isProcessing"
        @click="processPayment"
      >
        <span v-if="!isProcessing" class="material-icons">payment</span>
        <span v-if="isProcessing" class="material-icons spinning">refresh</span>
        {{ isProcessing ? 'Traitement en cours...' : `Payer ${formatAmount(totalAmount)} €` }}
      </button>

      <p class="payment-info">
        <span class="material-icons info-icon">lock</span>
        Paiement 100% sécurisé
      </p>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getSolde } from '../services/account.js'
import { getToken, getUser } from '../services/auth.js'

const presetAmounts = [100, 250, 500, 1000, 2500, 5000]

const currentBalance = ref(0)
const selectedAmount = ref(null)
const customAmount = ref(null)
const isProcessing = ref(false)
const userId = ref(null)

const totalAmount = computed(() => {
  if (customAmount.value && customAmount.value > 0) {
    return customAmount.value
  }
  return selectedAmount.value || 0
})

function formatAmount(value) {
  if (!value && value !== 0) return '0'
  return new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  }).format(value)
}

function selectAmount(amount) {
  selectedAmount.value = amount
  customAmount.value = null
}

function onCustomAmountInput() {
  if (customAmount.value && customAmount.value > 0) {
    selectedAmount.value = null
  }
}

onMounted(async () => {
  const user = getUser()
  if (user && user.id) {
    userId.value = user.id
    try {
      const soldeData = await getSolde(user.id)
      if (soldeData && soldeData.solde !== undefined) {
        currentBalance.value = soldeData.solde
      }
    } catch (err) {
      console.error('Erreur chargement solde:', err)
    }
  }
})

async function processPayment() {
  if (totalAmount.value <= 0 || !userId.value) return

  isProcessing.value = true

  try {
    const response = await fetch(`/api/users/${userId.value}/recharge`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${getToken()}`,
      },
      body: JSON.stringify({
        montant: totalAmount.value,
      }),
    })

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}))
      throw new Error(errorData.message || 'Erreur lors du paiement')
    }

    alert(`Paiement de ${formatAmount(totalAmount.value)} € effectué avec succès!\nVous avez reçu ${formatAmount(totalAmount.value)} PO.`)

    selectedAmount.value = null
    customAmount.value = null

    const soldeData = await getSolde(userId.value)
    if (soldeData && soldeData.solde !== undefined) {
      currentBalance.value = soldeData.solde
    }
  } catch (error) {
    console.error('Erreur paiement:', error)
    alert(`Erreur lors du paiement: ${error.message}`)
  } finally {
    isProcessing.value = false
  }
}
</script>

<style src="../css/RechargementView.css" scoped></style>
