<template>
  <div class="home-view">
    <!-- Carte solde utilisateur -->
    <section class="balance-card">
      <p class="card-owner-name">NEXUS</p>
      <div class="balance-card-left">
        <p class="user-name">{{ userFullName }}</p>
        <p class="account-number">{{ maskedAccountNumber }}</p>
      </div>
      <div class="balance-card-right">
        <p class="balance-label">Solde actuel</p>
        <div class="balance-value-row">
          <span class="balance-value">{{ formattedBalance }}</span>
          <img src="/img/PO.png" alt="PO" class="balance-badge">
        </div>
      </div>
    </section>

    <!-- Actions PO (mobile) -->
    <section class="po-actions">
      <RouterLink to="/envoiePO" class="po-action-card">
        <span class="material-icons po-action-icon">qr_code_2</span>
        <span class="po-action-label">Envoyer des PO</span>
      </RouterLink>
      <RouterLink to="/rechargement" class="po-action-card">
        <span class="material-icons po-action-icon">photo_camera</span>
        <span class="po-action-label">Recevoir des PO</span>
      </RouterLink>
    </section>

    <!-- Transactions récentes -->
    <section class="transactions-section">
      <div class="transactions-header">
        <h2 class="transactions-title">Transactions récentes</h2>
        <RouterLink to="/historique" class="btn-voir-tout">Voir tout →</RouterLink>
      </div>
      <div class="transactions-table-wrapper">
        <table class="transactions-table">
          <thead>
            <tr>
              <th>Compte</th>
              <th>Date</th>
              <th>Bénificiaire</th>
              <th>Description</th>
              <th>Montant</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(tx, index) in recentTransactions" :key="index">
              <td>{{ tx.compte }}</td>
              <td>{{ tx.date }}</td>
              <td>{{ tx.beneficiaire }}</td>
              <td>{{ tx.description }}</td>
              <td>{{ tx.montant }}</td>
            </tr>
            <tr v-if="recentTransactions.length === 0" class="empty-row">
              <td colspan="5">Aucune transaction récente</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const userFullName = ref('Pluto Calm')
const accountNumber = ref('9785')
const balance = ref(15250)
const recentTransactions = ref([
  {
    compte: '**** 1234',
    date: '28/01/2025',
    beneficiaire: 'Jean Dupont',
    description: 'Envoi PO',
    montant: '+ 500 PO',
  },
  {
    compte: '**** 1234',
    date: '25/01/2025',
    beneficiaire: 'Marie Martin',
    description: 'Reçu PO',
    montant: '- 200 PO',
  },
])

const maskedAccountNumber = computed(() => `N° **** ${accountNumber.value}`)
const formattedBalance = computed(() => balance.value.toLocaleString('fr-FR'))
</script>

<style src="../css/HomeView.css" scoped></style>
