<template>
  <div class="historique-view">
    <section class="historique-card">
      <h2 class="historique-title">Transactions</h2>
      <div class="historique-table-wrapper">
        <table class="historique-table">
          <thead>
            <tr>
              <th>Compte</th>
              <th>Date</th>
              <th>Bénéficiaire</th>
              <th>Description</th>
              <th class="col-montant">Montant</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(tx, index) in transactions" :key="tx.id || index">
              <td>{{ tx.compte }}</td>
              <td>{{ tx.date }}</td>
              <td>{{ tx.beneficiaire }}</td>
              <td>{{ tx.description }}</td>
              <td class="col-montant">{{ tx.montant }}</td>
            </tr>
            <tr v-if="transactions.length === 0" class="empty-row">
              <td colspan="5">Aucune transaction</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getToken, getUser } from '../services/auth.js'

const transactions = ref([])

async function loadTransactions() {
  try {
    const token = getToken()
    const user = getUser()
    const userId = user?.id
    if (!token || !userId) return
    const baseUrl = import.meta.env.VITE_API_BASE_URL || ''
    const res = await fetch(`${baseUrl}/api/transactions/${userId}`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    if (!res.ok) return
    const data = await res.json()
    const list = Array.isArray(data) ? data : (data?.transactions ? data.transactions : [])
    if (list.length) {
      transactions.value = list.map((t) => ({
        id: t.id,
        compte: `**** ${String(t.emetteur_id || t.recepteur_id || '').slice(-4)}` || '—',
        date: t.created_at ? new Date(t.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '—',
        beneficiaire: t.beneficiaire || '—',
        description: t.description || 'Envoi / Réception PO',
        montant: t.montant != null ? `${t.montant > 0 ? '+' : ''} ${t.montant} PO` : '—',
      }))
    }
  } catch {
    // garde les données de démo
  }
}

onMounted(() => {
  loadTransactions()
})
</script>

<style src="../css/HistoriqueView.css" scoped></style>
