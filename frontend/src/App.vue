<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800 antialiased selection:bg-gold-500 selection:text-white">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Router View Area -->
    <main class="flex-1">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useLocaleStore } from './stores/locale'
import Navbar from './components/layout/Navbar.vue'
import Footer from './components/layout/Footer.vue'

const localeStore = useLocaleStore()

onMounted(() => {
  localeStore.initLocale()
})
</script>
