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

<style>
/* Base */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, sans-serif;
}

body {
  background-color: white;
}

.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.navbar {
  height: 55px;
  background-color: #2b2b2b;
  color: white;
  display: flex;
  align-items: center;
  padding: 0 20px;
}

.logo-link {
  display: block;
  line-height: 0;
}

.logo {
  height: 125px;
  display: block;
}

.menu {
  display: flex;
  gap: 70px;
  margin-left: 50px;
}

.menu a {
  color: white;
  text-decoration: none;
  font-size: 15px;
}

/* Icônes du menu : masquées sur desktop */
.menu-icon {
  display: none;
}

.menu a:hover,
.menu a.router-link-active {
  text-decoration: underline;
}

.user {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 10px;
}

.logout {
  font-size: 14px;
  cursor: pointer;
}

.avatar {
  width: 35px;
  height: 35px;
  background-color: #ff6964;
  border-radius: 50%;
  display: flex;
}

.dashboard {
  flex: 1;
  padding: 20px;
}

@media screen and (max-width: 768px) {
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding-bottom: 70px;
  }

  .navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: black;
  }

  .logo {
    height: 100px;
    display: block;
  }

  .user {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .logout {
    font-size: 13px;
    color: white;
  }

  .menu-item-ordinateur {
    display: none !important;
  }

  .menu {
    display: flex;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #333;
    padding: 5px 8px 20px;
    justify-content: space-around;
    gap: 4px;
    margin-left: 0;
  }

  .menu .menu-item {
    color: white;
    text-decoration: none;
    font-size: 12px;
    padding: 8px 12px;
    flex: 1;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
  }

  /* Icônes visibles uniquement sur mobile, carré orange sur la page active */
  .menu .menu-icon {
    display: block;
    width: 60px;
    height: 32px;
    object-fit: contain;
    padding: 7px;
    background-color: none;
    border-radius: 16px;
    transition: background-color 0.2s ease, transform 0.2s ease;
  }

  .menu .menu-item.router-link-active .menu-icon,
  .menu .menu-item.router-link-exact-active .menu-icon {
    background-color: #e67e22;
  }

  .menu .menu-item:hover .menu-icon {
    background-color: #e67e22;
    transform: scale(1.10);
  }
/*pour oim : mettre les logos en blancs et finir lecarré entourer en orange  */
  .menu .menu-item.router-link-active:hover .menu-icon,
  .menu .menu-item.router-link-exact-active:hover .menu-icon {
    background-color: #e67e22;
  }

  .dashboard {
    padding-bottom: 80px;
  }
}
</style>
