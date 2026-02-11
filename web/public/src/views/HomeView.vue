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
              <th>Bénéficiaire</th>
              <th>Description</th>
              <th>Montant</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(tx, index) in recentTransactions" :key="tx.id || index">
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
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { getUser } from '../services/auth.js'
import { getProfile, getSolde, getTransactions } from '../services/account.js'

const userFullName = ref('')
const accountNumber = ref('')
const balance = ref(0)
const recentTransactions = ref([])

const maskedAccountNumber = computed(() =>
  accountNumber.value ? `N° **** ${accountNumber.value}` : ''
)

const formattedBalance = computed(() =>
  new Intl.NumberFormat('fr-FR', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
  }).format(balance.value)
)

onMounted(async () => {
  const user = getUser()
  if (!user?.id) return

  const profile = await getProfile(user.id)
  if (profile) {
    userFullName.value = [profile.prenom, profile.nom].filter(Boolean).join(' ') || 'Utilisateur'
  }

  accountNumber.value = String(user.id).slice(-4)

  const soldeData = await getSolde(user.id)
  if (soldeData != null && typeof soldeData.solde !== 'undefined') {
    balance.value = soldeData.solde
  }

  const transactionsList = await getTransactions(user.id)
  if (transactionsList && transactionsList.length > 0) {
    recentTransactions.value = transactionsList.slice(0, 10).map((t) => ({
      id: t.id,
      compte: `**** ${String(t.emetteur_id || t.recepteur_id || '').slice(-4)}` || '—',
      date: t.created_at ? new Date(t.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '—',
      beneficiaire: t.beneficiaire || '—',
      description: t.description || 'Envoi / Réception PO',
      montant: t.montant != null ? `${t.montant > 0 ? '+' : ''} ${t.montant} PO` : '—',
    }))
  }
})
</script>

<style src="../css/HomeView.css" scoped></style>
