import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { initTheme } from './utils/theme.js'
import './css/app.css'

initTheme()

const app = createApp(App)

app.use(router)

app.mount('#app')