<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuth, useUser } from '@clerk/vue'
import { toast } from 'vue3-toastify'
import { projectService } from '../services/api'
import { Loader2, ArrowLeft, ExternalLink, Github, Trophy, Cpu, Database, Share2, Heart, MessageSquare, Send, Globe, Linkedin, Mail, User } from 'lucide-vue-next'

const route = useRoute()
const { isLoaded, isSignedIn, getToken } = useAuth()
const { user: clerkUser } = useUser()
const project = ref(null)
const loading = ref(true)
const error = ref(null)
const isLiked = ref(false)
const isLiking = ref(false)

const newComment = ref('')
const currentPage = ref(1)
const commentsPerPage = 5

const fetchProject = async () => {
  loading.value = true
  try {
    const response = await projectService.getById(route.params.id)
    project.value = response.data
  } catch (err) {
    console.error('Error al cargar detalle del proyecto:', err)
    error.value = 'ERROR_404: El proyecto no existe o el acceso ha sido denegado.'
  } finally {
    loading.value = false
  }
}

const formatImageUrl = (url) => {
  if (url && (url.startsWith('/img/') || url.startsWith('/storage/'))) {
    const baseUrl = import.meta.env.VITE_API_URL ? import.meta.env.VITE_API_URL.replace('/api', '') : 'http://localhost:8000'
    return `${baseUrl}${url}`
  }
  return url
}

const authorAvatar = computed(() => {
  if (!project.value?.user) return null
  return project.value.user.avatar_url || null
})

const paginatedComments = computed(() => {
  if (!project.value?.comments) return []
  const start = (currentPage.value - 1) * commentsPerPage
  const end = start + commentsPerPage
  return [...project.value.comments].sort((a,b) => new Date(a.created_at) - new Date(b.created_at)).slice(start, end)
})

const totalPages = computed(() => {
  if (!project.value?.comments) return 0
  return Math.ceil(project.value.comments.length / commentsPerPage)
})

const handleLike = async () => {
  if (!isLoaded.value) return
  
  if (!isSignedIn.value) {
    toast.info('Regístrate para dar like a este proyecto')
    return
  }
  
  if (isLiking.value) return
  
  try {
    isLiking.value = true
    const token = await getToken.value()
    const response = await projectService.toggleLike(route.params.id, token)

    isLiked.value = response.data.liked
    project.value.likes = response.data.likes
  } catch (err) {

    console.error('Error al dar like:', err)
    toast.error('Error al dar like')
  } finally {
    isLiking.value = false
  }
}

const submitComment = async () => {
  if (!newComment.value.trim()) return
  
  if (!isSignedIn.value) {
    toast.info('Regístrate para dejar un comentario')
    return
  }
  
  try {
    const token = await getToken.value()
    await projectService.addComment(route.params.id, { content: newComment.value }, token)
    toast.success('Comentario publicado')
    await fetchProject()
    newComment.value = ''
  } catch (err) {
    console.error('Error al publicar comentario:', err)
    toast.error('Error al publicar el comentario')
  }
}

onMounted(fetchProject)
</script>

