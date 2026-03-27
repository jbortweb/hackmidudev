<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { projectService } from '../services/api'
import ProjectCard from '../components/ProjectCard.vue'
import Slider from '../components/slider.vue'
import { Loader2, Trophy, ChevronLeft, ChevronRight } from 'lucide-vue-next'

const currentYear = new Date().getFullYear()
const route = useRoute()
const router = useRouter()
const allProjects = ref([])
const loading = ref(true)
const error = ref(null)

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 12,
  total: 0
})

const availableYears = computed(() => {
  const years = new Set([currentYear])
  allProjects.value.forEach(p => {
    if (p.year) years.add(p.year)
  })
  return Array.from(years).sort((a, b) => b - a)
})

const selectedYear = computed(() => {
  return route.params.year ? parseInt(route.params.year) : null
})

const winners = computed(() => {
  const yearToFilter = selectedYear.value || currentYear
  const projects = Array.isArray(allProjects.value) ? allProjects.value : []
  return projects
    .filter(p => p.winner > 0 && p.year == yearToFilter)
    .sort((a, b) => a.winner - b.winner)
})

const currentYearProjects = computed(() => {
  const year = selectedYear.value || currentYear
  const projects = Array.isArray(allProjects.value) ? allProjects.value : []
  return projects
    .filter(p => p.year == year)
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

const olderProjects = computed(() => {
  const projects = Array.isArray(allProjects.value) ? allProjects.value : []
  return projects
    .filter(p => p.year !== currentYear)
    .sort((a, b) => b.year - a.year || new Date(b.created_at) - new Date(a.created_at))
})

const selectedYearProjects = computed(() => {
  const projects = Array.isArray(allProjects.value) ? allProjects.value : []
  return projects
    .filter(p => p.year === selectedYear.value)
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

const displayTotal = computed(() => {
  return Array.isArray(allProjects.value) ? allProjects.value.length : 0
})

const displayPagination = computed(() => {
  if (!selectedYear.value || selectedYear.value === currentYear) {
    return pagination.value
  }
  return null
})

const fetchProjects = async (page = 1) => {
  loading.value = true
  error.value = null
  try {
    const params = { page, per_page: 12 }
    const targetYear = selectedYear.value || currentYear
    params.year = targetYear
    
    const response = await projectService.getAll(params)
    const data = response.data
    
    if (data.data) {
      allProjects.value = data.data
      pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        per_page: data.per_page,
        total: data.total
      }
    } else {
      allProjects.value = data
      pagination.value.total = data.length
    }
  } catch (err) {
    console.error('Error al cargar proyectos:', err)
    error.value = 'ERR_CONNECTION_REFUSED: No se pudo establecer enlace con el núcleo de datos.'
  } finally {
    loading.value = false
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchProjects(page)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

onMounted(() => fetchProjects())
watch(() => route.params.year, () => fetchProjects())
</script>

<template>
  <div class="container mx-auto px-4 py-12 font-mono">
    
    <!-- Hero / Header de Sección Estilo Hacker -->
    <header class="max-w-4xl mb-20 relative">
      <div class="absolute -left-4 top-0 w-1 h-full bg-gradient-to-b from-green-500 to-transparent opacity-50"></div>
      <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tighter leading-none italic uppercase">
        EXPLORA EL <span class="text-green-400 drop-shadow-[0_0_10px_rgba(74,222,128,0.5)]">CÓDIGO</span> DE LA COMUNIDAD
      </h1>
      <p class="text-lg text-gray-300 max-w-2xl leading-relaxed border-l-2 border-green-900/30 pl-6 italic">
        >> Descubriendo la innovación y el talento en cada edición de la <span class="text-green-400/80 underline decoration-green-900 underline-offset-4">hackmidu</span> Regístrate y comparte tu proyecto.
      </p>
    </header>

    <!-- Estado de Carga -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-20 border border-green-500/10 rounded-3xl bg-green-500/5 backdrop-blur-sm">
      <div class="relative">
        <Loader2 class="w-16 h-16 text-green-500 animate-spin mb-6 opacity-80" />
        <div class="absolute inset-0 bg-green-500/20 blur-xl rounded-full"></div>
      </div>
      <p class="text-green-400 font-mono text-sm tracking-widest animate-pulse uppercase italic font-black">
        Iniciando volcado de memoria...
      </p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-500/5 border border-red-500/20 p-8 rounded-3xl text-center backdrop-blur-sm shadow-[0_0_30px_rgba(239,68,68,0.1)]">
      <p class="text-red-400 font-mono font-bold text-lg mb-4 uppercase tracking-tighter italic">>> ERROR_FATAL: {{ error }}</p>
      <button @click="fetchProjects" class="px-6 py-2 bg-red-500/10 border border-red-500/30 text-red-500 text-xs font-black uppercase tracking-widest hover:bg-red-500/20 transition-all rounded italic">
        REINTENTAR_ACCESO.sh
      </button>
    </div>

    <!-- Contenido -->
    <div v-else>
      
      <!-- Slider de Ganadores (solo si hay winners del año actual/seleccionado) -->
      <section v-if="winners.length > 0" class="mb-24">
        <div class="flex items-center gap-4 mb-10 group cursor-default">
          <div class="p-3 bg-yellow-500 text-black rounded group-hover:shadow-[0_0_20px_rgba(234,179,8,0.6)] transition-all">
            <Trophy :size="24" />
          </div>
          <div>
            <h2 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tighter italic">
              HALL_OF_FAME <span class="text-yellow-400">{{ selectedYear || currentYear }}</span>
            </h2>
            <div class="h-1 w-20 bg-yellow-900/50 mt-1"></div>
          </div>
        </div>
        
        <Slider :projects="winners" />
      </section>

      <!-- Proyectos del año actual (si estamos en home o año actual) -->
      <section v-if="!selectedYear || selectedYear === currentYear" class="mb-24">
        <div class="flex items-center justify-between mb-12 border-b border-green-900/20 pb-6">
          <h2 class="text-2xl font-black text-white uppercase tracking-tighter flex items-center gap-3 italic">
            <span class="text-green-500">&gt;</span>
            PROYECTOS_{{ currentYear }}: {{ displayTotal }}
          </h2>
          <span class="px-3 py-1 bg-green-500/10 border border-green-500/30 text-green-500 text-[10px] font-black uppercase tracking-widest rounded">
            NUEVOS
          </span>
        </div>
        
        <div v-if="currentYearProjects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <ProjectCard 
            v-for="project in currentYearProjects" 
            :key="project.id" 
            :project="project" 
          />
        </div>
        
        <div v-else class="text-center py-16 border-2 border-dashed border-green-900/20 rounded-3xl bg-black/40">
          <p class="text-green-900 font-mono text-sm tracking-widest uppercase italic font-bold">Esperando nuevos proyectos...</p>
        </div>

        <!-- Paginación -->
        <div v-if="displayPagination && displayPagination.last_page > 1" class="flex items-center justify-center gap-4 mt-12">
          <button 
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="p-3 border border-green-500/30 text-green-500 rounded-lg hover:bg-green-500/10 disabled:opacity-20 disabled:cursor-not-allowed transition-all"
          >
            <ChevronLeft :size="20" />
          </button>
          
          <div class="flex items-center gap-2">
            <button 
              v-for="page in pagination.last_page"
              :key="page"
              @click="goToPage(page)"
              class="w-10 h-10 rounded-lg font-black text-xs transition-all"
              :class="page === pagination.current_page 
                ? 'bg-green-500 text-black' 
                : 'border border-green-500/30 text-green-500 hover:bg-green-500/10'"
            >
              {{ page }}
            </button>
          </div>
          
          <button 
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="p-3 border border-green-500/30 text-green-500 rounded-lg hover:bg-green-500/10 disabled:opacity-20 disabled:cursor-not-allowed transition-all"
          >
            <ChevronRight :size="20" />
          </button>
        </div>
      </section>

      <!-- Proyectos de Años Anteriores (solo en home) -->
      <section v-if="!selectedYear && olderProjects.length > 0">
        <div class="flex items-center justify-between mb-12 border-b border-green-900/20 pb-6">
          <h2 class="text-2xl font-black text-white uppercase tracking-tighter flex items-center gap-3 italic">
            <span class="text-green-500/50">&gt;</span>
            HISTORICO: {{ olderProjects.length }} proyectos
          </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <ProjectCard 
            v-for="project in olderProjects" 
            :key="project.id" 
            :project="project" 
          />
        </div>
      </section>

      <!-- Filtro por año específico (cuando se selecciona un año) -->
      <section v-if="selectedYear && selectedYear !== currentYear">
        <div class="flex items-center justify-between mb-12 border-b border-green-900/20 pb-6">
          <h2 class="text-2xl font-black text-white uppercase tracking-tighter flex items-center gap-3 italic">
            <span class="text-green-500">&gt;</span>
            DATA_LOG_{{ selectedYear }}: {{ displayTotal }} proyectos
          </h2>
        </div>
        
        <div v-if="selectedYearProjects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <ProjectCard 
            v-for="project in selectedYearProjects" 
            :key="project.id" 
            :project="project" 
          />
        </div>
        
        <div v-else class="text-center py-24 border-2 border-dashed border-green-900/20 rounded-3xl bg-black/40">
          <p class="text-green-900 font-mono text-sm tracking-widest uppercase italic font-bold">Sin registros en este sector de memoria.</p>
        </div>

        <!-- Paginación para año específico -->
        <div v-if="displayPagination && displayPagination.last_page > 1" class="flex items-center justify-center gap-4 mt-12">
          <button 
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="p-3 border border-green-500/30 text-green-500 rounded-lg hover:bg-green-500/10 disabled:opacity-20 disabled:cursor-not-allowed transition-all"
          >
            <ChevronLeft :size="20" />
          </button>
          
          <div class="flex items-center gap-2">
            <button 
              v-for="page in pagination.last_page"
              :key="page"
              @click="goToPage(page)"
              class="w-10 h-10 rounded-lg font-black text-xs transition-all"
              :class="page === pagination.current_page 
                ? 'bg-green-500 text-black' 
                : 'border border-green-500/30 text-green-500 hover:bg-green-500/10'"
            >
              {{ page }}
            </button>
          </div>
          
          <button 
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="p-3 border border-green-500/30 text-green-500 rounded-lg hover:bg-green-500/10 disabled:opacity-20 disabled:cursor-not-allowed transition-all"
          >
            <ChevronRight :size="20" />
          </button>
        </div>
      </section>

    </div>
  </div>
</template>
