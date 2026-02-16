import { createRouter, createWebHistory } from 'vue-router'
import MiscChargesPage from '../pages/MiscChargesPage.vue'

const routes = [
    { path: '/', redirect: '/misc-charges' },
    { path: '/misc-charges', name: 'misc-charges', component: MiscChargesPage },
]

export default createRouter({
    history: createWebHistory(),
    routes,
})
