<script setup>
import { ref, onMounted } from 'vue'
import { api } from '../lib/api'
import { useToastStore } from '../stores/toast'
import CategoryCard from '../components/CategoryCard.vue'
import EmptyState from '../components/EmptyState.vue'

const toast = useToastStore()
const categories = ref([])
const loading = ref(true)

async function load() {
  loading.value = true
  try {
    const data = await api.get('/categories')
    categories.value = data.data
  } catch {
    toast.error('Could not load categories.')
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <div>
    <section class="bg-ink text-white">
      <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-14">
        <h1 class="text-3xl font-bold sm:text-4xl">Browse by <span class="text-brand-400">category</span></h1>
        <p class="mt-3 max-w-xl text-neutral-300">
          Explore accounting jobs grouped by specialty — pick a category to see every job in it.
        </p>
      </div>
    </section>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
      <div v-if="loading" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="n in 6" :key="n" class="card h-40 animate-pulse"></div>
      </div>

      <div v-else-if="categories.length" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <CategoryCard v-for="c in categories" :key="c.id" :category="c" />
      </div>

      <EmptyState v-else title="No categories yet" message="Categories will appear here once jobs are posted." />
    </div>
  </div>
</template>
