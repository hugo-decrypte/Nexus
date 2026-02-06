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

<script>
export default {
  name: 'HistoriqueView',
  data() {
    return {
      transactions: [
        { id: '1', compte: '**** 1234', date: '28/01/2025', beneficiaire: 'Jean Dupont', description: 'Envoi PO', montant: '+ 500 PO' },
        { id: '2', compte: '**** 1234', date: '25/01/2025', beneficiaire: 'Marie Martin', description: 'Reçu PO', montant: '- 200 PO' },
      ],
    }
  },
  async mounted() {
    await this.loadTransactions()
  },
  methods: {
    async loadTransactions() {
      try {
        const token = localStorage.getItem('token')
        const user = JSON.parse(localStorage.getItem('user') || '{}')
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
          this.transactions = list.map((t) => ({
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
    },
  },
}
</script>

<style src="../css/HistoriqueView.css" scoped></style>
