<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'

const props = defineProps({
  text: { type: String, default: '' },
  // Number of lines to show when collapsed
  clamp: { type: Number, default: 3 },
})

const expanded = ref(false)
const overflowing = ref(false)
const el = ref(null)

const clampStyle = computed(() =>
  expanded.value
    ? { whiteSpace: 'pre-line' }
    : {
        display: '-webkit-box',
        WebkitBoxOrient: 'vertical',
        WebkitLineClamp: String(props.clamp),
        overflow: 'hidden',
      },
)

function measure() {
  const node = el.value
  if (!node) return
  // Temporarily ensure clamped state to measure true overflow
  const wasExpanded = expanded.value
  if (wasExpanded) {
    overflowing.value = true
    return
  }
  overflowing.value = node.scrollHeight - node.clientHeight > 1
}

function toggle() {
  expanded.value = !expanded.value
  if (!expanded.value) nextTick(measure)
}

onMounted(() => {
  nextTick(measure)
  window.addEventListener('resize', measure)
})

onBeforeUnmount(() => window.removeEventListener('resize', measure))

watch(() => props.text, () => nextTick(measure))
</script>

<template>
  <div>
    <p
      ref="el"
      class="break-words text-sm leading-relaxed text-neutral-600"
      :style="clampStyle"
    >{{ text }}</p>
    <button
      v-if="overflowing || expanded"
      type="button"
      class="mt-1 text-xs font-semibold text-brand-600 hover:text-brand-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-300 rounded"
      @click="toggle"
    >
      {{ expanded ? 'Show less' : 'Show more' }}
    </button>
  </div>
</template>
