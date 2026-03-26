<script setup>
import TheHeader from './components/TheHeader.vue'
import TheFooter from './components/TheFooter.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-black text-gray-300 font-mono selection:bg-green-500 selection:text-black">
    <!-- Efecto sutil de escaneo (opcional) -->
    <div class="fixed inset-0 pointer-events-none z-[100] bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] bg-[length:100%_2px,3px_100%]"></div>
    
    <!-- Header -->
    <TheHeader />

    <!-- Main Content -->
    <main class="flex-grow relative">
      <router-view v-slot="{ Component }">
        <transition 
          enter-active-class="transition duration-500 ease-out"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition duration-300 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
          mode="out-in"
        >
          <div class="w-full h-full">
            <component :is="Component" />
          </div>
        </transition>
      </router-view>
    </main>

    <!-- Footer -->
    <TheFooter />
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&display=swap');

:root {
  --color-neon-green: #00ff41;
}

body {
  margin: 0;
  background-color: black;
  overflow-x: hidden;
}

.font-mono {
  font-family: 'JetBrains Mono', monospace;
}

/* Scrollbar estilo terminal */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #000;
}

::-webkit-scrollbar-thumb {
  background: #00ff4133;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #00ff4166;
}
</style>
