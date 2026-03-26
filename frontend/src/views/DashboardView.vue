<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'
import { useUser, useAuth } from '@clerk/vue'
import { Plus, LayoutDashboard, Terminal, ExternalLink, Edit3, Trash2, ShieldAlert, ArrowLeft } from 'lucide-vue-next'
import { projectService, userService } from '../services/api'
import ConfirmModal from '../components/ConfirmModal.vue'

const router = useRouter()

const baseUrl = computed(() => {
  return import.meta.env.VITE_API_URL ? import.meta.env.VITE_API_URL.replace('/api', '') : 'http://localhost:8000'
})
const { user } = useUser()
const { isLoaded, isSignedIn, getToken } = useAuth()
const projects = ref([])
const loading = ref(true)
const showDeleteModal = ref(false)
const projectToDelete = ref(null)

watch([isLoaded, isSignedIn], ([loaded, signedIn]) => {
  if (loaded) {
    if (!signedIn) {
      router.push('/')
    } else {
      fetchUserProjects()
    }
  }
})

const fetchUserProjects = async () => {
  try {
    loading.value = true
    const token = await getToken()
    const response = await userService.getProfile(token)
    projects.value = response.data.projects || []
  } catch (err) {
    console.error('Error al cargar tus proyectos:', err)
  } finally {
    loading.value = false
  }
}

const confirmDelete = (id) => {
  projectToDelete.value = id
  showDeleteModal.value = true
}

const deleteProject = async () => {
  if (!projectToDelete.value) return
  
  try {
    if (!isLoaded.value) return
    const token = await getToken()
    await projectService.delete(projectToDelete.value, token)
    if (Array.isArray(projects.value)) {
      projects.value = projects.value.filter(p => p.id !== projectToDelete.value)
    }
    toast.success('Proyecto eliminado correctamente')
  } catch (err) {
    console.error('Error al eliminar proyecto:', err)
    toast.error('Error al eliminar el proyecto.')
  } finally {
    showDeleteModal.value = false
    projectToDelete.value = null
  }
}

onMounted(() => {
  if (isLoaded.value && isSignedIn.value) {
    fetchUserProjects()
  }
})
</script>

