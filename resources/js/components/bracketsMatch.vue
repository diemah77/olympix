<template>
<div class="flex mb-6">
    <div class="w-8 mr-2 flex flex-col justify-around text-right text-gray-300 font-bold">
        <template v-if="playerNumberVisible">
            <div>{{ 2 * index + 1 }}</div>
            <div>{{ 2 * index + 2 }}</div>
        </template>
    </div>

    <div class="shadow-md rounded flex-1">
        <div class="flex justify-between items-center bg-gray-100 p-2 rounded-t border-l-4 border-blue-600"
            :class="{'text-center': match.p1.isBye, 'font-bold': match.winner == 1 && !isDraw, 'text-gray-600': match.winner == 2 && !isDraw}">

            <span v-html="match.p1.fullname"></span>

            <template v-if="!isDraw">
                <icon v-if="match.result.left == 'check'" class="text-green-400" icon="check"></icon>
                <span :title="match.result.sets" v-else v-html="match.result.left"></span>
            </template>
        </div>

        <div class="flex justify-between items-center p-2 rounded-b border-l-4 border-orange-400"
            :class="{'text-center': match.p2.isBye, 'font-bold': match.winner == 2 && !isDraw, 'text-gray-600': match.winner == 1 && !isDraw}">

            <span v-html="match.p2.fullname"></span>

            <template v-if="!isDraw">
                <icon v-if="match.result.right == 'check'" class="text-green-400" icon="check"></icon>
                <span :title="match.result.sets" v-else v-html="match.result.right"></span>
            </template>
        </div>
    </div>
</div>
</template>

<script>
export default {
	props: {
        index: {
            type: Number,
            required: false
        },

        numbersVisible: {
            type: Boolean,
            required: true
        },

        isDraw: {
            type: Boolean,
			required: false,
			default: false
        },

        isWinner: {
            type: Boolean,
			required: false,
			default: true
        },

		match: {
			type: Object,
			required: true
		}
	},

	computed: {
        playerNumberVisible()
        {
            return this.numbersVisible && this.isWinner
        },

        colWidth()
        {
            return this.isDraw && !this.round.isWinner ? '300px' : '250px'
        }
	}
}
</script>
