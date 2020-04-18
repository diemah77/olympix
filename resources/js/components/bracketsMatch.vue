<template>
<div class="flex mb-6">
    <div v-if="playerNumberVisible" class="w-8 mr-2 flex flex-col justify-around text-right text-gray-300 font-bold">
        <template>
            <div>{{ 2 * index + 1 }}</div>
            <div>{{ 2 * index + 2 }}</div>
        </template>
    </div>

    <div class="shadow-md rounded flex-1 relative group">
        <div v-if="match.winner == 0 && match.isRegular" class="transition duration-200 ease-in-out rounded absolute flex items-center justify-center top-0 left-0 opacity-0 bg-black-40 group-hover:opacity-100 w-full h-full z-50">
            <el-button type="primary" class="shadow" circle @click="matchVisible = true">
                <icon icon="pen"></icon>
            </el-button>
        </div>

        <div class="flex justify-between items-center bg-gray-100 p-2 rounded-t border-l-4 border-blue-600"
            :class="{'text-center': match.p1.isBye, 'font-bold': match.winner == 1 && !isDraw, 'text-gray-600': match.winner == 2 && !isDraw}">

            <span v-html="match.p1.fullname"></span>

            <template v-if="!isDraw">
                <icon v-if="match.result.left == 'check'" class="text-green-400" icon="check"></icon>
                <span v-else v-html="match.result.left"></span>
            </template>
        </div>

        <template v-if="!isDraw">
            <div v-if="match.winner" class="flex text-xs justify-end border-l-4 border-gray-400 font-bold p-1">
                <span class="mr-1" v-for="set in match.result.sets" :key="set.id">{{ set.points }}</span>
            </div>

            <div v-else-if="match.isStarted" class="flex text-xs items-center justify-end border-l-4 border-green-400 bg-teal-100 text-green-700 p-1">
                <status-pulse color="green" :size="2"></status-pulse>
                <span class="ml-1">läuft</span>
            </div>

            <div v-else class="flex text-xs justify-end border-l-4 border-gray-400 text-white p-1">
                <span class="mx-1">vorbereitet</span>
            </div>
        </template>

        <div class="flex justify-between items-center p-2 rounded-b border-l-4 border-orange-400"
            :class="{'text-center': match.p2.isBye, 'font-bold': match.winner == 2 && !isDraw, 'text-gray-600': match.winner == 1 && !isDraw}">

            <span v-html="match.p2.fullname"></span>

            <template v-if="!isDraw">
                <icon v-if="match.result.right == 'check'" class="text-green-400" icon="check"></icon>
                <span v-else v-html="match.result.right"></span>
            </template>
        </div>
    </div>

    <match
        :visible.sync="matchVisible"
        :match="match"
        :tables="tables"
        :results="results"
        @started="reload()"
        @stopped="reload()">
    </match>
</div>
</template>

<script>
import StatusPulse from '@/components/status-pulse'
import Match from '@/components/match'

export default {
    components: {
        Match,
        StatusPulse
    },

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

    data()
    {
        return {
            matchVisible: false
        }
    },

    methods: {
        reload()
        {
            this.matchVisible = false
            this.$inertia.reload()
        }
    },

	computed: {
        tables()
        {
            if (! this.$page.tables)
            {
                return []
            }

            return this.$page.tables.filter(t => !t.busy || t.id == this.match.table_id)
        },

        results()
        {
            if (! this.$page.results)
            {
                return []
            }

            return this.$page.results.filter(r => r.size == this.match.setsCount)
        },

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
