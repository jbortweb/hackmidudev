<script setup>
import { ref } from 'vue'
import { Linkedin, Mail, Github, Globe, X, Terminal, Cpu } from 'lucide-vue-next'

const showModal = ref(false)

const openModal = () => (showModal.value = true)
const closeModal = () => (showModal.value = false)
</script>

<template>
  <footer class="bg-black border-t border-green-500/20 py-12 mt-auto relative overflow-hidden font-mono">
    <!-- Decoración de fondo -->
    <div class="absolute inset-0 opacity-5 pointer-events-none">
      <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-green-500 to-transparent"></div>
    </div>

    <div class="container mx-auto px-4 text-center relative z-10">
      <div class="flex items-center justify-center gap-2 mb-4 text-green-500/70 text-[10px] uppercase tracking-[0.2em]">
        <Terminal :size="12" />
        <span>HACKATON2026 // HACK_MIDU_v2.0</span>
      </div>
      
      <p class="text-green-500/80 text-xs mb-6 italic">
        © 2026 jbortweb - Creado para la Hackathon CubePath
      </p>

      <button 
        @click="openModal"
        class="group relative inline-flex flex-col items-center gap-2"
      >
        <span class="text-green-500 font-black text-xs uppercase tracking-widest group-hover:text-white transition-colors italic">
          [ DESARROLLADO_POR_JBORTWEB ]
        </span>
        <div class="h-0.5 w-0 group-hover:w-full bg-green-500 transition-all duration-300"></div>
      </button>
    </div>

    <!-- Modal de Contacto Cybertech -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 font-mono">
        <!-- Overlay -->
        <div @click="closeModal" class="absolute inset-0 bg-black/80 backdrop-blur-md"></div>
        
        <!-- Contenido Modal -->
        <div class="bg-[#050505] border-2 border-green-500/30 rounded-lg shadow-[0_0_50px_rgba(34,197,94,0.2)] relative w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-300">
          
          <!-- Header del Modal -->
          <div class="bg-green-500/10 border-b border-green-500/20 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <Cpu :size="18" class="text-green-500 animate-pulse" />
              <span class="text-green-500 text-xs font-black uppercase tracking-tighter italic">INFO_DEL_DESARROLLADOR</span>
            </div>
            <button @click="closeModal" class="text-green-500/50 hover:text-green-400 transition-colors">
              <X :size="20" />
            </button>
          </div>

          <!-- Perfil -->
          <div class="p-8 text-center">
            <div class="relative inline-block mb-6">
              <div class="w-24 h-24 bg-green-500 rounded-lg flex items-center justify-center mx-auto border-2 border-green-400 shadow-[0_0_20px_rgba(34,197,94,0.4)] overflow-hidden grayscale hover:grayscale-0 transition-all">
                <img src="https://github.com/jbortweb.png" alt="jbortweb" class="w-full h-full object-cover" />
              </div>
              <div class="absolute -bottom-2 -right-2 bg-black border border-green-500 text-[8px] text-green-500 px-2 py-0.5 rounded font-black">
                ONLINE
              </div>
            </div>

            <h3 class="text-2xl font-black text-white mb-1 uppercase tracking-tighter">Jordi Bort</h3>
            <p class="text-green-500/60 text-[10px] font-black mb-8 uppercase italic tracking-widest italic">> full_stack_developer.sh</p>

            <!-- Links -->
            <div class="grid grid-cols-1 gap-3">
              <a href="https://www.linkedin.com/in/jordi-bort/" target="_blank" class="flex items-center gap-4 p-4 rounded bg-green-500/5 border border-green-500/10 hover:border-green-500 hover:bg-green-500 hover:text-black transition-all group">
                <Linkedin :size="18" class="text-green-500 group-hover:text-black" />
                <span class="text-xs font-black uppercase text-green-500 text-green-500 tracking-tighter italic">LINKEDIN_PERFIL</span>
              </a>
              
              <a href="mailto:jbortweb@gmail.com" class="flex items-center gap-4 p-4 rounded bg-green-500/5 border border-green-500/10 hover:border-green-500 hover:bg-green-500 hover:text-black transition-all group">
                <Mail :size="18" class="text-green-500 group-hover:text-black" />
                <span class="text-xs font-black uppercase text-green-500 tracking-tighter italic">PERSONAL_EMAIL</span>
              </a>

                <a href="https://github.com/jbortweb" target="_blank" class="flex items-center gap-4 p-4 rounded bg-green-500/5 border border-green-500/10 hover:border-green-500 hover:bg-green-500 hover:text-black transition-all group">
                  <Github :size="18" class="text-green-500 group-hover:text-black" />
                  <span class="text-xs font-black uppercase text-green-500 tracking-tighter italic">USER-GITHUB</span>
                </a>
                <a href="https://portafoliojbortweb.netlify.app/" target="_blank" class="flex items-center gap-4 p-4 rounded bg-green-500/5 border border-green-500/10 hover:border-green-500 hover:bg-green-500 hover:text-black transition-all group">
                  <Globe :size="18" class="text-green-500 group-hover:text-black" />
                  <span class="text-xs font-black uppercase text-green-500 tracking-tighter italic">SITIO-WEB</span>
                </a>
            </div>
          </div>

          <!-- Footer Modal -->
          <div class="bg-green-500/5 p-4 border-t border-green-500/10 text-center">
            <p class="text-[8px] text-green-500/40 uppercase font-black">
              Conexión cifrada de extremo a extremo // v2.0.26
            </p>
          </div>
        </div>
      </div>
    </Teleport>
  </footer>
</template>
