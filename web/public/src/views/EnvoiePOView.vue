<template>
  <div class="envoie-po-view">
    <header class="envoie-header">
      <RouterLink to="/home" class="envoie-back">
        <span class="material-icons envoie-back-icon">arrow_back</span>
        <span>Retour page accueil</span>
      </RouterLink>
    </header>

    <div class="envoie-cards">
      <section class="envoie-card">
        <h2 class="envoie-card-title">Destinataire</h2>
        <div class="envoie-input-wrap">
          <input
            v-model="destinataire"
            type="text"
            class="envoie-input"
            placeholder="ID Nexus ou email"
          />
          <span class="material-icons envoie-input-icon">search</span>
        </div>
        <button type="button" class="envoie-btn envoie-btn-qr">
          <span class="material-icons">qr_code_2</span>
          Générer un QrCode
        </button>
      </section>

      <section class="envoie-card">
        <h2 class="envoie-card-title">Montant</h2>
        <div class="envoie-montant-row">
          <span class="envoie-montant-value">{{ montant }}</span>
          <img src="/img/PO.png" alt="PO" class="envoie-po-badge" />
        </div>
      </section>

      <section class="envoie-card">
        <h2 class="envoie-card-title envoie-card-title-optional">Message (optionnel)</h2>
        <textarea
          v-model="message"
          class="envoie-textarea"
          placeholder="Message (optionnel)"
          rows="4"
        ></textarea>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import '../css/EnvoiePOView.css'

const destinataire = ref('')
const montant = ref('0')
const message = ref('')
</script>
