import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'jobs',
    component: () => import('../pages/JobsPage.vue'),
    meta: { title: 'Browse Jobs' },
  },
  {
    path: '/jobs/:id',
    name: 'job-detail',
    component: () => import('../pages/JobDetailPage.vue'),
    meta: { title: 'Job Details' },
  },
  {
    path: '/categories',
    name: 'categories',
    component: () => import('../pages/CategoriesPage.vue'),
    meta: { title: 'Categories' },
  },
  {
    path: '/categories/:id',
    name: 'category-jobs',
    component: () => import('../pages/CategoryJobsPage.vue'),
    meta: { title: 'Category Jobs' },
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../pages/LoginPage.vue'),
    meta: { title: 'Log In', guestOnly: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('../pages/RegisterPage.vue'),
    meta: { title: 'Create Account', guestOnly: true },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../pages/DashboardPage.vue'),
    meta: { title: 'My Bids', requiresAuth: true },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('../pages/NotFoundPage.vue'),
    meta: { title: 'Not Found' },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  // Wait for the initial session check before deciding on guarded routes.
  if (!auth.ready) await auth.fetchUser()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return { name: 'dashboard' }
  }

  return true
})

router.afterEach((to) => {
  document.title = to.meta.title ? `${to.meta.title} · Accountant Hub` : 'Accountant Hub'
})

export default router
