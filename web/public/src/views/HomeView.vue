<template>
  <div class="home-view">
    <!-- Carte solde utilisateur -->
    <section class="balance-card">
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
      userFullName: 'Nom Prenom',
      accountNumber: '1234',
      balance: 15250,
      recentTransactions: [],
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

<style scoped>
.home-view {
  width: 95%;
  max-width: none;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Carte solde */
.balance-card {
  background: linear-gradient(135deg, #e85d5a 0%, #d64d4a 100%);
  border-radius: 12px;
  padding: 75px 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.balance-card-left .user-name {
  font-size: 1.75rem;
  font-weight: 600;
  margin-bottom: 8px;
}

.balance-card-left .account-number {
  font-size: 1.25rem;
  opacity: 0.95;
}

.balance-card-right {
  text-align: right;
}

.balance-label {
  font-size: 1.5rem;
  opacity: 0.95;
  margin-bottom: 8px;
}

.balance-value-row {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 20px;
}

.balance-value {
  font-size: 2.75rem;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.balance-badge {
  width: 65px;
  height: 65px;
  object-fit: contain;
  border-radius: 50%;
}

/* Section transactions */
.transactions-section {
  background: #fff;
  border-radius: 12px;
  padding: 20px 24px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
}

.transactions-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.transactions-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #333;
}

.btn-voir-tout {
  padding: 8px 16px;
  border: 2px solid #e85d5a;
  color: #e85d5a;
  border-radius: 15px;
  font-size: 0.9rem;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.2s, color 0.2s;
}

.btn-voir-tout:hover {
  background: #e85d5a;
  color: #fff;
}

.transactions-table-wrapper {
  overflow-x: auto;
}

.transactions-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
}

.transactions-table th {
  text-align: left;
  padding: 12px 14px;
  border-bottom: 2px solid #eee;
  color: #555;
  font-weight: 600;
}

.transactions-table td {
  padding: 12px 14px;
  border-bottom: 1px solid #eee;
  color: #333;
}

.transactions-table tbody tr:hover {
  background: #f9f9f9;
}

.transactions-table .empty-row td {
  text-align: center;
  color: #888;
  font-style: italic;
  padding: 24px;
}
</style>
