<template>
<page :title="championship.mode == 'create' ? 'Spielklasse erstellen' : championship.name" width="narrow">
    <template slot="tag" v-if="championship.statusName == 'Gestartet'">
        <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full">Gestartet</span>
    </template>

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
        }
    },

    components: {
        ChampionshipNav
    }
}
</script>
