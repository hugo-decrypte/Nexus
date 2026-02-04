<template>
  <div class="home-view">
    <!-- Carte solde utilisateur -->
    <section class="balance-card">
      <p class="card-owner-name">NEXUS</p>
      <div class="balance-card-left">
        <div class="user-name-row">
          <p class="user-name">{{ userFullName }}</p>
          <span class="material-symbols-outlined card-icon" aria-hidden="true">credit_card</span>
        </div>
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

<script>
import { RouterLink } from 'vue-router'

export default {
  name: 'HomeView',
  components: {
    RouterLink,
  },
  data() {
    return {
      userFullName: 'Pluto Calm',
      accountNumber: '9785',
      balance: 15250,
      recentTransactions: [
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
      ],
    }
  },
  computed: {
    maskedAccountNumber() {
      return `N° **** ${this.accountNumber}`
    },
    formattedBalance() {
      return this.balance.toLocaleString('fr-FR')
    },
  },
}
</script>

<style src="../css/HomeView.css" scoped></style>
