<template>
  <div class="rechargement-view">
    <!-- Carte rouge en-tête -->
    <section class="recharge-card">
      <div class="recharge-card-left">
        <h1 class="recharge-title">Acheter des PO</h1>
        <p class="recharge-subtitle">Rechargez votre compte en PO</p>
      </div>
    </section>

    <!-- Solde actuel -->
    <section class="balance-display">
      <div class="balance-info">
        <span class="balance-label">Solde actuel</span>
        <div class="balance-amount">
          <span class="balance-value">{{ formatAmount(currentBalance) }}</span>
          <img src="/img/PO.png" alt="PO" class="balance-po-badge" />
        </div>
      </div>
    </section>

    <!-- Montants prédéfinis -->
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

    <!-- Résumé et paiement -->
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
import { ref, computed } from 'vue'

// Montants prédéfinis
const presetAmounts = [10, 25, 50, 100, 200, 500]

// État
const currentBalance = ref(15250) // TODO: récupérer depuis l'API
const selectedAmount = ref(null)
const customAmount = ref(null)
const isProcessing = ref(false)

// Montant total sélectionné
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

async function processPayment() {
  if (totalAmount.value <= 0) return

  isProcessing.value = true

  // TODO: Intégration avec une API de paiement
  // Simuler un délai de traitement
  setTimeout(() => {
    alert(`Paiement de ${formatAmount(totalAmount.value)} € effectué avec succès!\nVous avez reçu ${formatAmount(totalAmount.value)} PO.`)

    // Réinitialiser le formulaire
    selectedAmount.value = null
    customAmount.value = null
    isProcessing.value = false

    // TODO: Actualiser le solde depuis l'API
    currentBalance.value += totalAmount.value
  }, 2000)
}
</script>

<style src="../css/RechargementView.css" scoped></style>
