<script setup>
import { Trophy, MessageSquare, Heart, ChevronRight } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import { computed } from 'vue'

const props = defineProps({
  project: {
    type: Object,
    required: true
  }
})

const router = useRouter()

// Obtener tecnologías como array (máximo 3)
const technologies = computed(() => {
  if (!props.project.technologies) return []
  return props.project.technologies.split(',').slice(0, 3)
})

// Función para obtener la imagen principal con soporte para el backend
const getProjectImage = (images) => {
  if (Array.isArray(images) && images.length > 0) {
    const firstImage = images[0]
    if (firstImage.startsWith('/img/') || firstImage.startsWith('/storage/')) {
      const baseUrl = import.meta.env.VITE_API_URL ? import.meta.env.VITE_API_URL.replace('/api', '') : 'http://localhost:8000'
      return `${baseUrl}${firstImage}`
    }
    return firstImage
  }
  return 'https://placehold.co/600x400.webp?text=Sin+Imagen'
}

const goToProject = () => {
  router.push(`/project/${props.project.id}`)
}

// Colores del ganador con estilo Cyberpunk
const getWinnerBadge = (position) => {
  switch(position) {
    case 1: return { text: '1º PUESTO', class: 'border-yellow-500 text-yellow-500 shadow-yellow-500/20' }
    case 2: return { text: '2º PUESTO', class: 'border-gray-400 text-gray-300 shadow-gray-400/20' }
    case 3: return { text: '3º PUESTO', class: 'border-orange-500 text-orange-500 shadow-orange-500/20' }
    default: return null
  }
}

const winnerBadge = getWinnerBadge(props.project.winner)
</script>

<template>
  <div
    class="bg-gray-900 rounded-2xl shadow-2xl overflow-hidden hover:shadow-green-500/10 transition-all duration-300 border border-gray-800 hover:border-green-500/50 group flex flex-col h-full cursor-pointer"
    @click="goToProject"
  >
    <div class="p-6 flex flex-col h-full">
      <!-- Header Estilo Terminal -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
          <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse shadow-[0_0_8px_rgba(74,222,128,0.8)]"></div>
          <span class="text-green-400 text-[10px] font-mono uppercase tracking-widest">Estado: Activo</span>
        </div>
        <div class="text-gray-300 text-[10px] font-mono tracking-tighter uppercase">
          AÑO: {{ project.year }}
        </div>
      </div>

      <!-- Imagen del proyecto con overlay -->
      <div class="relative mb-6 rounded-xl overflow-hidden aspect-video border border-gray-800 group-hover:border-green-500/30 transition-colors">
        <img
          :src="getProjectImage(project.images)"
          :alt="project.title"
          class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 opacity-80 group-hover:opacity-100"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
        
        <!-- Badge de Ganador Estilo Cyber -->
        <div v-if="winnerBadge" :class="['absolute top-3 right-3 flex items-center gap-1.5 px-3 py-1 rounded border bg-black/60 backdrop-blur-md text-[10px] font-mono font-black uppercase tracking-tighter shadow-lg', winnerBadge.class]">
          <Trophy :size="12" />
          {{ winnerBadge.text }}
        </div>
      </div>

      <!-- Content -->
      <div class="flex-grow">
        <h3 class="text-white text-xl font-black mb-3 leading-tight tracking-tight group-hover:text-green-400 transition-colors uppercase italic">
          {{ project.title }}
        </h3>
        <p class="text-gray-300 text-sm mb-6 leading-relaxed line-clamp-2 font-medium">
          {{ project.description }}
        </p>
      </div>

      <!-- Tecnologías -->
      <div class="flex flex-wrap gap-2 mb-8">
        <span 
          v-for="tech in technologies" 
          :key="tech"
          class="bg-black border border-green-500/20 text-green-400/90 px-2 py-0.5 rounded text-[14px] font-mono uppercase"
        >
          {{ tech.trim() }}
        </span>
      </div>

      <!-- Footer / Stats -->
      <div class="pt-5 border-t border-gray-800 group-hover:border-green-500/20 transition-colors">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-4">
            <div class="flex items-center group/stat">
              <Heart :size="14" class="text-gray-300 group-hover/stat:text-red-500 transition-colors mr-1.5" />
              <span class="text-gray-300 text-xs font-mono group-hover/stat:text-red-400">{{ project.likes }}</span>
            </div>
            <div class="flex items-center group/stat">
              <MessageSquare :size="14" class="text-gray-300 group-hover/stat:text-green-500 transition-colors mr-1.5" />
              <span class="text-gray-300 text-xs font-mono group-hover/stat:text-green-400">{{ project.comments_count }}</span>
            </div>
          </div>
          <span class="text-gray-500 text-[10px] font-mono truncate max-w-[150px] uppercase italic" :title="project.user?.name">
            By_{{ project.user?.name || 'anonymous' }}
          </span>
        </div>

        <div class="flex justify-center pb-2">
          <button
            class="w-3/4 flex items-center justify-center gap-2 bg-green-500 hover:bg-green-400 text-black px-4 py-2.5 rounded-lg font-black text-xs transition-all duration-300 shadow-[0_0_15px_rgba(34,197,94,0.3)] hover:shadow-green-500/50 uppercase tracking-tighter italic"
          >
            VER_PROYECTO.exe
            <ChevronRight :size="16" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.font-mono {
  font-family: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;
}
</style>
