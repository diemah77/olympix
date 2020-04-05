<template>
<page :title="tournament.mode == 'create' ? 'Turnier erstellen' : tournament.name" :width="width">
    <template slot="tag" v-if="tournament.statusName == 'Gestartet'">
        <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full">Gestartet</span>
    </template>

    <tournament-nav class="mb-6" :tournament="tournament"></tournament-nav>

    <transition name="fade" mode="out-in">
        <slot />
    </transition>
</page>
</template>

<script>
import tournamentNav from '@/views/partials/tournament-nav'

export default {
    props : {
        tournament: {
            type: Object,
            required: true
        },

        width: {
            type: String,
            default: 'full'
        }
    },

    components: {
        tournamentNav
    }
}
</script>
