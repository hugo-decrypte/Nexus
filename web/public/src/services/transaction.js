import { getToken } from './auth.js'

function authHeaders() {
  const token = getToken()
  return {
    'Content-Type': 'application/json',
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
  }
}

/**
 * Recherche un utilisateur par email (pour envoi de PO).
 * @param {string} email
 * @returns {Promise<{user: {id, prenom, nom, email}|null, error?: string}>}
 */
export async function searchUserByEmail(email) {
  const trimmed = (email || '').trim()
  if (!trimmed) return { user: null, error: 'Saisissez un email.' }
  try {
    const response = await fetch(`/api/users/search?email=${encodeURIComponent(trimmed)}`, {
      headers: authHeaders(),
    })
    if (response.status === 401) {
      return { user: null, error: 'Session expirée. Reconnectez-vous.' }
    }
    if (!response.ok) {
      const data = await response.json().catch(() => ({}))
      const msg = data.message || data.error || (response.status === 404 ? 'Aucun utilisateur avec cet email.' : `Erreur ${response.status}`)
      return { user: null, error: msg }
    }
    const user = await response.json()
    return { user, error: null }
  } catch (err) {
    console.error('Erreur recherche utilisateur:', err)
    return { user: null, error: 'Erreur de connexion au serveur.' }
  }
}

/**
 * Crée une transaction (envoi de PO).
 * @param {string} id_emetteur
 * @param {string} id_recepteur
 * @param {number} montant
 * @param {string} [description]
 * @returns {Promise<{success: boolean, error?: string}>}
 */
export async function createTransaction(id_emetteur, id_recepteur, montant, description = '') {
  try {
    const response = await fetch('/api/transactions', {
      method: 'POST',
      headers: authHeaders(),
      body: JSON.stringify({
        id_emetteur,
        id_recepteur,
        montant: Number(montant),
        description: description || undefined,
      }),
    })
    const data = await response.json().catch(() => ({}))
    if (!response.ok) {
      return { success: false, error: data.message || data.error || 'Erreur lors de l\'envoi.' }
    }
    return { success: true }
  } catch (err) {
    console.error('Erreur envoi PO:', err)
    return { success: false, error: 'Erreur de connexion au serveur.' }
  }
}
