<template>
<div class="flex flex-col w-1/4 pr-8">
    <div class="mb-auto" v-if="!round.isWinner">
        <h1 class="py-3 mb-6 text-gray-800 text-center text-base font-bold">
            {{ round.name }}
        </h1>
    </div>

    <div class="flex-1 flex flex-col justify-around">
        <brackets-match
            v-for="(match, matchIndex) in round.matches"
            :key="match.id"
            :match="match"
            :numbers-visible="numbersVisible"
            :index="matchIndex"
            :is-draw="isDraw"
            :is-winner="round.isWinner">
        </brackets-match>
    </div>
</div>
</template>

<script>
import bracketsMatch from '@/components/bracketsMatch'

export default {
    components: {
        bracketsMatch
    },

	props: {
        numbersVisible: {
            type: Boolean,
            required: true
        },

        isDraw: {
            type: Boolean,
			required: false,
			default: false
        },

		round: {
			type: Object,
			required: true
		}
	},

	computed: {
        colWidth()
        {
            return this.isDraw && !this.round.isWinner ? '300px' : '250px'
        }
	}
}
</script>
