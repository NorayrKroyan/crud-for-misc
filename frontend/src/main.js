import { createApp } from 'vue'
import App from './App.vue'
import router from './router/router'

// your project styles
import './styles/base.css'
import './styles/table.css'
import './styles/modal.css'
import 'vue3-select/dist/vue3-select.css'
import './styles/select.css'


// vue3-select base css + your overrides to keep design consistent
import 'vue3-select/dist/vue3-select.css'
import './styles/select.css'

createApp(App).use(router).mount('#app')
