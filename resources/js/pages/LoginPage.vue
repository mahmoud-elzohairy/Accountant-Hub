<script setup>
import { reactive, ref } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useToastStore } from '../stores/toast'
import { ApiError } from '../lib/api'

const auth = useAuthStore()
const toast = useToastStore()
const router = useRouter()
const route = useRoute()

const form = reactive({ email: '', password: '' })
const errors = ref({})
const submitting = ref(false)

async function submit() {
  submitting.value = true
  errors.value = {}
  try {
    await auth.login(form)
    toast.success(`Welcome back, ${auth.user.name.split(' ')[0]}!`)
    router.push(route.query.redirect || { name: 'jobs' })
  } catch (e) {
    if (e instanceof ApiError && e.status === 422) errors.value = e.errors
    else toast.error(e.message || 'Login failed.')
  } finally {
    submitting.value = false
  }
}

function fillDemo() {
  form.email = 'accountant@demo.test'
  form.password = 'password'
}
</script>

<template>
  <div class="mx-auto flex max-w-md flex-col justify-center px-4 py-12 sm:py-16">
    <div class="card p-6 sm:p-8">
      <h1 class="text-2xl font-bold text-ink">Welcome back</h1>
      <p class="mt-1 text-sm text-neutral-500">Log in to submit bids and track your applications.</p>

      <form class="mt-6 space-y-4" @submit.prevent="submit">
        <div>
          <label class="label" for="email">Email</label>
          <input id="email" v-model="form.email" type="email" autocomplete="email" class="input" placeholder="you@example.com" />
          <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email[0] }}</p>
        </div>
        <div>
          <label class="label" for="password">Password</label>
          <input id="password" v-model="form.password" type="password" autocomplete="current-password" class="input" placeholder="••••••••" />
          <p v-if="errors.password" class="mt-1 text-xs text-red-600">{{ errors.password[0] }}</p>
        </div>
        <button type="submit" class="btn-primary w-full" :disabled="submitting">
          {{ submitting ? 'Logging in…' : 'Log in' }}
        </button>
      </form>

      <button class="mt-3 w-full text-center text-xs text-neutral-400 hover:text-brand-600" @click="fillDemo">
        Use demo accountant credentials
      </button>

      <p class="mt-6 text-center text-sm text-neutral-500">
        Don't have an account?
        <RouterLink :to="{ name: 'register' }" class="font-semibold text-brand-600 hover:text-brand-700">Sign up</RouterLink>
      </p>
    </div>
  </div>
</template>
