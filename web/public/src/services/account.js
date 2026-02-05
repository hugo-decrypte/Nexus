import { getToken } from './auth.js'

function authHeaders() {
  const token = getToken()
  return {
    'Content-Type': 'application/json',
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
  }
}

/**
 * Récupère le profil utilisateur (Prénom, Nom, Email)
 * @param {string} userId
 * @returns {Promise<{id, nom, prenom, email, role}|null>}
 */
export async function getProfile(userId) {
  try {
    const response = await fetch(`/api/users/${userId}`, {
      headers: authHeaders(),
    })
    if (!response.ok) return null
    return await response.json()
  } catch (err) {
    console.error('Erreur chargement profil:', err)
    return null
  }
}

/**
 * Met à jour le profil (nom, prenom, email)
 * Nécessite une route PUT/PATCH /api/users/:id côté backend
 */
export async function updateProfile(userId, { nom, prenom, email }) {
  try {
    const response = await fetch(`/api/users/${userId}`, {
      method: 'PUT',
      headers: authHeaders(),
      body: JSON.stringify({ nom, prenom, email }),
    })
    const data = await response.json().catch(() => ({}))
    if (!response.ok) {
      return { success: false, error: data.error || data.message || 'Erreur lors de la mise à jour' }
    }
    return { success: true }
  } catch (err) {
    console.error('Erreur mise à jour profil:', err)
    return { success: false, error: 'Erreur de connexion au serveur' }
  }
}

/**
 * Change le mot de passe (mot de passe actuel + nouveau)
 * Nécessite une route côté backend (ex. PUT /api/users/:id/password)
 */
export async function updatePassword(userId, { mot_de_passe_actuel, nouveau_mot_de_passe }) {
  try {
    const response = await fetch(`/api/users/${userId}/password`, {
      method: 'PUT',
      headers: authHeaders(),
      body: JSON.stringify({
        mot_de_passe_actuel,
        nouveau_mot_de_passe,
      }),
    })
    const data = await response.json().catch(() => ({}))
    if (!response.ok) {
      return { success: false, error: data.error || data.message || 'Erreur lors du changement de mot de passe' }
    }
    return { success: true }
  } catch (err) {
    console.error('Erreur changement mot de passe:', err)
    return { success: false, error: 'Erreur de connexion au serveur' }
  }
}
