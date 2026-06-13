<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { api, toQuery, ApiError } from '../lib/api'
import { useToastStore } from '../stores/toast'
import JobCard from '../components/JobCard.vue'
import Pagination from '../components/Pagination.vue'
import EmptyState from '../components/EmptyState.vue'
import SkeletonCard from '../components/SkeletonCard.vue'

const route = useRoute()
const toast = useToastStore()

const category = ref(null)
const jobs = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const loading = ref(true)
const notFound = ref(false)
const page = ref(1)
const sort = ref('newest')

async function loadCategory() {
  try {
    const data = await api.get(`/categories/${route.params.id}`)
    category.value = data.data
  } catch (e) {
    if (e instanceof ApiError && e.status === 404) notFound.value = true
  }
}

async function loadJobs() {
  loading.value = true
  try {
    const query = toQuery({ category: route.params.id, sort: sort.value, page: page.value })
    const data = await api.get(`/jobs${query}`)
    jobs.value = data.data
    meta.value = data.meta
  } catch {
    toast.error('Could not load jobs for this category.')
  } finally {
    loading.value = false
  }
}

function changePage(p) {
  page.value = p
  loadJobs()
}

function init() {
  page.value = 1
  notFound.value = false
  loadCategory()
  loadJobs()
}

// Re-fetch when navigating between categories without unmounting.
watch(() => route.params.id, init)
onMounted(init)
</script>

<template>
  <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
    <RouterLink :to="{ name: 'categories' }" class="mb-6 inline-flex items-center gap-1.5 text-sm font-medium text-neutral-500 hover:text-ink">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
      All categories
    </RouterLink>

    <EmptyState v-if="notFound" title="Category not found" message="This category may have been removed.">
      <RouterLink :to="{ name: 'categories' }" class="btn-primary mt-4">Browse categories</RouterLink>
    </EmptyState>

    <template v-else>
      <!-- Header -->
      <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
          <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ category?.name || 'Category' }}</h1>
          <p v-if="category?.description" class="mt-1 text-neutral-500">{{ category.description }}</p>
          <p class="mt-1 text-sm text-neutral-400">{{ meta.total }} job{{ meta.total === 1 ? '' : 's' }} in this category</p>
        </div>
        <select v-model="sort" class="input w-auto" @change="changePage(1)">
          <option value="newest">Newest first</option>
          <option value="budget_high">Highest budget</option>
          <option value="budget_low">Lowest budget</option>
          <option value="deadline">Deadline (soonest)</option>
        </select>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <SkeletonCard v-for="n in 6" :key="n" />
      </div>

      <!-- Jobs -->
      <div v-else-if="jobs.length" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <JobCard v-for="job in jobs" :key="job.id" :job="job" />
      </div>

      <EmptyState v-else title="No jobs in this category yet" message="Check back later or browse other categories.">
        <RouterLink :to="{ name: 'jobs' }" class="btn-primary mt-4">Browse all jobs</RouterLink>
      </EmptyState>

      <div class="mt-10">
        <Pagination :meta="meta" @change="changePage" />
      </div>
    </template>
  </div>
</template>
