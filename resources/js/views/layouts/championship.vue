<template>
<page :title="championship.mode == 'create' ? 'Spielklasse erstellen' : championship.name" width="narrow">
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
