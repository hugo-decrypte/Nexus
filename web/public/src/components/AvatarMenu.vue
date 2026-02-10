<template>
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
        <RouterLink to="/account" class="avatar-menu-item" role="menuitem" @click="closeMenu">Mon compte</RouterLink>
        <RouterLink to="/settings" class="avatar-menu-item" role="menuitem" @click="closeMenu">Paramètres</RouterLink>
        <button type="button" class="avatar-menu-item avatar-menu-item-btn" role="menuitem" @click="handleLogout">Se déconnecter</button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import { logout } from '../services/auth.js'

const emit = defineEmits(['logout'])

const userMenuOpen = ref(false)
const avatarDropdownRef = ref(null)

function closeMenu() {
  userMenuOpen.value = false
}

function handleLogout() {
  logout()
  closeMenu()
  emit('logout')
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
