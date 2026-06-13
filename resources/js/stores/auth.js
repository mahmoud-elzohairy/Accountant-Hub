import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api, setToken, getToken } from '../lib/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(getToken())
  const ready = ref(false) // becomes true once the initial session check finishes

  const isAuthenticated = computed(() => !!user.value)

  async function register(payload) {
    const data = await api.post('/register', payload)
    applySession(data)
  }

  async function login(payload) {
    const data = await api.post('/login', payload)
    applySession(data)
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch {
      // ignore network/token errors — we clear locally regardless
    }
    clearSession()
  }

  /**
   * On app boot, if we have a token, fetch the current user to confirm
   * the session is still valid.
   */
  async function fetchUser() {
    if (!token.value) {
      ready.value = true
      return
    }
    try {
      const data = await api.get('/user')
      user.value = data.data
    } catch {
      clearSession()
    } finally {
      ready.value = true
    }
  }

  function applySession(data) {
    user.value = data.user.data ?? data.user
    token.value = data.token
    setToken(data.token)
  }

  function clearSession() {
    user.value = null
    token.value = null
    setToken(null)
  }

  return { user, token, ready, isAuthenticated, register, login, logout, fetchUser }
})
