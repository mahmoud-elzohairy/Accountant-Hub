<script setup>
import { RouterLink } from 'vue-router'

defineProps({
  category: { type: Object, required: true },
})
</script>

<template>
  <RouterLink
    :to="{ name: 'category-jobs', params: { id: category.id } }"
    class="card group flex h-full flex-col p-5 transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md"
  >
    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>
    </div>
    <h3 class="text-base font-semibold text-ink group-hover:text-brand-600">{{ category.name }}</h3>
    <p v-if="category.description" class="mt-1 line-clamp-2 text-sm text-neutral-500">{{ category.description }}</p>
    <div class="mt-4 flex items-center gap-1.5 border-t border-neutral-100 pt-3 text-sm font-medium text-neutral-600">
      <span class="text-brand-600">{{ category.jobs_count ?? 0 }}</span>
      open {{ (category.jobs_count ?? 0) === 1 ? 'job' : 'jobs' }}
      <svg class="ml-auto h-4 w-4 text-neutral-400 transition group-hover:translate-x-0.5 group-hover:text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
    </div>
  </RouterLink>
</template>
