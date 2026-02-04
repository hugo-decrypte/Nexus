const TOKEN_KEY = 'nexus_token'

// connexion
export async function login(email, motDePasse) {
  try {
    const response = await fetch('/api/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email,
        mot_de_passe: motDePasse,
      }),
    })

    const data = await response.json()

    if (response.ok && data.token) {
      localStorage.setItem(TOKEN_KEY, data.token)
      return { success: true }
    }

    return { success: false, error: data.error || 'Identifiants incorrects' }
  } catch (err) {
    console.error('Erreur de connexion:', err)
    return { success: false, error: 'Erreur de connexion au serveur' }
  }
}

// deconnexion
export function logout() {
  localStorage.removeItem(TOKEN_KEY)
}
export function isAuthenticated() {
  const token = getToken()
  if (!token) return false

  // Vérifier si le token n'est pas expiré
  try {
    const payload = JSON.parse(atob(token.split('.')[1]))
    const now = Math.floor(Date.now() / 1000)
    return payload.exp > now
  } catch {
    return false
  }
}

export function getToken() {
  return localStorage.getItem(TOKEN_KEY)
}

export function getUser() {
  const token = getToken()
  if (!token) return null

  try {
    const payload = JSON.parse(atob(token.split('.')[1]))
    return {
      id: payload.sub,
      email: payload.data?.email,
      role: payload.data?.role,
    }
  } catch {
    return null
  }
}
