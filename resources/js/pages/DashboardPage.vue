<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { api } from '../lib/api'
import { useAuthStore } from '../stores/auth'
import { useToastStore } from '../stores/toast'
import { money, formatDate, budgetRange } from '../lib/format'
import StatusBadge from '../components/StatusBadge.vue'
import EmptyState from '../components/EmptyState.vue'

const auth = useAuthStore()
const toast = useToastStore()
const bids = ref([])
const loading = ref(true)

async function load() {
  loading.value = true
  try {
    const data = await api.get('/my-bids')
    bids.value = data.data
  } catch {
    toast.error('Could not load your bids.')
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
    <header class="mb-6">
      <h1 class="text-2xl font-bold text-ink">My bids</h1>
      <p class="mt-1 text-sm text-neutral-500">
        Track the jobs you've applied to, {{ auth.user?.name?.split(' ')[0] }}.
      </p>
    </header>

    <!-- Loading -->
    <div v-if="loading" class="space-y-3">
      <div v-for="n in 3" :key="n" class="card h-28 animate-pulse"></div>
    </div>

    <!-- Empty -->
    <EmptyState
      v-else-if="!bids.length"
      title="You haven't placed any bids yet"
      message="Browse the available jobs and submit your first proposal to get started."
    >
      <RouterLink :to="{ name: 'jobs' }" class="btn-primary mt-4">Browse jobs</RouterLink>
    </EmptyState>

    <!-- Bids -->
    <div v-else class="space-y-4">
      <div v-for="bid in bids" :key="bid.id" class="card p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="flex items-center gap-2">
              <span class="inline-flex rounded-md bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-600">{{ bid.job?.category?.name }}</span>
              <StatusBadge v-if="bid.job" :status="bid.job.status" />
            </div>
            <RouterLink
              :to="{ name: 'job-detail', params: { id: bid.job?.id } }"
              class="mt-1.5 block text-base font-semibold text-ink hover:text-brand-600"
            >
              {{ bid.job?.title }}
            </RouterLink>
            <p class="text-sm text-neutral-500">{{ bid.job?.company_name }}</p>
          </div>
          <div class="text-right">
            <p class="text-lg font-bold text-brand-600">{{ money(bid.amount) }}</p>
            <p class="text-xs text-neutral-400">your bid</p>
          </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-x-6 gap-y-1 border-t border-neutral-100 pt-3 text-xs text-neutral-500">
          <span>Delivery in <strong class="text-ink">{{ bid.delivery_days }} days</strong></span>
          <span>Client budget: <strong class="text-ink">{{ budgetRange(bid.job?.budget) }}</strong></span>
          <span>Submitted {{ formatDate(bid.submitted_at) }}</span>
        </div>

        <p class="mt-3 line-clamp-2 text-sm text-neutral-600">{{ bid.cover_letter }}</p>
      </div>
    </div>
  </div>
</template>
