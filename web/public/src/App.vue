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
        <span class="logout" @click="handleLogout">Se déconnecter</span>
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
            </div>
          </Transition>
        </div>
      </div>
    </header>

    <main class="dashboard">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter, RouterLink, RouterView } from 'vue-router'
import { logout } from './services/auth.js'

const router = useRouter()
const userMenuOpen = ref(false)
const avatarDropdownRef = ref(null)

function handleLogout() {
  logout()
  router.push('/login')
}

function closeMenuOnClickOutside(event) {
  if (avatarDropdownRef.value && !avatarDropdownRef.value.contains(event.target)) {
    userMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeMenuOnClickOutside)
})
onUnmounted(() => {
  document.removeEventListener('click', closeMenuOnClickOutside)
})
</script>

