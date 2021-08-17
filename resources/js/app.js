import { createApp, h } from 'vue'
import { App, plugin } from '@inertiajs/inertia-vue3'
import { Quasar, Dialog } from 'quasar'
import 'quasar/dist/quasar.prod.css'

const el = document.getElementById('app')

createApp({
  render() {
    return h(App, {
      initialPage: JSON.parse(el.dataset.page),
      resolveComponent: name => import(`./Pages/${name}`).then(module => module.default),
    })
  }
})
  .use(plugin)
  .use(Quasar, {
    plugins: {
      Dialog
    }
  })
  .mount(el)
