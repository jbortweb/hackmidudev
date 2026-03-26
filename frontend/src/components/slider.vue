<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";

const props = defineProps({
  projects: {
    type: Array,
    required: true
  }
});

const current = ref(0);
let interval = null;

// Obtenemos las imágenes y textos de los proyectos ganadores
const images = computed(() => {
  const baseUrl = import.meta.env.VITE_API_URL ? import.meta.env.VITE_API_URL.replace('/api', '') : 'http://localhost:8000'

  return props.projects.map(p => {
    if (Array.isArray(p.images) && p.images.length > 0) {
      const firstImage = p.images[0];
      if (firstImage.startsWith('/img/') || firstImage.startsWith('/storage/')) {
        return `${baseUrl}${firstImage}`;
      }
      if (firstImage.startsWith('http')) {
        return firstImage;
      }
      return `${baseUrl}/storage/${firstImage}`;
    }
    return 'https://placehold.co/1200x800.webp?text=' + encodeURIComponent(p.title);
  });
});

const texts = computed(() => {
  return props.projects.map(p => p.title);
});

const goTo = (idx) => {
  current.value = idx;
};

onMounted(() => {
  if (images.value.length > 0) {
    interval = setInterval(() => {
      current.value = (current.value + 1) % images.value.length;
    }, 3500);
  }
});

onUnmounted(() => {
  if (interval) clearInterval(interval);
});
</script>

<template>
  <div
    class="relative w-full h-[50vh] md:h-[70vh] flex items-center justify-center overflow-hidden rounded-3xl shadow-2xl mb-12"
  >
    <!-- Mitad izquierda -->
    <div class="absolute left-0 top-0 w-full h-full overflow-hidden">
      <div class="relative w-full h-full">
        <img
          v-for="(img, idx) in images"
          :key="'left-' + idx"
          :src="img"
          class="absolute w-full h-full object-cover transition-transform duration-1000"
          :style="{
            transform:
              idx === current
                ? 'translateY(0)'
                : idx < current
                ? 'translateY(-100%)'
                : 'translateY(100%)',
            clipPath: 'inset(0 50% 0 0)',
            zIndex: idx === current ? 2 : 1,
          }"
        />
      </div>
    </div>
    <!-- Mitad derecha -->
    <div class="absolute right-0 top-0 w-full h-full overflow-hidden">
      <div class="relative w-full h-full">
        <img
          v-for="(img, idx) in images"
          :key="'right-' + idx"
          :src="img"
          class="absolute w-full h-full object-cover transition-transform duration-1000"
          :style="{
            transform:
              idx === current
                ? 'translateY(0)'
                : idx < current
                ? 'translateY(100%)'
                : 'translateY(-100%)',
            clipPath: 'inset(0 0 0 50%)',
            zIndex: idx === current ? 2 : 1,
          }"
        />
      </div>
    </div>
    
    <!-- Overlay gradiente para legibilidad -->
    <div class="absolute inset-0 bg-black/30 z-[5]"></div>

    <!-- Texto central -->
    <div
      class="slideshow-text absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10 text-3xl md:text-6xl font-black text-white text-center tracking-widest uppercase pointer-events-none select-none drop-shadow-2xl"
    >
      {{ texts[current] }}
    </div>

    <!-- Dots -->
    <div
      class="absolute right-6 top-1/2 -translate-y-1/2 flex flex-col gap-3 z-20"
    >
      <button
        v-for="(_, idx) in images"
        :key="'dot-' + idx"
        @click="goTo(idx)"
        class="w-2 h-8 rounded-full transition-all duration-300 cursor-pointer"
        :class="
          idx === current ? 'bg-white h-12' : 'bg-white/40'
        "
      ></button>
    </div>
  </div>
</template>

<style scoped>
.slideshow-text {
  font-family: 'Inter', sans-serif;
  line-height: 1.1;
}
@media (max-width: 767px) {
  .slideshow-text {
    font-size: 1.5rem;
    letter-spacing: 0.1em;
  }
}
</style>
