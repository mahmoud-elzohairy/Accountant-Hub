<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: { type: String, required: true },
})

const isOpen = computed(() => props.status === 'open')
</script>

<template>
  <span
    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold"
    :class="isOpen ? 'bg-brand-50 text-brand-700' : 'bg-neutral-100 text-neutral-500'"
  >
    <span class="h-1.5 w-1.5 rounded-full" :class="isOpen ? 'bg-brand-500' : 'bg-neutral-400'" />
    {{ isOpen ? 'Open' : 'Closed' }}
  </span>
</template>
