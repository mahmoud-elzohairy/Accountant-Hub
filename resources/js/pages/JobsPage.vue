<script setup>
import { ref, reactive, onMounted } from 'vue'
import { api, toQuery } from '../lib/api'
import { useToastStore } from '../stores/toast'
import JobCard from '../components/JobCard.vue'
import JobFilters from '../components/JobFilters.vue'
import Pagination from '../components/Pagination.vue'
import EmptyState from '../components/EmptyState.vue'
import SkeletonCard from '../components/SkeletonCard.vue'

const toast = useToastStore()

const jobs = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const categories = ref([])
const loading = ref(true)
const page = ref(1)

const filters = reactive({
  search: '',
  category: '',
  budget_min: '',
  budget_max: '',
  status: '',
  sort: 'newest',
})

async function loadJobs() {
  loading.value = true
  try {
    const query = toQuery({ ...filters, page: page.value })
    const data = await api.get(`/jobs${query}`)
    jobs.value = data.data
    meta.value = data.meta
  } catch {
    toast.error('Could not load jobs. Please try again.')
  } finally {
    loading.value = false
  }
}

async function loadCategories() {
  try {
    const data = await api.get('/categories')
    categories.value = data.data
  } catch {
    // non-critical
  }
}

function applyFilters() {
  page.value = 1
  loadJobs()
}

function changePage(p) {
  page.value = p
  loadJobs()
}

onMounted(() => {
  loadCategories()
  loadJobs()
})
</script>

<template>
  <div>
    <!-- Hero -->
    <section class="bg-ink text-white">
      <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-16">
        <p class="mb-2 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-brand-300">
          <span class="h-1.5 w-1.5 rounded-full bg-brand-400" /> {{ meta.total }} accounting jobs available
        </p>
        <h1 class="max-w-2xl text-3xl font-bold leading-tight sm:text-4xl">
          Find accounting work and <span class="text-brand-400">bid with confidence.</span>
        </h1>
        <p class="mt-3 max-w-xl text-neutral-300">
          Browse jobs posted by companies, review the details, and submit a competitive bid in minutes.
        </p>
      </div>
    </section>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
      <JobFilters
        v-model="filters"
        :categories="categories"
        class="-mt-12 relative z-10"
        @apply="applyFilters"
      />

      <!-- Results header -->
      <div class="mb-4 mt-8 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-ink">
          {{ loading ? 'Loading jobs…' : `${meta.total} job${meta.total === 1 ? '' : 's'} found` }}
        </h2>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <SkeletonCard v-for="n in 6" :key="n" />
      </div>

      <!-- Results -->
      <div v-else-if="jobs.length" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <JobCard v-for="job in jobs" :key="job.id" :job="job" />
      </div>

      <!-- Empty -->
      <EmptyState
        v-else
        title="No jobs match your filters"
        message="Try adjusting your search terms or clearing some filters to see more results."
      />

      <div class="mt-10">
        <Pagination :meta="meta" @change="changePage" />
      </div>
    </div>
  </div>
</template>
