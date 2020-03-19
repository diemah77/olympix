<template>
<page :width="width">
    <championship-nav class="mb-6" :championship="championship"></championship-nav>

    <box>
        <transition name="fade" mode="out-in">
			<slot />
		</transition>
    </box>
</page>
</template>

<script>
import ChampionshipNav from '@/views/partials/championship-nav'

export default {
    props : {
        championship: {
            type: Object,
            required: true
        },

        width: {
            type: String,
            required: false,
            default: 'full',
            validator: function (value) {
                return ['full', 'narrow', 'default'].indexOf(value) !== -1
            }
        }
    },

    components: {
        ChampionshipNav
    },

    computed: {
        classes()
        {

        }
    }
}
</script>
