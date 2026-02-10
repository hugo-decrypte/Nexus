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
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { getProfile, getSolde } from '../services/account.js'
import { getToken, getUser } from '../services/auth.js'

const userFullName = ref('')
const accountNumber = ref('')
const balance = ref(0)
const recentTransactions = ref([])

const maskedAccountNumber = computed(() => `N° **** ${accountNumber.value}`)
const formattedBalance = computed(() => balance.value.toLocaleString('fr-FR'))

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('fr-FR')
}

onMounted(async () => {
  const user = getUser()
  if (!user || !user.id) return

  const profile = await getProfile(user.id)
  if (profile) {
    userFullName.value = `${profile.prenom} ${profile.nom}`
    accountNumber.value = user.id.slice(-4)
  }

  const soldeData = await getSolde(user.id)
  if (soldeData && soldeData.solde !== undefined) {
    balance.value = soldeData.solde
  }

  try {
    const response = await fetch(`/api/transactions/${user.id}`, {
      headers: { 'Authorization': `Bearer ${getToken()}` },
    })
    if (response.ok) {
      const transactions = await response.json()
      recentTransactions.value = transactions.slice(0, 5).map(tx => {
        const isReceived = tx.recepteur_id === user.id
        return {
          compte: `**** ${user.id.slice(-4)}`,
          date: formatDate(tx.created_at),
          beneficiaire: isReceived
            ? `**** ${(tx.emetteur_id || '').slice(-4)}`
            : `**** ${(tx.recepteur_id || '').slice(-4)}`,
          description: tx.description || (isReceived ? 'Reçu PO' : 'Envoi PO'),
          montant: isReceived
            ? `+ ${tx.montant.toLocaleString('fr-FR')} PO`
            : `- ${tx.montant.toLocaleString('fr-FR')} PO`,
          isReceived,
        }
      })
    }
  } catch (err) {
    console.error('Erreur chargement transactions:', err)
  }
})
</script>

<style src="../css/HomeView.css" scoped></style>
