<template>
<div class="overflow-x-auto text-sm">
    <div class="flex">
        <div v-for="(round, roundIndex) in winnerRounds" class="flex flex-col mr-8" :style="{'min-width': colWidth}" :key="round.id">
            <div class="mb-auto" v-if="!isDraw">
                <h1 class="py-3 pr-8 text-gray-800 text-center text-base font-bold">
                    {{ round.name }}
                </h1>
            </div>

            <div class="flex-1 flex flex-col justify-around mt-8">
                <div
                    class="flex mb-6"
                    v-for="(match, index) in round.matches"
                    :key="match.id">

                    <div class="w-8 mr-2 flex flex-col justify-around text-right text-gray-300 font-bold" v-if="roundIndex == 0">
                        <div>{{ 2 * index + 1 }}</div>

                        <div>{{ 2 * index + 2 }}</div>
                    </div>

                    <div class="shadow-md rounded flex-1">
                        <div :class="['flex justify-between items-center bg-gray-100 p-2 rounded-t border-l-4 border-blue-600', {'text-center': match.p1.isBye, 'font-bold': match.winner == 1 && !isDraw, 'text-gray-600': match.winner == 2} ]">
                            <div class="flex">
                                <!-- <span class="w-6 text-center text-gray-600 mr-1 ml-1" v-html="match.p1.seed"></span> -->
                                <span v-html="match.p1.fullname"></span>
                            </div>

                            <template v-if="!isDraw">
                                <icon v-if="match.result.left == 'check'" class="text-green-400" icon="check"></icon>
                                <span :title="match.result.sets" v-else v-html="match.result.left"></span>
                            </template>
                        </div>

                        <div :class="['flex justify-between items-center p-2 rounded-b border-l-4 border-orange-400', {'text-center': match.p2.isBye, 'font-bold': match.winner == 2 && !isDraw, 'text-gray-600': match.winner == 1} ]">
                            <div class="flex">
                                <!-- <span class="w-6 text-center text-gray-600 mr-1 ml-1" v-html="match.p2.seed"></span> -->
                                <span v-html="match.p2.fullname"></span>
                            </div>

                            <template v-if="!isDraw">
                                <icon v-if="match.result.right == 'check'" class="text-green-400" icon="check"></icon>
                                <span :title="match.result.sets" v-else v-html="match.result.right"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="loserRounds.length && !isDraw" class="flex mt-8">
        <div v-for="round in loserRounds" class="flex flex-col" style="min-width: 250px;" :key="round.id">
            <div class="mb-auto">
                <h1 class="py-3 pr-8 text-gray-800 text-center text-sm font-normal border-b border-t border-gray-300 mb-8">
                    {{ round.name }}
                </h1>
            </div>

            <div class="flex-1 flex flex-col justify-around">
                <div v-for="match in round.matches" class="shadow-md mb-8 mr-8 rounded" :key="match.id">
                    <div :class="['flex justify-between items-center bg-gray-100 py-2 pr-2 rounded-t border-l-4 border-blue-600', {'text-center': match.p1.isBye, 'font-bold': match.winner == 1 && !isDraw, 'text-gray-600': match.winner == 2} ]">
                        <div class="flex">
                            <span class="w-6 text-center text-gray-600 mr-1 ml-1" v-html="match.p1.seed"></span>
                            <span v-html="match.p1.fullname"></span>
                        </div>

                        <template v-if="!isDraw">
                            <icon v-if="match.result.left == 'check'" class="text-green-400" icon="check"></icon>
                            <span :title="match.result.sets" v-else v-html="match.result.left"></span>
                        </template>
                    </div>

                    <div :class="['flex justify-between items-center py-2 pr-2 rounded-b border-l-4 border-orange-400', {'text-center': match.p2.isBye, 'font-bold': match.winner == 2 && !isDraw, 'text-gray-600': match.winner == 1} ]">
                        <div class="flex">
                            <span class="w-6 text-center text-gray-600 mr-1 ml-1" v-html="match.p2.seed"></span>
                            <span v-html="match.p2.fullname"></span>
                        </div>

                        <template v-if="!isDraw">
                            <icon v-if="match.result.right == 'check'" class="text-green-400" icon="check"></icon>
                            <span :title="match.result.sets" v-else v-html="match.result.right"></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
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
