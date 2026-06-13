<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useToastStore } from '../stores/toast'

const auth = useAuthStore()
const toast = useToastStore()
const router = useRouter()
const menuOpen = ref(false)

async function logout() {
  await auth.logout()
  toast.success('You have been logged out.')
  menuOpen.value = false
  router.push({ name: 'jobs' })
}
</script>

<template>
  <header class="sticky top-0 z-40 border-b border-neutral-200 bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
      <RouterLink :to="{ name: 'jobs' }" class="flex items-center gap-2">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-500 font-bold text-white">A</span>
        <span class="text-lg font-bold tracking-tight text-ink">Accountant<span class="text-brand-500">Hub</span></span>
      </RouterLink>

      <nav class="flex items-center gap-1 sm:gap-2">
        <RouterLink :to="{ name: 'jobs' }" class="btn-ghost hidden sm:inline-flex" active-class="text-ink bg-neutral-100">
          Browse Jobs
        </RouterLink>
        <RouterLink :to="{ name: 'categories' }" class="btn-ghost hidden sm:inline-flex" active-class="text-ink bg-neutral-100">
          Categories
        </RouterLink>

        <template v-if="auth.isAuthenticated">
          <RouterLink :to="{ name: 'dashboard' }" class="btn-ghost" active-class="text-ink bg-neutral-100">
            My Bids
          </RouterLink>
          <div class="relative">
            <button class="btn-outline gap-2" @click="menuOpen = !menuOpen">
              <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white">
                {{ auth.user?.name?.charAt(0) }}
              </span>
              <span class="hidden max-w-[8rem] truncate sm:inline">{{ auth.user?.name }}</span>
            </button>
            <div v-if="menuOpen" class="absolute right-0 mt-2 w-48 rounded-lg border border-neutral-200 bg-white py-1 shadow-lg" @click="menuOpen = false">
              <div class="border-b border-neutral-100 px-4 py-2">
                <p class="truncate text-sm font-semibold text-ink">{{ auth.user?.name }}</p>
                <p class="truncate text-xs text-neutral-500">{{ auth.user?.email }}</p>
              </div>
              <button class="block w-full px-4 py-2 text-left text-sm text-neutral-700 hover:bg-neutral-50" @click="logout">
                Log out
              </button>
            </div>
          </div>
        </template>

        <template v-else>
          <RouterLink :to="{ name: 'login' }" class="btn-ghost">Log in</RouterLink>
          <RouterLink :to="{ name: 'register' }" class="btn-primary">Sign up</RouterLink>
        </template>
      </nav>
    </div>
  </header>
</template>
