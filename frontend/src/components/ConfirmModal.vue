<script setup>
import { ref, watch } from 'vue'
import { X } from 'lucide-vue-next'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Confirmar acción'
  },
  message: {
    type: String,
    default: '¿Estás seguro?'
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const isVisible = ref(false)

watch(() => props.show, (val) => {
  isVisible.value = val
  if (val) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

const handleConfirm = () => {
  emit('confirm')
  isVisible.value = false
  document.body.style.overflow = ''
}

const handleCancel = () => {
  emit('cancel')
  isVisible.value = false
  document.body.style.overflow = ''
}
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div 
        v-if="isVisible" 
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
      >
        <div 
          class="absolute inset-0 bg-black/80 backdrop-blur-sm"
          @click="handleCancel"
        ></div>
        
        <div class="relative bg-black border border-green-500/50 rounded-lg shadow-[0_0_40px_rgba(34,197,94,0.2)] max-w-md w-full p-6 font-mono">
          <button 
            @click="handleCancel"
            class="absolute top-4 right-4 text-green-500/50 hover:text-green-400 transition-colors"
          >
            <X :size="20" />
          </button>

          <h2 class="text-green-400 text-lg font-bold mb-2 tracking-wider uppercase">
            {{ title }}
          </h2>
          
          <p class="text-gray-400 text-sm mb-6">
            {{ message }}
          </p>

          <div class="flex gap-4 justify-end">
            <button 
              @click="handleCancel"
              class="px-4 py-2 text-xs font-bold uppercase tracking-widest border border-gray-700 text-gray-400 hover:text-white hover:border-gray-500 transition-all rounded"
            >
              Cancelar
            </button>
            <button 
              @click="handleConfirm"
              class="px-4 py-2 text-xs font-bold uppercase tracking-widest bg-red-500/20 border border-red-500/50 text-red-400 hover:bg-red-500/30 hover:border-red-400 transition-all rounded"
            >
              Confirmar
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active > div:last-child,
.modal-leave-active > div:last-child {
  transition: transform 0.2s ease;
}

.modal-enter-from > div:last-child,
.modal-leave-to > div:last-child {
  transform: scale(0.95);
}
</style>
