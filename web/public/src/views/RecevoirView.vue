<template>
  <div class="recevoir-view">
    <header class="recevoir-header">
      <RouterLink to="/home" class="recevoir-back">
        <span class="material-icons recevoir-back-icon">arrow_back</span>
        <span>Retour page accueil</span>
      </RouterLink>
    </header>

    <div class="recevoir-cards">
      <section class="recevoir-card">
        <h2 class="recevoir-card-title">Recevoir des PO</h2>
        <p class="recevoir-hint">Générez un QR code pour qu’un autre utilisateur puisse vous envoyer des PO.</p>
        <button type="button" class="recevoir-btn-qr recevoir-btn-qr-mobile-only" @click="genererQR">
          <span class="material-icons">qr_code_2</span>
          Générer un QrCode
        </button>
      </section>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'
import '../css/RecevoirView.css'

function genererQR() {
  // Fonctionnalité à venir
}
</script>
