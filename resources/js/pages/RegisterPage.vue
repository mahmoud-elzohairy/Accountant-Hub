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

const form = reactive({
  name: '',
  email: '',
  headline: '',
  password: '',
  password_confirmation: '',
})
const errors = ref({})
const submitting = ref(false)

async function submit() {
  submitting.value = true
  errors.value = {}
  try {
    await auth.register(form)
    toast.success('Account created — welcome to Accountant Hub!')
    router.push(route.query.redirect || { name: 'jobs' })
  } catch (e) {
    if (e instanceof ApiError && e.status === 422) errors.value = e.errors
    else toast.error(e.message || 'Registration failed.')
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div class="mx-auto flex max-w-md flex-col justify-center px-4 py-12 sm:py-16">
    <div class="card p-6 sm:p-8">
      <h1 class="text-2xl font-bold text-ink">Create your account</h1>
      <p class="mt-1 text-sm text-neutral-500">Join as an accountant to start bidding on jobs.</p>

      <form class="mt-6 space-y-4" @submit.prevent="submit">
        <div>
          <label class="label" for="name">Full name</label>
          <input id="name" v-model="form.name" type="text" autocomplete="name" class="input" placeholder="Jane Doe" />
          <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name[0] }}</p>
        </div>
        <div>
          <label class="label" for="email">Email</label>
          <input id="email" v-model="form.email" type="email" autocomplete="email" class="input" placeholder="you@example.com" />
          <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email[0] }}</p>
        </div>
        <div>
          <label class="label" for="headline">Professional headline <span class="font-normal text-neutral-400">(optional)</span></label>
          <input id="headline" v-model="form.headline" type="text" class="input" placeholder="e.g. Senior Tax Accountant · 5 yrs" />
          <p v-if="errors.headline" class="mt-1 text-xs text-red-600">{{ errors.headline[0] }}</p>
        </div>
        <div>
          <label class="label" for="password">Password</label>
          <input id="password" v-model="form.password" type="password" autocomplete="new-password" class="input" placeholder="At least 8 characters" />
          <p v-if="errors.password" class="mt-1 text-xs text-red-600">{{ errors.password[0] }}</p>
        </div>
        <div>
          <label class="label" for="password_confirmation">Confirm password</label>
          <input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" class="input" placeholder="Re-enter your password" />
        </div>
        <button type="submit" class="btn-primary w-full" :disabled="submitting">
          {{ submitting ? 'Creating account…' : 'Create account' }}
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-neutral-500">
        Already have an account?
        <RouterLink :to="{ name: 'login' }" class="font-semibold text-brand-600 hover:text-brand-700">Log in</RouterLink>
      </p>
    </div>
  </div>
</template>
