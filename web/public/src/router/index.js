import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import HistoriqueView from '../views/HistoriqueView.vue'
import RechargementView from '../views/RechargementView.vue'
import EnvoiePOView from '../views/EnvoiePOView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/home', name: 'home', component: HomeView },
    { path: '/historique', name: 'historique', component: HistoriqueView },
    { path: '/rechargement', name: 'rechargement', component: RechargementView },
    { path: '/envoiePO', name: 'envoiePO', component: EnvoiePOView },
    { path: '/', redirect: '/home' },
  ],
})

export default router
