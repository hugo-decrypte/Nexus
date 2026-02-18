<template>
  <div class="admin-view">
    <header class="admin-header">
      <RouterLink to="/home" class="admin-back">
        <span class="material-icons">arrow_back</span>
        <span>Retour accueil</span>
      </RouterLink>
      <h1 class="admin-title">Administration</h1>
    </header>

    <p v-if="loadError" class="admin-error">{{ loadError }}</p>

    <section class="admin-card">
      <h2 class="admin-card-title">Comptes</h2>
      <div class="admin-toolbar">
        <div class="admin-search-wrap">
          <span class="material-icons admin-search-icon">search</span>
          <input
            v-model="searchQuery"
            type="search"
            class="admin-search-input"
            placeholder="Rechercher (nom, prénom, email, rôle)"
            autocomplete="off"
          />
        </div>
        <div class="admin-limit-wrap">
          <label class="admin-limit-label">Afficher</label>
          <select v-model.number="accountsLimit" class="admin-limit-select">
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
            <option :value="9999">Tous</option>
          </select>
        </div>
      </div>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Prénom</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Rôle</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in accountsDisplayed" :key="u.id">
              <td>{{ u.prenom }}</td>
              <td>{{ u.nom }}</td>
              <td>{{ u.email }}</td>
              <td>{{ u.role }}</td>
              <td>
                <button
                  v-if="canEditUser(u)"
                  type="button"
                  class="admin-btn-small"
                  @click="selectUserToEdit(u)"
                >
                  Modifier
                </button>
                <span v-else-if="u.role === 'admin'" class="admin-no-edit">—</span>
              </td>
            </tr>
            <tr v-if="accountsFiltered.length === 0" class="empty-row">
              <td colspan="5">{{ searchQuery ? 'Aucun compte ne correspond à la recherche.' : 'Aucun compte' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section v-if="editingUser" class="admin-card">
      <h2 class="admin-card-title">Modifier le compte</h2>
      <form class="admin-form" @submit.prevent="submitUpdate">
        <p class="admin-label">Compte : {{ editingUser.prenom }} {{ editingUser.nom }} ({{ editingUser.email }})</p>
        <div class="admin-form-row">
          <label class="admin-label">Prénom</label>
          <input v-model="editForm.prenom" type="text" class="admin-input" required />
        </div>
        <div class="admin-form-row">
          <label class="admin-label">Nom</label>
          <input v-model="editForm.nom" type="text" class="admin-input" required />
        </div>
        <div class="admin-form-row">
          <label class="admin-label">Email (lecture seule)</label>
          <input v-model="editForm.email" type="email" class="admin-input" disabled />
        </div>
        <p v-if="updateError" class="admin-error">{{ updateError }}</p>
        <p v-if="updateSuccess" class="admin-success">{{ updateSuccess }}</p>
        <button type="submit" class="admin-btn" :disabled="updateLoading">Enregistrer</button>
      </form>
    </section>

    <section class="admin-card">
      <h2 class="admin-card-title">Dernières transactions</h2>
      <div class="admin-toolbar">
        <div class="admin-limit-wrap">
          <label class="admin-limit-label">Afficher</label>
          <select v-model.number="transactionsLimit" class="admin-limit-select">
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
            <option :value="9999">Toutes</option>
          </select>
        </div>
      </div>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Émetteur</th>
              <th>Bénéficiaire</th>
              <th class="col-montant">Montant</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(tx, i) in displayTransactions" :key="tx.id || i">
              <td>{{ tx.date }}</td>
              <td>{{ tx.emetteur }}</td>
              <td>{{ tx.recepteur }}</td>
              <td class="col-montant">{{ tx.montant }}</td>
              <td>{{ tx.description || '—' }}</td>
            </tr>
            <tr v-if="displayTransactions.length === 0" class="empty-row">
              <td colspan="5">Aucune transaction</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { getUser } from '../services/auth.js'
import { getAdminAccounts, getAdminTransactions, updateUser } from '../services/admin.js'
import '../css/AdminView.css'

const currentUser = computed(() => getUser())

function canEditUser(u) {
  if (!u || !currentUser.value?.id) return false
  if (u.role === 'admin' && u.id !== currentUser.value.id) return false
  return true
}

const loadError = ref('')
const accounts = ref([])
const rawTransactions = ref([])

const editingUser = ref(null)
const editForm = reactive({ nom: '', prenom: '', email: '' })
const updateError = ref('')
const updateSuccess = ref('')
const updateLoading = ref(false)

const searchQuery = ref('')
const accountsLimit = ref(20)
const transactionsLimit = ref(20)

const accountsFiltered = computed(() => {
  const q = (searchQuery.value || '').trim().toLowerCase()
  if (!q) return accounts.value
  return accounts.value.filter(
    (u) =>
      (u.prenom || '').toLowerCase().includes(q) ||
      (u.nom || '').toLowerCase().includes(q) ||
      (u.email || '').toLowerCase().includes(q) ||
      (u.role || '').toLowerCase().includes(q)
  )
})

const accountsDisplayed = computed(() => {
  return accountsFiltered.value.slice(0, accountsLimit.value)
})

function formatNumber(n) {
  return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(n ?? 0)
}

const displayTransactions = computed(() => {
  const list = rawTransactions.value.slice(0, transactionsLimit.value)
  return list.map((t) => {
    const emetteur = accounts.value.find((u) => u.id === t.emetteur_id)
    const recepteur = accounts.value.find((u) => u.id === t.recepteur_id)
    return {
      id: t.id,
      date: t.date_creation
        ? new Date(t.date_creation).toLocaleString('fr-FR', { dateStyle: 'short', timeStyle: 'short' })
        : '—',
      emetteur: emetteur ? `${emetteur.prenom} ${emetteur.nom}` : (t.emetteur_id || '—'),
      recepteur: recepteur ? `${recepteur.prenom} ${recepteur.nom}` : (t.recepteur_id || '—'),
      montant: `${formatNumber(t.montant)} PO`,
      description: t.description,
    }
  })
})

function selectUserToEdit(u) {
  editingUser.value = u
  editForm.nom = u.nom
  editForm.prenom = u.prenom
  editForm.email = u.email
  updateError.value = ''
  updateSuccess.value = ''
}

async function load() {
  loadError.value = ''
  const data = await getAdminAccounts()
  if (!data) {
    loadError.value = 'Impossible de charger les comptes.'
    return
  }
  accounts.value = data
  const tx = await getAdminTransactions()
  if (tx) rawTransactions.value = Array.isArray(tx) ? tx : []
}

async function submitUpdate() {
  if (!editingUser.value) return
  updateError.value = ''
  updateSuccess.value = ''
  updateLoading.value = true
  const res = await updateUser(editingUser.value.id, editForm)
  updateLoading.value = false
  if (res.success) {
    updateSuccess.value = 'Compte mis à jour.'
    const u = accounts.value.find((a) => a.id === editingUser.value.id)
    if (u) {
      u.nom = editForm.nom
      u.prenom = editForm.prenom
    }
  } else {
    updateError.value = res.error || 'Erreur'
  }
}

onMounted(() => {
  load()
})
</script>
