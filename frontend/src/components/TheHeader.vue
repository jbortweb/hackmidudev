<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUser, SignInButton, SignUpButton, useAuth } from '@clerk/vue'
import { User, LayoutDashboard, LogOut, Calendar, ChevronDown, ChevronUp } from 'lucide-vue-next'
import ConfirmModal from './ConfirmModal.vue'

const { isSignedIn } = useUser()
const { signOut } = useAuth()
const route = useRoute()
const router = useRouter()

const showLogoutModal = ref(false)
const showYearDropdown = ref(false)

const currentYear = new Date().getFullYear()

const years = [
  { year: currentYear, label: `${currentYear} (Actual)` },
  { year: currentYear - 1, label: `${currentYear - 1}` },
]

const isYearActive = (year) => {
  return route.params.year == year || (!route.params.year && year === currentYear)
}

const selectYear = (year) => {
  if (year === currentYear) {
    router.push('/')
  } else {
    router.push(`/year/${year}`)
  }
  showYearDropdown.value = false
}

const handleLogout = () => {
  showLogoutModal.value = true
}

const confirmLogout = async () => {
  await signOut()
}
</script>

<template>
  <header class="bg-black border-b border-green-500/50 sticky top-0 z-50 backdrop-blur-xl font-mono">
    <div class="container mx-auto px-4 h-20 flex items-center justify-between">
      
      <!-- Logo tipo terminal -->
      <router-link to="/" class="flex items-center group">
        <div class="relative">
          <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center shadow-[0_0_20px_rgba(34,197,94,0.6)] group-hover:scale-105 transition-transform overflow-hidden border-2 border-green-400">
            <img src="https://static-cdn.jtvnw.net/jtv_user_pictures/ff538181-5311-4b24-8765-48630db88a93-profile_image-70x70.png" alt="Midudev" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all" />
          </div>
          <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full animate-pulse shadow-[0_0_10px_#4ade80]"></div>
        </div>
        <div class="ml-4 hidden sm:block">
          <span class="text-green-400 font-mono text-xl font-black tracking-widest uppercase drop-shadow-[0_0_8px_rgba(74,222,128,0.5)]">
            hack<span class="text-white">midu</span>
          </span>
          <div class="text-green-300/60 text-[10px] leading-none tracking-tighter uppercase font-bold italic">Acceso_Autorizado // v2.0.26</div>
        </div>
      </router-link>

      <!-- Menú y Auth -->
      <nav class="flex items-center gap-6">
        <div class="hidden md:flex items-center gap-6">
          <!-- Dropdown de Años -->
          <div class="relative">
            <button 
              @click="showYearDropdown = !showYearDropdown"
              class="flex items-center gap-2 text-[10px] font-black text-green-400 hover:text-white transition-all tracking-widest uppercase"
            >
              <Calendar :size="16" />
              <span class="hidden lg:inline">
                {{ route.params.year ? route.params.year : currentYear }}
              </span>
              <ChevronDown v-if="!showYearDropdown" :size="14" />
              <ChevronUp v-else :size="14" />
            </button>

            <!-- Dropdown Menu -->
            <Transition name="dropdown">
              <div 
                v-if="showYearDropdown"
                class="absolute top-full left-0 mt-2 bg-black border border-green-500/30 rounded-lg shadow-[0_0_30px_rgba(34,197,94,0.2)] min-w-[160px] overflow-hidden"
              >
                <button
                  v-for="item in years"
                  :key="item.year"
                  @click="selectYear(item.year)"
                  class="w-full px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-between"
                  :class="[
                    isYearActive(item.year) 
                      ? 'bg-green-500/20 text-green-400 border-l-2 border-green-500' 
                      : 'text-gray-400 hover:text-white hover:bg-green-500/10 border-l-2 border-transparent'
                  ]"
                >
                  {{ item.label }}
                </button>
              </div>
            </Transition>
          </div>
        </div>
        
        <div class="h-8 w-px bg-green-500/20 hidden sm:block"></div>

        <!-- Estado: No autenticado -->
        <template v-if="!isSignedIn">
          <div class="flex items-center gap-4">
            <SignInButton mode="modal">
              <button class="text-[10px] font-black text-green-500 hover:text-white cursor-pointer uppercase tracking-widest transition-all italic">
                // ACCEDER.sh
              </button>
            </SignInButton>
            <SignUpButton mode="modal">
              <button class="bg-green-500 text-black px-4 py-2 rounded font-black text-[10px] hover:bg-white transition-all shadow-[0_0_20px_rgba(34,197,94,0.3)] cursor-pointer uppercase tracking-widest italic">
                REGISTRO
              </button>
            </SignUpButton>
          </div>
        </template>
        
        <!-- Estado: Autenticado -->
        <template v-else>
          <div class="flex items-center gap-4">
            <router-link to="/dashboard" class="flex items-center gap-2 text-green-500 hover:text-white transition-all group" title="Dashboard">
              <LayoutDashboard :size="18" class="group-hover:drop-shadow-[0_0_5px_rgba(34,197,94,0.8)]" />
              <span class="hidden lg:inline text-[10px] font-black tracking-widest uppercase italic">DASHBOARD</span>
            </router-link>

            <router-link to="/profile" class="flex items-center gap-2 text-green-500 hover:text-white transition-all group" title="Mi Perfil">
              <User :size="18" class="group-hover:drop-shadow-[0_0_5px_rgba(34,197,94,0.8)]" />
              <span class="hidden lg:inline text-[10px] font-black tracking-widest uppercase italic">PERFIL</span>
            </router-link>

            <button @click="handleLogout" class="flex items-center gap-2 text-red-500/70 hover:text-red-400 transition-all group" title="Cerrar Sesión">
              <LogOut :size="18" />
              <span class="hidden lg:inline text-[10px] font-black tracking-widest uppercase italic">SALIR</span>
            </button>
          </div>
        </template>

      </nav>
    </div>
  </header>

  <ConfirmModal 
    :show="showLogoutModal"
    title="Cerrar sesión"
    message="¿Estás seguro de que deseas salir del sistema?"
    @confirm="confirmLogout"
    @cancel="showLogoutModal = false"
  />
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
