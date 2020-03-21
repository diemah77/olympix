<template>
<div class="overflow-x-auto text-sm">
    <div class="flex">
        <brackets-round
            v-for="(round, index) in winnerRounds"
            :key="round.id"
            :is-draw="isDraw"
            :round="round"
            :round-index="index">
        </brackets-round>
    </div>

    <div class="flex mt-8" v-if="loserRounds.length && !isDraw">
        <brackets-round
            v-for="(round, index) in loserRounds"
            :key="round.id"
            :is-draw="isDraw"
            :round="round"
            :round-index="index">
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
        isDraw: {
            type: Boolean,
			required: false,
			default: false
        },

		rounds: {
			type: Array,
			required: true
		},

		double: {
			type: Boolean,
			required: false,
			default: false
		}
	},

	computed: {
        colWidth()
        {
            return this.isDraw ? '300px' : '250px'
        },

		winnerRounds()
		{
            const rounds = this.rounds.filter(r => r.isWinner)

			return this.isDraw ? [rounds[0]] : rounds
		},

		loserRounds()
		{
			return this.rounds.filter(r => !r.isWinner)
		}
	}
}
</script>