<template>
  <div class="container mx-auto px-4 py-12 font-mono">
    
    <!-- Botón Volver -->
    <router-link to="/" class="inline-flex items-center gap-2 text-xs font-black text-green-500/90 hover:text-green-400 mb-12 transition-all group uppercase tracking-widest italic">
      <span class="text-green-900 group-hover:text-green-500">[</span>
      <ArrowLeft :size="14" />
      VOLVER_AL_INICIO
      <span class="text-green-900 group-hover:text-green-500">]</span>
    </router-link>

    <!-- Terminal Header -->
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12 border-b border-green-500/20 pb-8">
      <div class="flex items-center gap-4">
        <div class="p-3 bg-green-500/10 text-green-500 rounded-lg border border-green-500/20 shadow-[0_0_15px_rgba(34,197,94,0.1)]">
          <Terminal :size="32" />
        </div>
        <div>
          <h1 class="text-3xl font-black text-white uppercase tracking-tighter">SISTEMA_DASHBOARD.sh</h1>
          <p class="text-green-500/60 text-xs italic mt-1 uppercase">
            > SESIÓN_ACTIVA: {{ user?.username || 'user_root' }} // ESTADO: EN_LÍNEA
          </p>
        </div>
      </div>

      <router-link 
        to="/project/create"
        class="flex items-center justify-center gap-3 px-8 py-3 bg-green-500 text-black rounded font-black hover:bg-white transition-all shadow-[0_0_30px_rgba(34,197,94,0.2)] uppercase text-xs italic tracking-widest"
      >
        <Plus :size="18" />
        NUEVO_PROYECTO.deb
      </router-link>
    </header>

    <!-- Lista de Proyectos -->
    <div v-if="loading" class="py-20 text-center">
      <div class="inline-block w-8 h-8 border-4 border-green-500/20 border-t-green-500 rounded-full animate-spin mb-4"></div>
      <p class="text-green-500/60 italic uppercase tracking-widest text-xs">ESCANEANDO_REGISTROS_EN_DB...</p>
    </div>

    <div v-else>
      <div v-if="projects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="project in projects" 
          :key="project.id"
          class="group bg-[#0a0a0a] border border-green-500/20 rounded-xl overflow-hidden hover:border-green-500/50 transition-all shadow-[0_0_20px_rgba(0,0,0,0.5)] flex flex-col"
        >
          <!-- Imagen Preview -->
          <div class="aspect-video bg-black relative overflow-hidden border-b border-green-500/10">
            <img 
              v-if="project.images && project.images[0]" 
              :src="project.images[0].startsWith('http') ? project.images[0] : baseUrl + project.images[0]" 
              class="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition-opacity duration-500"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-green-500/20 italic text-[10px] uppercase">
              SIN_VISTA_PREVIA
            </div>
            
            <!-- Badge de Ganador -->
            <div v-if="project.winner > 0" class="absolute top-3 left-3 px-2 py-1 bg-yellow-500 text-black text-[10px] font-black rounded uppercase">
              🏆 GANADOR_{{ project.winner }}
            </div>
          </div>

          <!-- Info -->
          <div class="p-6 flex-grow">
            <h3 class="text-white font-black uppercase tracking-tight truncate mb-1">{{ project.title }}</h3>
            <p class="text-green-500/40 text-[10px] uppercase italic mb-4">> EDICIÓN: {{ project.year }}</p>
            <p class="text-gray-400 text-xs line-clamp-2 font-light leading-relaxed mb-6">{{ project.description }}</p>
          </div>

          <!-- Acciones -->
          <div class="p-4 bg-green-500/5 border-t border-green-500/10 flex items-center justify-between gap-2">
            <router-link 
              :to="`/project/${project.id}`"
              class="p-2 text-green-500/60 hover:text-green-500 transition-colors"
              title="VER_DETALLE"
            >
              <ExternalLink :size="16" />
            </router-link>
            
            <div class="flex gap-2">
              <router-link 
                :to="`/project/edit/${project.id}`"
                class="p-2 text-blue-500/60 hover:text-blue-500 transition-colors"
                title="EDITAR"
              >
                <Edit3 :size="16" />
              </router-link>
              
              <button 
                @click="confirmDelete(project.id)"
                class="p-2 text-red-500/60 hover:text-red-500 transition-colors"
                title="ELIMINAR"
              >
                <Trash2 :size="16" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="py-20 bg-green-500/5 rounded-2xl border-2 border-dashed border-green-500/10 text-center flex flex-col items-center justify-center">
        <ShieldAlert :size="48" class="text-green-500/20 mb-6" />
        <p class="text-green-500/60 font-black uppercase tracking-widest text-sm mb-2">ADVERTENCIA: NO_SE_HAN_ENCONTRADO_REGISTROS</p>
        <p class="text-green-500/30 text-xs italic mb-8 max-w-xs">Actualmente no tienes ningún proyecto vinculado a tu identidad en este nodo central.</p>
        <router-link 
          to="/project/create"
          class="px-6 py-2 border border-green-500 text-green-500 hover:bg-green-500 hover:text-black transition-all font-black text-[10px] uppercase tracking-widest"
        >
          INICIAR_NUEVO_REGISTRO.sh
        </router-link>
      </div>
    </div>
  </div>

  <ConfirmModal 
    :show="showDeleteModal"
    title="Eliminar proyecto"
    message="¿Estás seguro de que deseas eliminar este proyecto? Esta acción no se puede deshacer."
    @confirm="deleteProject"
    @cancel="showDeleteModal = false; projectToDelete = null"
  />
</template>
