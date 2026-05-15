import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'

/* TOAST */
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.use(ElementPlus)

/* TOAST CONFIG */
app.use(Toast, {
  position: "bottom-right",

  timeout: 3000,

  closeOnClick: true,

  pauseOnHover: true,

  draggable: true,

  hideProgressBar: false,

  newestOnTop: true,
})

app.mount('#app')