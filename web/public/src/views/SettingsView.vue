<template>
  <div class="settings-view">
    <h1 class="page-title">Paramètres</h1>

    <section class="settings-card">
      <h2 class="section-title">Langue</h2>
      <p class="section-desc">Choisissez la langue de l'application.</p>
      <div class="settings-row">
        <label class="settings-label">Langue</label>
        <select v-model="settings.langue" class="form-input form-select" @change="saveSettings">
          <option value="fr">Français</option>
          <option value="en">English</option>
        </select>
      </div>
    </section>

    <section class="settings-card">
      <h2 class="section-title">Notifications</h2>
      <p class="section-desc">Gérez les notifications.</p>
      <div class="settings-row settings-row-switch">
        <label class="settings-label">Activer les notifications</label>
        <button
          type="button"
          class="switch"
          :class="{ 'switch-on': settings.notifications }"
          :aria-pressed="settings.notifications"
          @click="toggleNotifications"
        >
          <span class="switch-knob"></span>
        </button>
      </div>
    </section>

    <section class="settings-card">
      <h2 class="section-title">Apparence</h2>
      <p class="section-desc">Thème d'affichage.</p>
      <div class="settings-row">
        <label class="settings-label">Thème</label>
        <select v-model="settings.theme" class="form-input form-select" @change="saveSettings">
          <option value="clair">Clair</option>
          <option value="sombre">Sombre</option>
        </select>
      </div>
    </section>

    <p v-if="settingsMessage" class="account-success">{{ settingsMessage }}</p>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

const SETTINGS_KEY = 'nexus_settings'

const defaultSettings = {
  langue: 'fr',
  notifications: true,
  theme: 'clair',
}

const settings = reactive({ ...defaultSettings })
const settingsMessage = ref('')

onMounted(() => {
  try {
    const saved = localStorage.getItem(SETTINGS_KEY)
    if (saved) {
      const parsed = JSON.parse(saved)
      Object.assign(settings, { ...defaultSettings, ...parsed })
    }
  } catch {
    // garder les valeurs par défaut
  }
})

function saveSettings() {
  try {
    localStorage.setItem(SETTINGS_KEY, JSON.stringify(settings))
    settingsMessage.value = 'Paramètres enregistrés.'
    setTimeout(() => { settingsMessage.value = '' }, 2000)
  } catch {
    settingsMessage.value = 'Erreur lors de l\'enregistrement.'
  }
}

function toggleNotifications() {
  settings.notifications = !settings.notifications
  saveSettings()
}
</script>

<style src="../css/SettingsView.css" scoped></style>
