sessionStorage.setItem("data-preloader", "enable");
document.documentElement.setAttribute("data-preloader", "enable");

import { createApp } from 'vue'
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import router from './router';
import App from './App.vue'
import './config/vee-validate';
import { createNotivue } from 'notivue'
import 'notivue/notifications.css'

const app = createApp(App)
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

window.addEventListener("load", () => {
    const preloader = document.getElementById("preloader");
    if (preloader) {
        preloader.style.opacity = "0";
        preloader.style.visibility = "hidden";
    }
});

app.use(createNotivue())

app.use(pinia)
app.use(router)

app.mount('#app')