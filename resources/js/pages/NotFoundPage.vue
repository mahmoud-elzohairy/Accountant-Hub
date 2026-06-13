<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
  <div class="mx-auto flex max-w-md flex-col items-center justify-center px-4 py-24 text-center">
    <p class="text-6xl font-bold text-brand-500">404</p>
    <h1 class="mt-4 text-2xl font-bold text-ink">Page not found</h1>
    <p class="mt-2 text-neutral-500">The page you're looking for doesn't exist or has moved.</p>
    <RouterLink :to="{ name: 'jobs' }" class="btn-primary mt-6">Back to jobs</RouterLink>
  </div>
</template>
