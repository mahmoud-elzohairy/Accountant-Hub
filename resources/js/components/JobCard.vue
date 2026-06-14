<script setup>
import { RouterLink } from 'vue-router'
import StatusBadge from './StatusBadge.vue'
import { budgetRange, formatDate, timeAgo } from '../lib/format'

defineProps({
  job: { type: Object, required: true },
})
</script>

<template>
  <RouterLink
    :to="{ name: 'job-detail', params: { id: job.id } }"
    class="card group flex h-full flex-col p-5 transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md"
  >
    <div class="mb-3 flex items-start justify-between gap-3">
      <span class="inline-flex rounded-md bg-neutral-100 px-2 py-1 text-xs font-medium text-neutral-600">
        {{ job.category?.name }}
      </span>
      <StatusBadge :status="job.status" />
    </div>

    <h3 class="line-clamp-2 min-h-[2.75rem] text-base font-semibold leading-snug text-ink group-hover:text-brand-600">
      {{ job.title }}
    </h3>
    <p class="mt-1 truncate text-sm font-medium text-neutral-500">{{ job.company_name }}</p>

    <p class="mt-3 line-clamp-2 text-sm text-neutral-600">{{ job.short_description }}</p>

    <div class="mt-auto pt-4 flex items-center gap-2 text-sm">
      <span class="font-semibold text-brand-600">{{ budgetRange(job.budget) }}</span>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1.5 border-t border-neutral-100 pt-4 text-xs text-neutral-500">
      <span class="inline-flex items-center gap-1">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0V11.25A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
        Due {{ formatDate(job.deadline) }}
      </span>
      <span class="inline-flex items-center gap-1">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
        {{ job.delivery_days }}d delivery
      </span>
      <span class="inline-flex items-center gap-1">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
        {{ job.bids_count }} {{ job.bids_count === 1 ? 'bid' : 'bids' }}
      </span>
      <span class="ml-auto text-neutral-400">{{ timeAgo(job.posted_at) }}</span>
    </div>
  </RouterLink>
</template>
