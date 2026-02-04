<template>
  <div class="app">
    <header class="navbar">
      <RouterLink to="/home" class="logo-link">
        <img class="logo" src="../img/logo.png" alt="NEXUS" />
      </RouterLink>

      <nav class="menu">
        <RouterLink to="/home" class="menu-item">
          <img class="menu-icon" src="../img/Acceuil_logo.png" alt="" aria-hidden="true" />
          <span class="menu-label">Accueil</span>
        </RouterLink>
        <RouterLink to="/envoiePO" class="menu-item menu-item-ordinateur">
          <span class="menu-label">Envoyer des PO</span>
        </RouterLink>
        <RouterLink to="/rechargement" class="menu-item">
          <img class="menu-icon" src="../img/Rechargement_logo.png" alt="" aria-hidden="true" />
          <span class="menu-label">Rechargement</span>
        </RouterLink>
        <RouterLink to="/historique" class="menu-item">
          <img class="menu-icon" src="../img/Historique_logo.png" alt="" aria-hidden="true" />
          <span class="menu-label">Historique</span>
        </RouterLink>
      </nav>

      <div class="user">
        <span class="logout">Se déconnecter</span>
        <div class="avatar"></div>
      </div>
    </header>

    <main class="dashboard">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { RouterLink, RouterView } from 'vue-router'
</script>
