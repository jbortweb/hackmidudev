<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'
import { useUser, useAuth } from '@clerk/vue'
import { Save, ArrowLeft, Globe, Github, AlertTriangle, ShieldAlert } from 'lucide-vue-next'
import { projectService } from '../services/api'
import ImageUploader from '../components/ImageUploader.vue'

const route = useRoute()
const router = useRouter()
const { user } = useUser()
const { isLoaded, isSignedIn, getToken } = useAuth()

const isEdit = computed(() => !!route.params.id)
const loading = ref(false)
const fetching = ref(false)
const projectLimitReached = ref(false)

const form = ref({
  title: '',
  description: '',
  technologies: '',
  images: [],
  project_url: '',
  repo_url: '',
  year: new Date().getFullYear()
})

watch([isLoaded, isSignedIn], ([loaded, signedIn]) => {
  if (loaded && !signedIn) {
    router.push('/')
  }
})

const fetchProject = async () => {
  if (!isLoaded.value || !isSignedIn.value) return
  
  if (isEdit.value) {
    try {
      fetching.value = true
      const response = await projectService.getById(route.params.id)
      const project = response.data
      form.value = {
        title: project.title,
        description: project.description,
        technologies: project.technologies,
        images: project.images || [],
        project_url: project.project_url,
        repo_url: project.repo_url,
        year: project.year
      }
    } catch (err) {
      console.error('Error al cargar proyecto:', err)
    } finally {
      fetching.value = false
    }
  }
}

const handleSubmit = async () => {
  try {
    loading.value = true
    const token = await getToken()
    
    const payload = { ...form.value }

    if (isEdit.value) {
      await projectService.update(route.params.id, payload, token)
      toast.success('Proyecto actualizado correctamente')
    } else {
      await projectService.create(payload, token)
      toast.success('Proyecto creado correctamente')
    }

    router.push('/dashboard')
  } catch (err) {
    console.error('Error al guardar proyecto:', err)
    if (err.response?.status === 422) {
      toast.error('Has alcanzado el límite de 5 proyectos.')
      projectLimitReached.value = true
    } else {
      toast.error('Error al guardar el proyecto. Revisa los campos.')
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (isLoaded.value && isSignedIn.value) {
    fetchProject()
  }
})
</script>

<template>
  <div class="container mx-auto px-4 py-12 max-w-4xl font-mono">
    
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
      <div class="bg-green-500/10 border-b border-green-500/20 p-8">
        <h1 class="text-3xl font-black text-white uppercase tracking-tighter">
          {{ isEdit ? 'EDITAR_PROYECTO.sh' : 'REGISTRAR_NUEVO_PROYECTO.sh' }}
        </h1>
        <p class="text-green-500/60 text-xs italic mt-2 uppercase">
          > Acceso: Autorizado // Usuario: {{ user?.fullName || user?.username || 'user_anon' }}
        </p>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="handleSubmit" class="p-8 space-y-8">
        
        <!-- Alerta de límite alcanzado -->
        <div v-if="projectLimitReached" class="p-4 bg-red-500/10 border border-red-500/30 rounded flex gap-4">
          <ShieldAlert :size="20" class="text-red-500 shrink-0" />
          <p class="text-red-400 text-sm leading-relaxed">
            Has alcanzado el límite de <strong>5 proyectos</strong>. Elimina un proyecto existente para crear uno nuevo.
          </p>
        </div>
        
        <!-- Grid de Datos Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          
          <!-- Título -->
          <div class="space-y-2">
            <label class="text-[10px] text-green-500 uppercase font-black tracking-widest">TÍTULO_DEL_PROYECTO</label>
            <input 
              v-model="form.title"
              type="text" 
              required
              placeholder="Ej: Key Leap"
              class="w-full bg-black border border-green-500/20 rounded-lg px-4 py-3 text-white placeholder:text-green-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500/50 transition-all"
            />
          </div>

          <!-- Año -->
          <div class="space-y-2">
            <label class="text-[10px] text-green-500 uppercase font-black tracking-widest">EDICIÓN_HACKATHON (AÑO)</label>
            <input 
              v-model="form.year"
              type="number" 
              required
              class="w-full bg-black border border-green-500/20 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500/50 transition-all"
            />
          </div>

          <!-- Tecnologías -->
          <div class="space-y-2">
            <label class="text-[10px] text-green-500 uppercase font-black tracking-widest">TECNOLOGÍAS (Separadas por comas, max 3)</label>
            <input 
              v-model="form.technologies"
              type="text" 
              placeholder="Vue, Tailwind, Laravel"
              class="w-full bg-black border border-green-500/20 rounded-lg px-4 py-3 text-white placeholder:text-green-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500/50 transition-all"
            />
          </div>
        </div>

        <!-- Descripción -->
        <div class="space-y-2">
          <label class="text-[10px] text-green-500 uppercase font-black tracking-widest">DESCRIPCIÓN_TÉCNICA</label>
          <textarea 
            v-model="form.description"
            rows="5" 
            required
            placeholder="Describe qué hace tu proyecto y qué problemas resuelve..."
            class="w-full bg-black border border-green-500/20 rounded-lg px-4 py-3 text-white placeholder:text-green-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500/50 transition-all resize-none"
          ></textarea>
        </div>

        <!-- URLs -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2">
            <label class="text-[10px] text-green-500 uppercase font-black tracking-widest flex items-center gap-2">
              <Globe :size="12" /> URL_DESPLIEGUE
            </label>
            <input 
              v-model="form.project_url"
              type="url" 
              placeholder="https://tu-proyecto.vercel.app"
              class="w-full bg-black border border-green-500/20 rounded-lg px-4 py-3 text-white placeholder:text-green-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500/50 transition-all"
            />
          </div>
          <div class="space-y-2">
            <label class="text-[10px] text-green-500 uppercase font-black tracking-widest flex items-center gap-2">
              <Github :size="12" /> URL_REPOSITORIO
            </label>
            <input 
              v-model="form.repo_url"
              type="url" 
              placeholder="https://github.com/usuario/repo"
              class="w-full bg-black border border-green-500/20 rounded-lg px-4 py-3 text-white placeholder:text-green-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500/50 transition-all"
            />
          </div>
        </div>

        <!-- Imágenes del Proyecto -->
        <div class="space-y-4">
          <label class="text-[10px] text-green-500 uppercase font-black tracking-widest">
            IMÁGENES_DEL_PROYECTO (Max 3)
          </label>
          
          <ImageUploader 
            v-model="form.images"
            type="projects"
            :maxImages="3"
          />
        </div>

        <!-- Acciones del Formulario -->
        <div class="pt-8 border-t border-green-500/10 flex justify-end gap-6">
          <router-link 
            to="/dashboard"
            class="px-8 py-3 text-green-500/50 hover:text-green-500 uppercase font-black text-xs transition-all italic"
          >
            CANCELAR.sh
          </router-link>
          
          <button 
            type="submit"
            :disabled="loading"
            class="flex items-center gap-3 px-12 py-3 bg-green-500 text-black rounded font-black hover:bg-white transition-all shadow-[0_0_30px_rgba(34,197,94,0.3)] disabled:opacity-50 disabled:cursor-not-allowed uppercase text-xs italic tracking-widest"
          >
            <span v-if="loading" class="w-4 h-4 border-2 border-black border-t-transparent rounded-full animate-spin"></span>
            <Save v-else :size="18" />
            {{ loading ? 'EJECUTANDO...' : (isEdit ? 'ACTUALIZAR_REGISTRO' : 'CONFIRMAR_REGISTRO.deb') }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>
