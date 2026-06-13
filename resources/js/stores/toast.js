import { defineStore } from 'pinia'
import { ref } from 'vue'

let counter = 0

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([])

  function push(message, type = 'success') {
    const id = ++counter
    toasts.value.push({ id, message, type })
    setTimeout(() => dismiss(id), 4000)
  }

  function dismiss(id) {
    toasts.value = toasts.value.filter((t) => t.id !== id)
  }

  const success = (msg) => push(msg, 'success')
  const error = (msg) => push(msg, 'error')

  return { toasts, push, dismiss, success, error }
})
