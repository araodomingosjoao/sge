sessionStorage.setItem("data-preloader", "enable");
document.documentElement.setAttribute("data-preloader", "enable");

import { createApp } from 'vue'
import router from './router';
import App from './App.vue'
import './config/vee-validate';


window.addEventListener("load", () => {
    const preloader = document.getElementById("preloader");
    if (preloader) {
        preloader.style.opacity = "0";
        preloader.style.visibility = "hidden";
    }
});
createApp(App).use(router).mount('#app')
