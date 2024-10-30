import { createApp } from 'vue'
import router from './router';
import App from './App.vue'
import './config/vee-validate';
createApp(App).use(router).mount('#app')
