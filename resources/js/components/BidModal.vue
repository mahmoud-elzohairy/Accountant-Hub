<script setup>
import { reactive, ref } from 'vue'
import { api, ApiError } from '../lib/api'
import { useToastStore } from '../stores/toast'
import { budgetRange } from '../lib/format'
import BaseModal from './BaseModal.vue'

const props = defineProps({
  job: { type: Object, required: true },
})
const emit = defineEmits(['close', 'submitted'])

const toast = useToastStore()
const submitting = ref(false)
const errors = ref({})

const form = reactive({
  amount: '',
  delivery_days: '',
  cover_letter: '',
  experience_summary: '',
})

async function submit() {
  submitting.value = true
  errors.value = {}
  try {
    await api.post(`/jobs/${props.job.id}/bids`, {
      amount: Number(form.amount),
      delivery_days: Number(form.delivery_days),
      cover_letter: form.cover_letter,
      experience_summary: form.experience_summary,
    })
    toast.success('Your bid has been submitted successfully!')
    emit('submitted')
  } catch (e) {
    if (e instanceof ApiError && e.status === 422) {
      errors.value = e.errors
      toast.error('Please fix the highlighted fields.')
    } else if (e instanceof ApiError && e.status === 409) {
      toast.error(e.message)
      emit('submitted') // already bid — refresh state
    } else {
      toast.error(e.message || 'Could not submit your bid.')
    }
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <BaseModal title="Submit your bid" @close="emit('close')">
    <div class="mb-5 rounded-lg bg-neutral-50 p-3 text-sm">
      <p class="font-semibold text-ink">{{ job.title }}</p>
      <p class="mt-0.5 text-neutral-500">
        Client budget: <span class="font-medium text-brand-600">{{ budgetRange(job.budget) }}</span>
      </p>
    </div>

    <form class="space-y-4" @submit.prevent="submit">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label class="label" for="amount">Proposed price (USD)</label>
          <input
            id="amount"
            v-model="form.amount"
            type="number"
            :min="job.budget?.min"
            :max="job.budget?.max"
            class="input"
            :placeholder="`e.g. ${job.budget?.min}`"
          />
          <p v-if="errors.amount" class="mt-1 text-xs text-red-600">{{ errors.amount[0] }}</p>
          <p v-else class="mt-1 text-xs text-neutral-400">Must be within {{ budgetRange(job.budget) }}.</p>
        </div>
        <div>
          <label class="label" for="delivery">Estimated delivery (days)</label>
          <input id="delivery" v-model="form.delivery_days" type="number" min="1" class="input" placeholder="e.g. 14" />
          <p v-if="errors.delivery_days" class="mt-1 text-xs text-red-600">{{ errors.delivery_days[0] }}</p>
        </div>
      </div>

      <div>
        <label class="label" for="cover">Cover letter / proposal</label>
        <textarea id="cover" v-model="form.cover_letter" rows="4" class="input" placeholder="Explain your approach and why you're a great fit for this job…" />
        <p v-if="errors.cover_letter" class="mt-1 text-xs text-red-600">{{ errors.cover_letter[0] }}</p>
      </div>

      <div>
        <label class="label" for="experience">Relevant experience</label>
        <textarea id="experience" v-model="form.experience_summary" rows="3" class="input" placeholder="Summarise relevant experience that qualifies you for this work…" />
        <p v-if="errors.experience_summary" class="mt-1 text-xs text-red-600">{{ errors.experience_summary[0] }}</p>
      </div>

      <div class="flex items-center justify-end gap-2 pt-2">
        <button type="button" class="btn-outline" @click="emit('close')">Cancel</button>
        <button type="submit" class="btn-primary" :disabled="submitting">
          {{ submitting ? 'Submitting…' : 'Submit bid' }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>
