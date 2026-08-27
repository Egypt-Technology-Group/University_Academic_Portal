<template>
  <span ref="elementRef" class="inline-block">{{ displayValue }}</span>
</template>

<script setup>
import { toRef } from 'vue'
import { useAnimatedCounter } from '../../composables/useAnimatedCounter'

const props = defineProps({
  value: {
    type: [String, Number],
    required: true
  },
  duration: {
    type: Number,
    default: 1400
  },
  delay: {
    type: Number,
    default: 0
  }
})

const { displayValue, elementRef } = useAnimatedCounter(toRef(props, 'value'), {
  duration: props.duration,
  delay: props.delay
})
</script>
