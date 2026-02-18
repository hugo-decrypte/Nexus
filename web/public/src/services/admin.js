import { getToken } from './auth.js'

function authHeaders() {
  const token = getToken()
  return {
    'Content-Type': 'application/json',
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
  }
}

/**
 * Liste des comptes (admin).
 * @returns {Promise<Array<{id, nom, prenom, email, role}>|null>}
 */
export async function getAdminAccounts() {
  try {
    const res = await fetch('/api/admin/accounts', { headers: authHeaders() })
    if (!res.ok) return null
    return await res.json()
  } catch (err) {
    console.error('Erreur admin accounts:', err)
    return null
  }
}

/**
 * Toutes les transactions (admin).
 * @returns {Promise<Array<{id, emetteur_id, recepteur_id, montant, date_creation, description}>|null>}
 */
export async function getAdminTransactions() {
  try {
    const res = await fetch('/api/admin/transactions', { headers: authHeaders() })
    if (!res.ok) return null
    return await res.json()
  } catch (err) {
    console.error('Erreur admin transactions:', err)
    return null
  }
}

/**
 * Modifier un utilisateur (nom, prenom, email). Admin peut modifier n'importe quel compte.
 * @param {string} userId
 * @param {{ nom, prenom, email }} data
 * @returns {Promise<{ success: boolean, error?: string }>}
 */
export async function updateUser(userId, data) {
  try {
    const res = await fetch(`/api/users/${userId}`, {
      method: 'PUT',
      headers: authHeaders(),
      body: JSON.stringify({
        nom: (data.nom ?? '').trim(),
        prenom: (data.prenom ?? '').trim(),
        email: (data.email ?? '').trim(),
      }),
    })
    const body = await res.json().catch(() => ({}))
    if (!res.ok) {
      return { success: false, error: body.message || body.error || 'Erreur lors de la mise à jour' }
    }
    return { success: true }
  } catch (err) {
    console.error('Erreur mise à jour utilisateur:', err)
    return { success: false, error: 'Erreur de connexion au serveur' }
  }
}
