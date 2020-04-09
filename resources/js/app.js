
import Vue from 'vue'
import { InertiaApp } from '@inertiajs/inertia-vue'
import axios from 'axios'
import ElementUI from 'element-ui'
import Box from '@/components/box'
import Status from '@/components/status'
import Page from '@/views/partials/page'
import { library } from '@fortawesome/fontawesome-svg-core'
import VueAwesomeSwiper from 'vue-awesome-swiper'

import {
    faAngleDown,
    faAngleUp,
    faBars,
    faCalculator,
    faCheck,
    faChevronDown,
    faChevronLeft,
    faChevronRight,
    faClipboardList,
    faClock,
    faCloudDownloadAlt,
    faCloudUploadAlt,
    faCodeBranch,
    faCogs,
    faEdit,
    faEye,
    faHome,
    faLightbulb,
    faList,
    faLongArrowAltLeft,
    faLongArrowAltRight,
    faPlay,
    faPlus,
    faQuestionCircle,
    faRandom,
    faTable,
    faTimes,
    faTrash,
    faTrophy,
    faUndo,
    faUsers
} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon, FontAwesomeLayers } from '@fortawesome/vue-fontawesome'
library.add(
    faAngleDown,
    faAngleUp,
    faBars,
    faCalculator,
    faCheck,
    faChevronDown,
    faChevronLeft,
    faChevronRight,
    faClipboardList,
    faClock,
    faCloudDownloadAlt,
    faCloudUploadAlt,
    faCodeBranch,
    faCogs,
    faEdit,
    faEye,
    faHome,
    faLightbulb,
    faList,
    faLongArrowAltLeft,
    faLongArrowAltRight,
    faPlay,
    faPlus,
    faQuestionCircle,
    faRandom,
    faTable,
    faTimes,
    faTrash,
    faTrophy,
    faUndo,
    faUsers
)

window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

Vue.use(InertiaApp)
Vue.use(ElementUI, {size: 'medium'})
Vue.use(VueAwesomeSwiper)

Vue.component('box', Box)
Vue.component('page', Page)
Vue.component('status', Status)
Vue.component('icon', FontAwesomeIcon)
Vue.component('icon-layers', FontAwesomeLayers)

Vue.config.productionTip = false

const app = document.getElementById('app')
const data = JSON.parse(app.dataset.page)

Vue.mixin({
    methods: {
        route: route
    }
})

new Vue({
    render: h => h(InertiaApp, {
        props: {
            initialPage: data,
            resolveComponent: name => require(`@/views/${name}`).default
        },
    }),
}).$mount(app)
