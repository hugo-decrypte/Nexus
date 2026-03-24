const TOKEN_KEY = 'nexus_token'

// Connexion : POST /api/auth/login (proxy Vite ou nginx redirige vers l'API)
export async function login(email, motDePasse) {
  try {
    const response = await fetch('/api/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email: String(email ?? '').trim(),
        mot_de_passe: String(motDePasse ?? ''),
      }),
    })

    let data = {}
    try {
      const text = await response.text()
      if (text && text.trim()) {
        data = JSON.parse(text)
      }
    } catch {
      if (!response.ok) {
        return { success: false, error: 'Erreur du serveur (vérifier que l’API est démarrée)' }
      }
      return { success: false, error: 'Réponse du serveur invalide' }
    }

    if (response.ok && data.needsOtp && data.pending_token) {
      return {
        success: false,
        needsOtp: true,
        pendingToken: data.pending_token,
        emailMasked: data.email_masked || '',
      }
    }

    if (response.ok && data.token) {
      if (data.id) {
        const user = {
          id: data.id,
          email: data.email,
          role: data.role,
        }
        try {
          localStorage.setItem('nexus_user', JSON.stringify(user))
        } catch {}
      }
      localStorage.setItem(TOKEN_KEY, data.token)
      return { success: true }
    }

    if (!response.ok) {
      return {
        success: false,
        error: data.error || 'Identifiants incorrects',
      }
    }
    // 200 sans token ni OTP (réponse inattendue)
    return {
      success: false,
      error:
        data.error ||
        'Réponse de connexion inattendue. Vérifiez l’API ou désactivez temporairement l’OTP (AUTH_SKIP_EMAIL_OTP).',
    }
  } catch (err) {
    console.error('Erreur de connexion:', err)
    const message = err.message || ''
    if (message.includes('Failed to fetch') || message.includes('NetworkError')) {
      return { success: false, error: 'Impossible de joindre le serveur. Vérifiez que l’API est démarrée (ex. port 6080).' }
    }
    return { success: false, error: 'Erreur de connexion au serveur' }
  }
}

export async function verifyLoginOtp(pendingToken, code) {
  try {
    const response = await fetch('/api/auth/login/verify-otp', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        pending_token: pendingToken,
        code: String(code).replace(/\D/g, '').slice(0, 6),
      }),
    })
    let data = {}
    try {
      const text = await response.text()
      if (text && text.trim()) data = JSON.parse(text)
    } catch {
      return { success: false, error: 'Réponse du serveur invalide' }
    }
    if (response.ok && data.token) {
      if (data.id) {
        const user = { id: data.id, email: data.email, role: data.role }
        try {
          localStorage.setItem('nexus_user', JSON.stringify(user))
        } catch {}
      }
      localStorage.setItem(TOKEN_KEY, data.token)
      return { success: true }
    }
    return { success: false, error: data.error || 'Code incorrect ou expiré' }
  } catch (err) {
    console.error('Erreur OTP:', err)
    return { success: false, error: 'Erreur de connexion au serveur' }
  }
}

// Inscription : POST /api/auth/register
export async function register(email, motDePasse, nom, prenom) {
  try {
    const response = await fetch('/api/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email,
        mot_de_passe: motDePasse,
        nom,
        prenom,
      }),
    })

    const contentType = response.headers.get('Content-Type') || ''
    let data = {}
    if (contentType.includes('application/json')) {
      try {
        data = await response.json()
      } catch {
        return { success: false, error: 'Réponse du serveur invalide' }
      }
    }

    if (response.ok) {
      return { success: true }
    }
    return { success: false, error: data.error || "Erreur lors de l'inscription" }
  } catch (err) {
    console.error('Erreur d\'inscription:', err)
    const message = err.message || ''
    if (message.includes('Failed to fetch') || message.includes('NetworkError')) {
      return { success: false, error: 'Impossible de joindre le serveur. Vérifiez que l\'API est démarrée (ex. port 6080).' }
    }
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