import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/year/:year',
    name: 'year-projects',
    component: () => import('../views/HomeView.vue')
  },
  {
    path: '/project/:id',
    name: 'project-detail',
    component: () => import('../views/ProjectDetailView.vue')
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../views/DashboardView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'profile',
    component: () => import('../views/ProfileView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/project/create',
    name: 'project-create',
    component: () => import('../views/ProjectFormView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/project/edit/:id',
    name: 'project-edit',
    component: () => import('../views/ProjectFormView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
