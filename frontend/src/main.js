import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { clerkPlugin } from '@clerk/vue'
import router from './router'
import Toast from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const app = createApp(App)

const publishableKey = import.meta.env.VITE_CLERK_PUBLISHABLE_KEY

if (!publishableKey) {
  console.error("Falta la clave VITE_CLERK_PUBLISHABLE_KEY en el archivo .env")
}

app.use(clerkPlugin, {
  publishableKey
})

app.use(router)

app.use(Toast, {
  position: 'bottom-right',
  autoClose: 3000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  hideProgressBar: false
})

app.mount('#app')
