<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'
import { useUser, useAuth } from '@clerk/vue'
import { Save, ArrowLeft, Github, Linkedin, Globe, User, ShieldCheck, Mail, Trash2 } from 'lucide-vue-next'
import { userService } from '../services/api'
import ImageUploader from '../components/ImageUploader.vue'
import ConfirmModal from '../components/ConfirmModal.vue'

const router = useRouter()
const { user } = useUser()
const { isLoaded, isSignedIn, getToken, signOut } = useAuth()

const loading = ref(false)
const fetching = ref(true)
const deleting = ref(false)
const showDeleteModal = ref(false)

const form = ref({
  name: '',
  email: '',
  avatar_url: '',
  github_url: '',
  linkedin_url: '',
  website_url: ''
})

watch([isLoaded, isSignedIn], ([loaded, signedIn]) => {
  if (loaded && !signedIn) {
    router.push('/')
  }
})

const getAvatarUrl = (url) => {
  if (!url) return ''
  if (url.startsWith('http')) return url
  const baseUrl = import.meta.env.VITE_API_URL ? import.meta.env.VITE_API_URL.replace('/api', '') : 'http://localhost:8000'
  return `${baseUrl}${url}`
}

const fetchProfile = async () => {
  if (!isLoaded.value || !isSignedIn.value) return
  try {
    fetching.value = true
    const token = await getToken.value()
    const response = await userService.getProfile(token)
    const userData = response.data
    
    const clerkAvatar = user.value?.imageUrl || ''
    const storedAvatar = userData.avatar_url || ''
    
    form.value = {
      name: userData.name || user.value?.fullName || '',
      email: userData.email || user.value?.primaryEmailAddress?.emailAddress || '',
      avatar_url: storedAvatar || clerkAvatar,
      github_url: userData.github_url || '',
      linkedin_url: userData.linkedin_url || '',
      website_url: userData.website_url || ''
    }
  } catch (err) {
    console.error('Error al cargar perfil:', err)
  } finally {
    fetching.value = false
  }
}

const handleUpdate = async () => {
  try {
    if (!isLoaded.value || !isSignedIn.value) return
    loading.value = true
    const token = await getToken.value()
    
    const payload = {
      name: form.value.name,
      avatar_url: form.value.avatar_url,
      github_url: form.value.github_url,
      linkedin_url: form.value.linkedin_url,
      website_url: form.value.website_url,
    }
    
    await userService.sync(payload, token)
    toast.success('Perfil actualizado correctamente')
    router.push('/dashboard')
  } catch (err) {
    console.error('Error al actualizar perfil:', err)
    toast.error('Error al actualizar el perfil.')
  } finally {
    loading.value = false
  }
}

const handleDeleteAccount = () => {
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  try {
    deleting.value = true
    const token = await getToken.value()
    await userService.deleteProfile(token)
    toast.success('Cuenta eliminada correctamente')
    await signOut.value()
  } catch (err) {
    console.error('Error al eliminar cuenta:', err)
    toast.error('Error al eliminar la cuenta.')
    deleting.value = false
  }
}

onMounted(() => {
  fetchProfile()
})
</script>