<template>
  <div class="container mx-auto px-4 py-12 font-mono selection:bg-green-500 selection:text-black">
    <!-- Botón volver -->
    <router-link to="/" class="inline-flex items-center gap-2 text-xs font-black text-green-500/90 hover:text-green-400 mb-12 transition-all group uppercase tracking-widest italic">
      <span class="text-green-900 group-hover:text-green-500">[</span>
      <ArrowLeft :size="14" />
      VOLVER_ATRAS
      <span class="text-green-900 group-hover:text-green-500">]</span>
    </router-link>

    <div v-if="loading" class="flex flex-col items-center justify-center py-32">
      <Loader2 class="w-12 h-12 text-green-500 animate-spin mb-6" />
      <p class="text-green-500/60 text-sm tracking-[0.3em] animate-pulse uppercase italic">Descifrando paquete de datos...</p>
    </div>

    <div v-else-if="error" class="text-center py-32 border border-red-500/20 rounded-3xl bg-red-500/5">
      <p class="text-red-400 text-lg font-black uppercase italic mb-2">>> {{ error }}</p>
    </div>

    <div v-else-if="project" class="max-w-6xl mx-auto">
      
      <!-- En móvil: sidebar primero, luego contenido, luego comentarios -->
      <!-- En desktop: sidebar a la derecha, contenido a la izquierda -->
      
      <!-- Sidebar - Móvil primero / Desktop derecha -->
      <div class="lg:grid lg:grid-cols-12 lg:gap-12 space-y-8 lg:space-y-0">
        
        <!-- Sidebar (orden 1 en móvil, orden 2 en desktop) -->
        <aside class="lg:col-span-4 lg:order-2 space-y-8">
          
          <!-- Botón de Like -->
          <button 
            @click="handleLike"
            :disabled="!isLoaded || isLiking"
            class="group w-full p-8 bg-green-500/5 border rounded-[2rem] flex flex-col items-center justify-center gap-4 transition-all"
            :class="isLiked 
              ? 'border-red-500/50' 
              : 'border-green-500/20 hover:border-red-500/50'"
          >
            <div class="p-4 bg-black rounded-full border transition-all" :class="isLiked 
              ? 'border-red-500 scale-110' 
              : 'border-green-500/20 group-hover:border-red-500/30 group-hover:scale-110'">
              <Heart :size="32" :class="isLiked 
                ? 'text-red-500 fill-red-500' 
                : 'text-green-500 group-hover:text-red-500 fill-transparent'" 
                class="transition-all" />
            </div>
            <div class="text-center">
              <span class="block text-3xl font-black text-white tracking-tighter">{{ project.likes }}</span>
              <span class="text-[10px] text-green-500/90 uppercase font-black tracking-widest italic">likes</span>
            </div>
          </button>

          <!-- Perfil del Autor -->
          <div class="p-8 bg-black border border-green-500/20 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
            <h3 class="font-black text-white text-xs mb-8 uppercase tracking-widest italic flex items-center gap-2">
              <Cpu :size="16" class="text-green-500" />
              DATOS_DEL_AUTOR
            </h3>
            
            <div class="flex flex-col items-center text-center mb-10">
              <img v-if="authorAvatar" :src="formatImageUrl(authorAvatar)" class="w-24 h-24 rounded-2xl border-2 border-green-500/30 mb-4 shadow-[0_0_30px_rgba(34,197,94,0.2)] object-cover" />
              <div v-else class="w-24 h-24 rounded-2xl border-2 border-green-500/30 mb-4 shadow-[0_0_30px_rgba(34,197,94,0.2)] bg-green-500/10 flex items-center justify-center">
                <User :size="40" class="text-green-500/50" />
              </div>
              <h4 class="text-white font-black uppercase text-xl italic tracking-tight">{{ project.author || project.user?.name }}</h4>
            </div>

            <div class="space-y-3">
              <a v-if="project.repo_url" :href="project.repo_url" target="_blank" class="flex items-center gap-3 w-full p-4 border border-green-900/20 rounded-2xl text-green-400 text-xs font-black hover:border-green-500 hover:bg-green-500/5 transition-all">
                <Github :size="18" /> GITHUB_PERFIL
              </a>
              <a v-if="project.user?.linkedin_url" :href="project.user.linkedin_url" target="_blank" class="flex items-center gap-3 w-full p-4 border border-green-900/20 rounded-2xl text-green-400 text-xs font-black hover:border-green-500 hover:bg-green-500/5 transition-all">
                <Linkedin :size="18" /> LINKEDIN_USUARIO
              </a>
              <a v-if="project.project_url" :href="project.project_url" target="_blank" class="flex items-center gap-3 w-full p-4 border border-green-900/20 rounded-2xl text-green-400 text-xs font-black hover:border-green-500 hover:bg-green-500/5 transition-all">
                <Globe :size="18" /> PERSONAL_SITIO_WEB
              </a>
              <a v-if="project.user?.email" :href="'mailto:' + project.user.email" class="flex items-center gap-3 w-full p-4 border border-green-900/20 rounded-2xl text-green-400 text-xs font-black hover:border-green-500 hover:bg-green-500/5 transition-all">
                <Mail :size="18" /> CONTACTO_DIRECTO
              </a>
            </div>
          </div>

          <!-- Acceso Demo -->
          <div class="p-8 border border-green-500/20 rounded-[2.5rem] bg-green-500 text-black">
             <h3 class="font-black text-sm mb-6 uppercase tracking-widest italic flex items-center gap-2">
              <Database :size="16" />
              PUNTO_DE_ACCESO
            </h3>
            <a :href="project.project_url" target="_blank" class="flex items-center justify-between w-full py-4 px-6 bg-black text-green-500 rounded-2xl font-black text-xs hover:bg-white hover:text-black transition-all uppercase tracking-tighter italic">
             LANZAR_PROYECTO.sh
             <ExternalLink :size="16" />
            </a>
             <a :href="project.repo_url" target="_blank" class="mt-6 flex items-center justify-between w-full py-4 px-6 bg-black text-green-500 rounded-2xl font-black text-xs hover:bg-white hover:text-black transition-all uppercase tracking-tighter italic">
             VISITAR_REPO.deb
             <ExternalLink :size="16" />
            </a>           </div>

        </aside>

        <!-- Columna Izquierda: Proyecto (orden 2 en móvil, orden 1 en desktop) -->
        <div class="lg:col-span-8 lg:order-1">
          <header class="mb-12 relative">
            <div class="flex items-center gap-3 mb-6">
              <div v-if="project.winner > 0" class="flex items-center gap-1.5 px-3 py-1 rounded bg-yellow-500 text-black text-[10px] font-black uppercase italic shadow-[0_0_20px_rgba(234,179,8,0.4)]">
                <Trophy :size="12" />
                PROYECTO GANADOR
              </div>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-black text-white mb-8 tracking-tighter leading-[0.85] uppercase italic drop-shadow-[0_0_15px_rgba(255,255,255,0.1)]">
              {{ project.title }}
            </h1>

            <!-- Tecnologías -->
            <div class="flex flex-wrap gap-3 mb-10">
              <span v-for="tech in project.technologies?.split(',')" :key="tech" class="px-3 py-1 border border-green-500/20 text-green-400 text-[10px] font-bold uppercase rounded-full bg-green-500/5">
                {{ tech.trim() }}
              </span>
            </div>
          </header>

          <!-- Galería Real (Hasta 3 imágenes) -->
          <section class="grid grid-cols-1 gap-6 mb-16">
            <div 
              v-for="(img, index) in project.images?.slice(0, 3)" 
              :key="index" 
              class="rounded-3xl overflow-hidden border border-green-900/30 relative group shadow-2xl bg-gray-900"
            >
              <img :src="formatImageUrl(img)" class="w-full h-auto min-h-[300px] object-cover opacity-90 group-hover:opacity-100 transition-all duration-700" />
              <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60"></div>
              <div class="absolute bottom-6 left-6 text-[10px] text-green-500/40 font-mono uppercase tracking-[0.3em]">CAPTURA_DE_PANTALLA_0{{ index + 1 }}</div>
            </div>
          </section>

          <!-- Manifiesto -->
          <div class="p-10 border border-green-900/20 rounded-[2.5rem] bg-green-500/5 backdrop-blur-sm relative overflow-hidden mb-16">
            <h2 class="text-xl font-black text-white mb-8 uppercase tracking-tighter italic flex items-center gap-3">
              <span class="text-green-500">>></span> DESCRIPCIÓN_DEL_PROYECTO
            </h2>
            <p class="text-gray-300 leading-relaxed text-sm md:text-lg whitespace-pre-wrap font-medium">
              {{ project.description }}
            </p>
          </div>
        </div>
        
      </div>

      <!-- SECCIÓN COMENTARIOS - Siempre al final -->
      <section class="mt-16 space-y-8">
        <h2 class="text-xl font-black text-white uppercase tracking-tighter italic flex items-center gap-3">
          <MessageSquare :size="20" class="text-green-500" />
          FEEDBACK_DEL_SISTEMA ({{ project.comments?.length || 0 }})
        </h2>

        <!-- Formulario Comentar -->
        <div class="p-6 rounded-3xl bg-black/40">
          <textarea 
            v-model="newComment"
            placeholder="Escribe un comentario..."
            class="w-full bg-transparent p-2 border-2 border-green-900/90 text-green-400 placeholder:text-gray-300/50! focus:ring-0 focus:outline-none resize-auto font-mono text-md mb-4"
            rows="3"
          ></textarea>
          <div class="flex justify-end border-t border-green-900/10 pt-4">
            <button 
              @click="submitComment"
              class="flex items-center gap-2 px-6 py-2 bg-green-500 text-black rounded-xl font-black text-xs hover:bg-white transition-all uppercase italic"
            >
              ENVIAR_MENSAJE <Send :size="14" />
            </button>
          </div>
        </div>

        <!-- Lista de Comentarios Cronológica -->
        <div class="space-y-4">
          <div 
            v-for="comment in paginatedComments" 
            :key="comment.id"
            class="p-6 border border-green-900/10 rounded-2xl bg-green-500/2 hover:bg-green-500/5 transition-colors"
          >
            <div class="flex items-center gap-3 mb-3">
              <img v-if="comment.user?.avatar_url" :src="formatImageUrl(comment.user.avatar_url)" class="w-8 h-8 rounded-lg border border-green-500/20 object-cover" />
              <div v-else class="w-8 h-8 rounded-lg border border-green-500/20 bg-green-500/10 flex items-center justify-center">
                <User :size="16" class="text-green-500/50" />
              </div>
              <div>
                <span class="text-white text-xs font-black uppercase">{{ comment.user?.name || 'Anónimo' }}</span>
                <span class="text-[10px] text-green-900 ml-3">{{ new Date(comment.created_at).toLocaleString() }}</span>
              </div>
            </div>
            <p class="text-gray-400 text-sm leading-relaxed">{{ comment.content }}</p>
          </div>
        </div>

        <!-- Paginación de a 5 -->
        <div v-if="totalPages > 1" class="flex items-center justify-center gap-4 mt-10">
          <button 
            @click="currentPage--" 
            :disabled="currentPage === 1"
            class="px-4 py-2 border border-green-900/30 text-green-500 rounded-lg text-xs font-black disabled:opacity-20 hover:bg-green-500/10 transition-all italic"
          >
            &lt;&lt; ANTERIOR
          </button>
          <span class="text-[10px] text-green-900 font-black">PÁGINA {{ currentPage }} DE {{ totalPages }}</span>
          <button 
            @click="currentPage++" 
            :disabled="currentPage === totalPages"
            class="px-4 py-2 border border-green-900/30 text-green-500 rounded-lg text-xs font-black disabled:opacity-20 hover:bg-green-500/10 transition-all italic"
          >
            SIGUIENTE >>
          </button>
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
.font-mono {
  font-family: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;
}
textarea::placeholder {
  color: rgba(0, 255, 65, 0.2);
}
</style>
