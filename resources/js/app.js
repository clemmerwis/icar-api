require('./bootstrap');

// Import modules...
import { createApp, h } from 'vue';

import Test from './Components/Test.vue'

const app = createApp({})

app.component('test', Test)

app.mount('#app')