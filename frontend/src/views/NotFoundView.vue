<template>
  <div class="max-w-2xl mx-auto px-4 py-20 text-center space-y-6">
    <div class="text-8xl font-black text-gold-500 font-mono">404</div>
    <h1 class="text-2xl sm:text-3xl font-black text-navy-950">
      {{ localeStore.isRtl ? 'الصفحة غير موجودة' : 'Page Not Found' }}
    </h1>
    <p class="text-sm text-slate-600">
      {{ localeStore.isRtl ? 'عذراً، لم نتمكن من العثور على الصفحة أو الرابط المطلوب.' : 'Sorry, we could not find the requested page or link.' }}
    </p>
    <div class="pt-4">
      <Button to="/" variant="primary" size="md" rounded="xl">
        {{ $t('nav.home') }}
      </Button>
    </div>
  </div>
</template>

<script setup>
import { useLocaleStore } from '../stores/locale'
import Button from '../components/ui/Button.vue'

const localeStore = useLocaleStore()
</script>
