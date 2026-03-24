<template>
  <div
    class="bg-gray-900 rounded-2xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-300 w-96 mx-auto border border-gray-800"
  >
    <div class="p-6">
      <!-- Header dark mode -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
          <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
          <span
            class="text-green-400 text-xs font-mono uppercase tracking-wider"
            >Published</span
          >
        </div>
        <div class="text-gray-400 text-xs font-mono">
          10 Ene 2024
        </div>
      </div>
      <!-- Image con overlay dark -->
      <div class="relative mb-6 rounded-xl overflow-hidden">
        <img
          src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=400&h=250&fit=crop"
          alt="Vue 3: Composition API en Profundidad"
          class="w-full h-32 object-cover"
        />
        <div
          class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"
        ></div>
        <div class="absolute top-3 right-3">
          <span
            class="bg-cyan-500 text-gray-900 px-2 py-1 rounded text-xs font-bold"
          >
            12 min
          </span>
        </div>
      </div>

      <!-- Content -->
      <h3 class="text-white text-xl font-bold mb-3 leading-tight">
        Vue 3: Composition API en Profundidad
      </h3>
      <p class="text-gray-300 text-sm mb-4 leading-relaxed">
        Domina la Composition API de Vue 3 con ejemplos prácticos
      </p>

      <!-- Tags dark mode -->
      <div class="flex flex-wrap gap-2 mb-6">
        <span
          class="bg-gray-800 border border-gray-700 text-cyan-400 px-3 py-1 rounded-lg text-xs font-medium"
        >
          Vue.js
        </span>
        <span
          class="bg-gray-800 border border-gray-700 text-cyan-400 px-3 py-1 rounded-lg text-xs font-medium"
        >
          JavaScript
        </span>
      </div>

      <!-- Author info dark -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
          <div
            class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center"
          >
            <span class="text-white text-sm font-bold">VM</span>
          </div>
          <div class="ml-3">
            <p class="text-white font-semibold text-sm">Vue Mastery</p>
            <p class="text-gray-400 text-xs">Tech Writer</p>
          </div>
        </div>
      </div>

      <!-- Stats dark mode -->
      <div
        class="flex items-center justify-between pt-4 border-t border-gray-800"
      >
        <div class="flex items-center space-x-4">
          <div class="flex items-center">
            <svg
              class="w-4 h-4 text-red-400 mr-2"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
              />
            </svg>
            <span class="text-gray-300 text-sm">294</span>
          </div>
          <div class="flex items-center">
            <svg
              class="w-4 h-4 text-cyan-400 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
              />
            </svg>
            <span class="text-gray-300 text-sm">45</span>
          </div>
        </div>

        <button
          class="bg-cyan-500 hover:bg-cyan-400 text-gray-900 px-4 py-2 rounded-lg font-bold text-sm transition-all duration-200 shadow-lg"
        >
          Read →
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Lógica del componente aquí
</script>
