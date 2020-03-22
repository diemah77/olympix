<template>
<div class="overflow-x-auto text-sm">
    <div class="flex">
        <brackets-round
            :is-draw="true"
            :round="winnerRound"
            :numbers-visible="false"
            :round-index="0">
        </brackets-round>
    </div>
</div>
</template>

<script>
import bracketsRound from '@/components/bracketsRound'

export default {
    components: {
        bracketsRound
    },

	props: {
		rounds: {
			type: Array,
			required: true
		},
	},

	computed: {
		winnerRound()
		{
            return this.rounds.find(r => r.isWinner)
		}
	}
}
</script>
