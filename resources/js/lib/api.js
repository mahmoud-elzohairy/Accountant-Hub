// Lightweight fetch wrapper around the Accountant Hub API.
// Attaches the bearer token, parses JSON, and normalises errors.

const TOKEN_KEY = 'ah_token'

export function getToken() {
  return localStorage.getItem(TOKEN_KEY)
}

export function setToken(token) {
  if (token) localStorage.setItem(TOKEN_KEY, token)
  else localStorage.removeItem(TOKEN_KEY)
}

/**
 * Thrown for any non-2xx response. Carries the HTTP status and the
 * parsed validation `errors` bag (if present) for form display.
 */
export class ApiError extends Error {
  constructor(message, status, errors = {}) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.errors = errors
  }
}

async function request(method, path, body = null) {
  const headers = { Accept: 'application/json' }
  const token = getToken()
  if (token) headers.Authorization = `Bearer ${token}`

  const options = { method, headers }
  if (body !== null) {
    headers['Content-Type'] = 'application/json'
    options.body = JSON.stringify(body)
  }

  const res = await fetch(`/api${path}`, options)

  // 204 No Content
  if (res.status === 204) return null

  const data = await res.json().catch(() => ({}))

  if (!res.ok) {
    throw new ApiError(
      data.message || 'Something went wrong. Please try again.',
      res.status,
      data.errors || {},
    )
  }

  return data
}

export const api = {
  get: (path) => request('GET', path),
  post: (path, body) => request('POST', path, body),
  put: (path, body) => request('PUT', path, body),
  delete: (path) => request('DELETE', path),
}

/**
 * Build a query string from an object, skipping empty values.
 */
export function toQuery(params) {
  const q = new URLSearchParams()
  Object.entries(params).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') {
      q.append(key, value)
    }
  })
  const str = q.toString()
  return str ? `?${str}` : ''
}
