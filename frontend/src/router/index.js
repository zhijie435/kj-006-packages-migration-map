import { createRouter, createWebHistory } from 'vue-router'
import MoqDirectShip from '@/views/MoqDirectShip/index.vue'

const routes = [
  {
    path: '/',
    redirect: '/moq-direct-ship',
  },
  {
    path: '/moq-direct-ship',
    name: 'MoqDirectShip',
    component: MoqDirectShip,
    meta: {
      title: 'P1 金路径二 - 国内小批量 MOQ 直发',
    },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  document.title = to.meta.title || 'MOQ直发管理'
  next()
})

export default router
