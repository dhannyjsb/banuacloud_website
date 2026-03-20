import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './index.css'
import App from './App.vue'
import router from './router'
import { registerTrafficTracking } from './lib/traffic'

const app = createApp(App)
const pinia = createPinia()

registerTrafficTracking(router)

app.use(pinia)
app.use(router)
app.mount('#root')