<template>
  <div class="container mx-auto px-4 py-12 max-w-2xl font-mono">
    
    <!-- Botón Volver -->
    <router-link 
      to="/dashboard" 
      class="inline-flex items-center gap-2 text-green-500/60 hover:text-green-400 mb-8 text-xs uppercase italic tracking-tighter transition-all"
    >
      <ArrowLeft :size="14" />
      VOLVER_AL_DASHBOARD
    </router-link>

    <div class="bg-[#0a0a0a] border border-green-500/20 rounded-2xl overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)]">
      
      <!-- Header del Formulario -->
      <div class="bg-green-500/10 border-b border-green-500/20 p-8 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-black text-white uppercase tracking-tighter">CONFIG_PERFIL.sh</h1>
          <p class="text-green-500/60 text-xs italic mt-2 uppercase">
            > Nodo_Usuario: {{ user?.username || 'user_anon' }}
          </p>
        </div>
        <div class="hidden sm:block">
          <img :src="getAvatarUrl(form.avatar_url) || user?.imageUrl" class="w-16 h-16 rounded-lg border-2 border-green-500/30 object-cover" />
        </div>
      </div>

      <!-- Cargando -->
      <div v-if="fetching" class="p-12 text-center text-green-500">
        <p class="animate-pulse italic uppercase tracking-widest text-xs">RECUPERANDO_DATOS_DEL_SERVIDOR...</p>
      </div>

      <!-- Formulario -->
      <form v-else @submit.prevent="handleUpdate" class="p-8 space-y-8">
        
        <div class="p-4 bg-green-500/5 border border-green-500/20 rounded-lg flex items-start gap-4 mb-8">
          <ShieldCheck :size="20" class="text-green-500 shrink-0 mt-1" />
          <p class="text-[10px] text-green-500/80 leading-relaxed uppercase">
            Personaliza tu identidad en la plataforma. Estos datos se mostrarán en tus proyectos publicados para que otros participantes puedan contactar contigo.
          </p>
        </div>

        <!-- Datos Básicos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2">
            <label class="text-[10px] text-green-500 uppercase font-black tracking-widest flex items-center gap-2">
              <User :size="12" /> NOMBRE_COMPLETO
            </label>
            <input 
              v-model="form.name"
              type="text" 
              class="w-full bg-black border border-green-500/20 rounded-lg px-4 py-3 text-white placeholder:text-green-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500/50 transition-all"
            />
          </div>

          <div class="space-y-2">
            <label class="text-[10px] text-green-500 uppercase font-black tracking-widest flex items-center gap-2">
              <Mail :size="12" /> EMAIL_SINC
            </label>
            <input 
              v-model="form.email"
              type="email" 
              readonly
              class="w-full bg-black/50 border border-green-500/10 rounded-lg px-4 py-3 text-green-500/50 cursor-not-allowed italic"
            />
          </div>
        </div>

        <!-- Avatar -->
        <div class="space-y-2">
          <label class="text-[10px] text-green-500 uppercase font-black tracking-widest flex items-center gap-2">
            <Globe :size="12" /> AVATAR
          </label>
          <ImageUploader 
            v-model="form.avatar_url"
            type="users"
            :maxImages="1"
          />
        </div>

        <!-- Enlaces Sociales -->
        <div class="space-y-6">
          <div class="space-y-2">
            <label class="text-[10px] text-green-500 uppercase font-black tracking-widest flex items-center gap-2">
              <Github :size="12" /> GITHUB_PROFILE_URL
            </label>
            <input 
              v-model="form.github_url"
              type="url" 
              placeholder="https://github.com/tu-usuario"
              class="w-full bg-black border border-green-500/20 rounded-lg px-4 py-3 text-white placeholder:text-green-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500/50 transition-all"
            />
          </div>

          <div class="space-y-2">
            <label class="text-[10px] text-green-500 uppercase font-black tracking-widest flex items-center gap-2">
              <Linkedin :size="12" /> LINKEDIN_PROFILE_URL
            </label>
            <input 
              v-model="form.linkedin_url"
              type="url" 
              placeholder="https://linkedin.com/in/tu-perfil"
              class="w-full bg-black border border-green-500/20 rounded-lg px-4 py-3 text-white placeholder:text-green-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500/50 transition-all"
            />
          </div>

          <div class="space-y-2">
            <label class="text-[10px] text-green-500 uppercase font-black tracking-widest flex items-center gap-2">
              <Globe :size="12" /> PERSONAL_WEBSITE_URL
            </label>
            <input 
              v-model="form.website_url"
              type="url" 
              placeholder="https://tu-portfolio.com"
              class="w-full bg-black border border-green-500/20 rounded-lg px-4 py-3 text-white placeholder:text-green-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500/50 transition-all"
            />
          </div>
        </div>

        <!-- Acciones del Formulario -->
        <div class="pt-8 border-t border-green-500/10 flex justify-end gap-6">
          <router-link 
            to="/dashboard"
            class="px-8 py-3 text-green-500/50 hover:text-green-500 uppercase font-black text-xs transition-all italic"
          >
            DESCARTAR.sh
          </router-link>
          
          <button 
            type="submit"
            :disabled="loading"
            class="flex items-center gap-3 px-12 py-3 bg-green-500 text-black rounded font-black hover:bg-white transition-all shadow-[0_0_30px_rgba(34,197,94,0.3)] disabled:opacity-50 disabled:cursor-not-allowed uppercase text-xs italic tracking-widest"
          >
            <span v-if="loading" class="w-4 h-4 border-2 border-black border-t-transparent rounded-full animate-spin"></span>
            <Save v-else :size="18" />
            {{ loading ? 'SINC_EN_CURSO...' : 'GUARDAR_DATOS.deb' }}
          </button>
        </div>

      </form>

      <!-- Zona de Peligro -->
      <div class="p-8 border-t border-red-500/20 bg-red-500/5">
        <h3 class="text-red-400 text-xs font-black uppercase tracking-widest mb-4 flex items-center gap-2">
          <Trash2 :size="14" />
          ZONA_DE_PELIGRO
        </h3>
        <p class="text-gray-500 text-[10px] mb-6 leading-relaxed">
          Al eliminar tu cuenta se borrarán todos tus proyectos y datos asociados. Esta acción no se puede deshacer.
        </p>
        <button 
          @click="handleDeleteAccount"
          :disabled="deleting"
          class="flex items-center gap-3 px-6 py-3 border border-red-500/50 text-red-400 rounded-lg font-black text-xs hover:bg-red-500/10 hover:border-red-400 transition-all uppercase italic tracking-widest disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="deleting" class="w-4 h-4 border-2 border-red-400 border-t-transparent rounded-full animate-spin"></span>
          <Trash2 v-else :size="16" />
          {{ deleting ? 'ELIMINANDO...' : 'ELIMINAR_CUENTA.deb' }}
        </button>
      </div>
    </div>
  </div>

  <ConfirmModal 
    :show="showDeleteModal"
    title="Eliminar cuenta"
    message="¿Estás seguro de que deseas eliminar tu cuenta? Se eliminarán todos tus proyectos y datos asociados. Esta acción no se puede deshacer."
    @confirm="confirmDelete"
    @cancel="showDeleteModal = false"
  />
</template>
