<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { api, toQuery } from '../lib/api'
import { useAuthStore } from '../stores/auth'
import { useToastStore } from '../stores/toast'
import { money, formatDate, budgetRange } from '../lib/format'
import StatusBadge from '../components/StatusBadge.vue'
import EmptyState from '../components/EmptyState.vue'
import Pagination from '../components/Pagination.vue'
import ExpandableText from '../components/ExpandableText.vue'

const auth = useAuthStore()
const toast = useToastStore()
const bids = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const loading = ref(true)
const page = ref(1)

async function load() {
  loading.value = true
  try {
    const data = await api.get(`/my-bids${toQuery({ page: page.value })}`)
    bids.value = data.data
    meta.value = data.meta
  } catch {
    toast.error('Could not load your bids.')
  } finally {
    loading.value = false
  }
}

function changePage(p) {
  page.value = p
  load()
}

onMounted(load)
</script>

<template>
  <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
    <header class="mb-6">
      <h1 class="text-2xl font-bold text-ink">My bids</h1>
      <p class="mt-1 text-sm text-neutral-500">
        Track the jobs you've applied to, {{ auth.user?.name?.split(' ')[0] }}.<span v-if="meta.total"> You've placed {{ meta.total }} bid{{ meta.total === 1 ? '' : 's' }}.</span>
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

        <div class="mt-4 space-y-3">
          <div v-if="bid.cover_letter">
            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-neutral-400">Cover letter</p>
            <ExpandableText :text="bid.cover_letter" :clamp="3" />
          </div>
          <div v-if="bid.experience_summary">
            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-neutral-400">Experience summary</p>
            <ExpandableText :text="bid.experience_summary" :clamp="3" />
          </div>
        </div>
      </div>

      <div class="pt-4">
        <Pagination :meta="meta" @change="changePage" />
      </div>
    </div>
  </div>
</template>
