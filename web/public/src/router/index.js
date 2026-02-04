import { createRouter, createWebHistory } from 'vue-router'
import { isAuthenticated } from '../services/auth.js'
import HomeView from '../views/HomeView.vue'
import HistoriqueView from '../views/HistoriqueView.vue'
import RechargementView from '../views/RechargementView.vue'
import EnvoiePOView from '../views/EnvoiePOView.vue'
import Login from '../views/Login.vue'

/** true = connexion obligatoire pour accéder aux pages, false = accès libre à toutes les pages */
const AUTH_REQUIRED = false

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/login', name: 'login', component: Login, meta: { public: true } },
    { path: '/home', name: 'home', component: HomeView },
    { path: '/historique', name: 'historique', component: HistoriqueView },
    { path: '/rechargement', name: 'rechargement', component: RechargementView },
    { path: '/envoiePO', name: 'envoiePO', component: EnvoiePOView },
    { path: '/', redirect: '/home' },
  ],
})

router.beforeEach((to, from, next) => {
  if (!AUTH_REQUIRED) {
    next()
    return
  }

  const isPublic = to.meta.public === true

  if (!isPublic && !isAuthenticated()) {
    next('/login')
  } else if (to.path === '/login' && isAuthenticated()) {
    next('/home')
  } else {
    next()
  }
})

export default router
