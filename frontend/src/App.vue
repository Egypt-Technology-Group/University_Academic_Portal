<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800 antialiased selection:bg-gold-500 selection:text-white" :dir="localeStore.dir">
    <!-- Navbar (Public routes only) -->
    <Navbar v-if="!isAdminRoute" />

    <!-- Main Router View Area -->
    <main :class="['flex-1', isAdminRoute ? 'flex flex-col' : '']">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Footer (Public routes only) -->
    <Footer v-if="!isAdminRoute" />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useLocaleStore } from './stores/locale'
import Navbar from './components/layout/Navbar.vue'
import Footer from './components/layout/Footer.vue'

const route = useRoute()
const localeStore = useLocaleStore()

const isAdminRoute = computed(() => {
  return route.path.startsWith('/admin')
})

onMounted(() => {
  localeStore.initLocale()
})
</script>
