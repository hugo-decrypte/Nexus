<template>
  <div class="app">
    <header class="navbar">
      <RouterLink to="/home" class="logo-link">
        <img class="logo" src="../img/logo.png" alt="NEXUS" />
      </RouterLink>

      <nav class="menu">
        <RouterLink to="/home" class="menu-item">
          <span class="menu-icon material-icons" aria-hidden="true">sync_alt</span>
          <span class="menu-label">Accueil</span>
        </RouterLink>
        <RouterLink to="/envoiePO" class="menu-item menu-item-ordinateur">
          <span class="menu-label">Envoyer des PO</span>
        </RouterLink>
        <RouterLink to="/rechargement" class="menu-item">
          <span class="menu-icon material-icons" aria-hidden="true">add_circle</span>
          <span class="menu-label">Rechargement</span>
        </RouterLink>
        <RouterLink to="/historique" class="menu-item">
          <span class="menu-icon material-icons" aria-hidden="true">schedule</span>
          <span class="menu-label">Historique</span>
        </RouterLink>
      </nav>

      <div class="user">
        <RouterLink v-if="!isLoggedIn" to="/login" class="logout link">Se connecter</RouterLink>
        <template v-else>
          <span class="user-name">{{ userDisplayName }}</span>
          <div class="avatar-dropdown" ref="avatarDropdownRef">
            <button
              type="button"
              class="avatar avatar-btn"
              aria-haspopup="true"
              :aria-expanded="userMenuOpen"
              @click="userMenuOpen = !userMenuOpen"
            >
              <span class="material-icons avatar-icon" aria-label="Compte">person</span>
            </button>
            <Transition name="dropdown">
              <div v-show="userMenuOpen" class="avatar-menu" role="menu">
                <RouterLink to="/account" class="avatar-menu-item" role="menuitem" @click="userMenuOpen = false">Account</RouterLink>
                <RouterLink to="/settings" class="avatar-menu-item" role="menuitem" @click="userMenuOpen = false">Settings</RouterLink>
                <button type="button" class="avatar-menu-item avatar-menu-item-btn" role="menuitem" @click="handleMenuLogout">Se déconnecter</button>
              </div>
            </Transition>
          </div>
        </template>
        <div v-if="!isLoggedIn" class="avatar">
          <span class="material-icons avatar-icon" aria-label="Compte">person</span>
        </div>
      </div>
    </header>

    <main class="dashboard">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute, RouterLink, RouterView } from 'vue-router'
import { logout, isAuthenticated, getUser } from './services/auth.js'
import { getProfile } from './services/account.js'

const router = useRouter()
const route = useRoute()
const userMenuOpen = ref(false)
const avatarDropdownRef = ref(null)
const isLoggedIn = ref(isAuthenticated())
const userDisplayName = ref('')

async function loadUserDisplayName() {
  const user = getUser()
  if (!user?.id) return
  const profile = await getProfile(user.id)
  if (profile?.prenom || profile?.nom) {
    userDisplayName.value = [profile.prenom, profile.nom].filter(Boolean).join(' ').trim()
  } else if (profile?.email) {
    userDisplayName.value = profile.email
  } else {
    userDisplayName.value = 'Prénom Nom'
  }
}

watch(() => route.path, () => {
  isLoggedIn.value = isAuthenticated()
  if (isLoggedIn.value) loadUserDisplayName()
})

watch(isLoggedIn, (loggedIn) => {
  if (loggedIn) loadUserDisplayName()
  else userDisplayName.value = ''
})

function handleLogout() {
  logout()
  isLoggedIn.value = false
  userMenuOpen.value = false
  router.push('/login')
}

function handleMenuLogout() {
  handleLogout()
}

function closeMenuOnClickOutside(event) {
  if (avatarDropdownRef.value && !avatarDropdownRef.value.contains(event.target)) {
    userMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeMenuOnClickOutside)
  if (isLoggedIn.value) loadUserDisplayName()
})
onUnmounted(() => {
  document.removeEventListener('click', closeMenuOnClickOutside)
})
</script>

