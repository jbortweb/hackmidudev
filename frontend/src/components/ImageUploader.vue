<script setup>
import { ref } from 'vue'
import { Upload, X, Loader2 } from 'lucide-vue-next'
import { useAuth } from '@clerk/vue'
import axios from 'axios'

const props = defineProps({
  modelValue: {
    type: [Array, String],
    default: ''
  },
  type: {
    type: String,
    default: 'projects'
  },
  maxImages: {
    type: Number,
    default: 3
  }
})

const emit = defineEmits(['update:modelValue'])

const { isLoaded, getToken } = useAuth()
const isDragging = ref(false)
const uploading = ref(false)
const error = ref(null)

const isSingleMode = props.maxImages === 1

const getImages = () => {
  if (isSingleMode) {
    return props.modelValue ? [props.modelValue] : []
  }
  return Array.isArray(props.modelValue) ? props.modelValue : []
}

const handleDrop = (e) => {
  e.preventDefault()
  isDragging.value = false
  const files = Array.from(e.dataTransfer?.files || [])
  if (files.length) uploadFiles(files)
}

const handleFileSelect = (e) => {
  const files = Array.from(e.target.files || [])
  if (files.length) uploadFiles(files)
  e.target.value = ''
}

const uploadFiles = async (files) => {
  if (uploading.value || !isLoaded.value) return
  
  uploading.value = true
  error.value = null
  
  try {
    const token = await getToken.value()
    let images = getImages()
    
    for (const file of files) {
      if (!file.type.startsWith('image/')) continue
      
      if (images.length >= props.maxImages && !isSingleMode) break
      
      const formData = new FormData()
      formData.append('image', file)
      formData.append('type', props.type)
      
      const response = await axios.post(
        `${import.meta.env.VITE_API_URL || 'http://localhost:8000/api'}/images/upload`,
        formData,
        {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'multipart/form-data'
          }
        }
      )
      
      if (isSingleMode) {
        images = [response.data.url]
      } else {
        images.push(response.data.url)
      }
    }
    
    if (isSingleMode) {
      emit('update:modelValue', images[0] || '')
    } else {
      emit('update:modelValue', images)
    }
  } catch (err) {
    console.error('Error al subir imagen:', err)
    error.value = 'Error al subir imagen'
  } finally {
    uploading.value = false
  }
}

const removeImage = async (index) => {
  const images = getImages()
  const url = images[index]
  if (!url) return
  
  if (url.includes('/storage/') || url.includes('/img/')) {
    try {
      if (isLoaded.value) {
        const token = await getToken.value()
        await axios.delete(
          `${import.meta.env.VITE_API_URL || 'http://localhost:8000/api'}/images/delete`,
          {
            headers: { Authorization: `Bearer ${token}` },
            data: { path: url }
          }
        )
      }
    } catch (err) {
      console.error('Error al eliminar imagen:', err)
    }
  }
  
  const newImages = [...images]
  newImages.splice(index, 1)
  
  if (isSingleMode) {
    emit('update:modelValue', '')
  } else {
    emit('update:modelValue', newImages)
  }
}

const getImageUrl = (url) => {
  if (!url) return ''
  if (url.startsWith('http')) return url
  const baseUrl = import.meta.env.VITE_API_URL ? import.meta.env.VITE_API_URL.replace('/api', '') : 'http://localhost:8000'
  return `${baseUrl}${url}`
}
</script>

<template>
  <div class="space-y-4">
    <!-- Preview de todas las imágenes -->
    <div v-if="getImages().length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
      <div 
        v-for="(url, index) in getImages()" 
        :key="index"
        class="relative rounded-lg overflow-hidden border border-green-500/20 group aspect-video"
        :class="isSingleMode ? 'max-w-[150px] aspect-square' : ''"
      >
        <img 
          :src="getImageUrl(url)" 
          alt="Imagen subida"
          class="w-full h-full object-cover"
        />
        
        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
          <button 
            type="button"
            @click="removeImage(index)"
            class="p-2 bg-red-500/80 rounded-full hover:bg-red-500 transition-colors"
          >
            <X :size="16" class="text-white" />
          </button>
        </div>
      </div>
    </div>

    <!-- Zona de subida -->
    <div 
      v-if="isSingleMode || getImages().length < maxImages"
      class="relative border-2 border-dashed rounded-xl p-6 text-center transition-all cursor-pointer"
      :class="[
        isDragging ? 'border-green-500 bg-green-500/10' : 'border-green-500/30 hover:border-green-500/50',
        uploading ? 'opacity-50 pointer-events-none' : ''
      ]"
      @dragenter.prevent="isDragging = true"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop="handleDrop"
    >
      <input 
        type="file" 
        accept="image/*"
        :multiple="!isSingleMode"
        @change="handleFileSelect"
        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
        :disabled="uploading"
      />
      
      <div class="flex flex-col items-center gap-3">
        <div v-if="uploading" class="p-3 bg-green-500/10 rounded-full">
          <Loader2 :size="28" class="text-green-500 animate-spin" />
        </div>
        <div v-else class="p-3 bg-green-500/10 rounded-full">
          <Upload :size="28" class="text-green-500" />
        </div>
        
        <div>
          <p class="text-green-400 font-black text-sm uppercase tracking-wider">
            {{ isDragging ? 'SOLTAR PARA SUBIR' : 'Arrastra o haz clic' }}
          </p>
          <p class="text-green-500/50 text-xs mt-1">
            {{ isSingleMode ? 'Reemplazar imagen' : `Subir (${getImages().length}/${maxImages})` }}
          </p>
        </div>
        
        <p class="text-green-900 text-[10px] uppercase">
          JPG, PNG, WEBP, GIF • Máx 5MB
        </p>
        
        <p v-if="error" class="text-red-500 text-xs">{{ error }}</p>
      </div>
    </div>
  </div>
</template>
