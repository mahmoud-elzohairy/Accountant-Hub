<script setup>
import { computed } from 'vue'

const props = defineProps({
  meta: { type: Object, required: true }, // { current_page, last_page }
})
const emit = defineEmits(['change'])

const pages = computed(() => {
  const last = props.meta.last_page
  const current = props.meta.current_page
  const range = []
  const start = Math.max(1, current - 2)
  const end = Math.min(last, start + 4)
  for (let i = start; i <= end; i++) range.push(i)
  return range
})

function go(page) {
  if (page >= 1 && page <= props.meta.last_page && page !== props.meta.current_page) {
    emit('change', page)
  }
}
</script>

<template>
  <nav v-if="meta.last_page > 1" class="flex items-center justify-center gap-1" aria-label="Pagination">
    <button class="btn-ghost px-3 py-2" :disabled="meta.current_page === 1" @click="go(meta.current_page - 1)">
      Prev
    </button>
    <button
      v-for="page in pages"
      :key="page"
      class="btn px-3.5 py-2"
      :class="page === meta.current_page ? 'bg-ink text-white' : 'text-neutral-600 hover:bg-neutral-100'"
      @click="go(page)"
    >
      {{ page }}
    </button>
    <button class="btn-ghost px-3 py-2" :disabled="meta.current_page === meta.last_page" @click="go(meta.current_page + 1)">
      Next
    </button>
  </nav>
</template>
