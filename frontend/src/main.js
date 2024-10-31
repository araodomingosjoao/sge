sessionStorage.setItem("data-preloader", "enable");
document.documentElement.setAttribute("data-preloader", "enable");

import { createApp } from 'vue'
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue'
import './config/vee-validate';

const app = createApp(App)
const pinia = createPinia();

window.addEventListener("load", () => {
    const preloader = document.getElementById("preloader");
    if (preloader) {
        preloader.style.opacity = "0";
        preloader.style.visibility = "hidden";
    }
});

app.use(pinia)
app.use(router)

app.mount('#app')