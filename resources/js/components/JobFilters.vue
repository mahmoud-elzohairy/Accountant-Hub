<script setup>
import { reactive, watch, ref } from 'vue'

const props = defineProps({
  categories: { type: Array, default: () => [] },
  modelValue: { type: Object, required: true },
})
const emit = defineEmits(['update:modelValue', 'apply'])

const local = reactive({ ...props.modelValue })

// Keep local copy in sync if the parent resets filters.
watch(
  () => props.modelValue,
  (val) => Object.assign(local, val),
)

let searchTimer = null
function onSearchInput() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(emitChange, 350)
}

function emitChange() {
  emit('update:modelValue', { ...local })
  emit('apply')
}

function reset() {
  local.search = ''
  local.category = ''
  local.budget_min = ''
  local.budget_max = ''
  local.status = ''
  local.sort = 'newest'
  emitChange()
}

const showAdvanced = ref(false)
</script>

<template>
  <div class="card p-4 sm:p-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
      <div class="relative flex-1">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
        <input
          v-model="local.search"
          type="search"
          placeholder="Search by job title…"
          class="input pl-9"
          @input="onSearchInput"
        />
      </div>

      <select v-model="local.sort" class="input sm:w-48" @change="emitChange">
        <option value="newest">Newest first</option>
        <option value="budget_high">Highest budget</option>
        <option value="budget_low">Lowest budget</option>
        <option value="deadline">Deadline (soonest)</option>
      </select>

      <button class="btn-outline sm:w-auto" @click="showAdvanced = !showAdvanced">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
        Filters
      </button>
    </div>

    <div v-show="showAdvanced" class="mt-4 grid grid-cols-1 gap-4 border-t border-neutral-100 pt-4 sm:grid-cols-4">
      <div>
        <label class="label">Category</label>
        <select v-model="local.category" class="input" @change="emitChange">
          <option value="">All categories</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }} ({{ c.jobs_count }})</option>
        </select>
      </div>
      <div>
        <label class="label">Min budget</label>
        <input v-model="local.budget_min" type="number" min="0" placeholder="0" class="input" @change="emitChange" />
      </div>
      <div>
        <label class="label">Max budget</label>
        <input v-model="local.budget_max" type="number" min="0" placeholder="Any" class="input" @change="emitChange" />
      </div>
      <div>
        <label class="label">Status</label>
        <select v-model="local.status" class="input" @change="emitChange">
          <option value="">All</option>
          <option value="open">Open</option>
          <option value="closed">Closed</option>
        </select>
      </div>
      <div class="sm:col-span-4">
        <button class="btn-ghost px-0 text-brand-600 hover:bg-transparent hover:text-brand-700" @click="reset">
          Clear all filters
        </button>
      </div>
    </div>
  </div>
</template>
