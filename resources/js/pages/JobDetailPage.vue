<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { api, ApiError } from '../lib/api'
import { useAuthStore } from '../stores/auth'
import { budgetRange, formatDate, timeAgo } from '../lib/format'
import StatusBadge from '../components/StatusBadge.vue'
import BidModal from '../components/BidModal.vue'
import EmptyState from '../components/EmptyState.vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const job = ref(null)
const loading = ref(true)
const notFound = ref(false)
const showBidModal = ref(false)

const canBid = computed(() => job.value?.status === 'open' && !job.value?.has_bid)

async function load() {
  loading.value = true
  notFound.value = false
  try {
    const data = await api.get(`/jobs/${route.params.id}`)
    job.value = data.data
  } catch (e) {
    if (e instanceof ApiError && e.status === 404) notFound.value = true
  } finally {
    loading.value = false
  }
}

function onApplyClick() {
  if (!auth.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  showBidModal.value = true
}

function onSubmitted() {
  showBidModal.value = false
  load() // refresh bid count + has_bid flag
}

onMounted(load)
</script>

<template>
  <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
    <RouterLink :to="{ name: 'jobs' }" class="mb-6 inline-flex items-center gap-1.5 text-sm font-medium text-neutral-500 hover:text-ink">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
      Back to jobs
    </RouterLink>

    <!-- Loading -->
    <div v-if="loading" class="animate-pulse space-y-4">
      <div class="h-8 w-2/3 rounded bg-neutral-200"></div>
      <div class="h-4 w-1/3 rounded bg-neutral-200"></div>
      <div class="h-40 rounded-xl bg-neutral-200"></div>
    </div>

    <!-- Not found -->
    <EmptyState v-else-if="notFound" title="Job not found" message="This job may have been removed or the link is incorrect.">
      <RouterLink :to="{ name: 'jobs' }" class="btn-primary mt-4">Browse all jobs</RouterLink>
    </EmptyState>

    <!-- Content -->
    <div v-else-if="job" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <!-- Main column -->
      <div class="lg:col-span-2">
        <div class="card p-6">
          <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex rounded-md bg-neutral-100 px-2.5 py-1 text-xs font-medium text-neutral-600">{{ job.category?.name }}</span>
            <StatusBadge :status="job.status" />
            <span class="text-xs text-neutral-400">Posted {{ timeAgo(job.posted_at) }}</span>
          </div>
          <h1 class="mt-3 text-2xl font-bold text-ink">{{ job.title }}</h1>
          <p class="mt-1 font-medium text-neutral-500">{{ job.company_name }}</p>

          <hr class="my-6 border-neutral-100" />

          <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-neutral-400">Job description</h2>
          <div class="prose-sm whitespace-pre-line text-neutral-700">{{ job.description }}</div>

          <template v-if="job.required_skills?.length">
            <h2 class="mb-2 mt-6 text-sm font-semibold uppercase tracking-wide text-neutral-400">Required skills</h2>
            <div class="flex flex-wrap gap-2">
              <span v-for="skill in job.required_skills" :key="skill" class="rounded-full bg-brand-50 px-3 py-1 text-sm font-medium text-brand-700">{{ skill }}</span>
            </div>
          </template>

          <h2 class="mb-2 mt-6 text-sm font-semibold uppercase tracking-wide text-neutral-400">Attachments</h2>
          <div class="rounded-lg border border-dashed border-neutral-300 px-4 py-6 text-center text-sm text-neutral-400">
            No attachments provided for this job.
          </div>

          <template v-if="job.company_about">
            <h2 class="mb-2 mt-6 text-sm font-semibold uppercase tracking-wide text-neutral-400">About the client</h2>
            <p class="text-neutral-700">{{ job.company_about }}</p>
          </template>
        </div>
      </div>

      <!-- Sidebar -->
      <aside class="lg:col-span-1">
        <div class="card sticky top-20 p-6">
          <p class="text-sm text-neutral-500">Budget</p>
          <p class="text-2xl font-bold text-brand-600">{{ budgetRange(job.budget) }}</p>

          <dl class="mt-5 space-y-3 text-sm">
            <div class="flex items-center justify-between">
              <dt class="text-neutral-500">Expected delivery</dt>
              <dd class="font-medium text-ink">{{ job.delivery_days }} days</dd>
            </div>
            <div class="flex items-center justify-between">
              <dt class="text-neutral-500">Deadline</dt>
              <dd class="font-medium text-ink">{{ formatDate(job.deadline) }}</dd>
            </div>
            <div class="flex items-center justify-between">
              <dt class="text-neutral-500">Bids received</dt>
              <dd class="font-medium text-ink">{{ job.bids_count }}</dd>
            </div>
            <div class="flex items-center justify-between">
              <dt class="text-neutral-500">Category</dt>
              <dd>
                <RouterLink
                  v-if="job.category"
                  :to="{ name: 'category-jobs', params: { id: job.category.id } }"
                  class="font-medium text-brand-600 hover:text-brand-700 hover:underline"
                >
                  {{ job.category.name }}
                </RouterLink>
              </dd>
            </div>
          </dl>

          <div class="mt-6">
            <button v-if="canBid" class="btn-primary w-full" @click="onApplyClick">
              Apply / Submit bid
            </button>

            <div v-else-if="job.has_bid" class="rounded-lg bg-brand-50 px-4 py-3 text-center text-sm font-medium text-brand-700">
              ✓ You've already submitted a bid for this job.
            </div>

            <div v-else-if="job.status === 'closed'" class="rounded-lg bg-neutral-100 px-4 py-3 text-center text-sm font-medium text-neutral-500">
              This job is closed and no longer accepting bids.
            </div>

            <p v-if="!auth.isAuthenticated && canBid" class="mt-2 text-center text-xs text-neutral-400">
              You'll need to log in to submit a bid.
            </p>
          </div>
        </div>
      </aside>
    </div>

    <BidModal v-if="showBidModal && job" :job="job" @close="showBidModal = false" @submitted="onSubmitted" />
  </div>
</template>
