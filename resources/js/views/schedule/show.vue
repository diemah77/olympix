<template>
<div>
    <div class="flex items-center justify-between mb-6">
        <div class="">
            Spiele: <span class="font-bold">{{ selectedMatches.length }}</span>
        </div>

        <el-checkbox v-model="form.relevant" label="Nur relevante Spiele anzeigen"></el-checkbox>
    </div>

    <el-table
        :data="showedMatches"
        empty-text="Keine Spiele"
        :row-class-name="rowClassName">

        <el-table-column
            type="index"
            width="50">
        </el-table-column>

        <el-table-column
            label="Phase"
            property="phase"
            width="120">
        </el-table-column>

        <el-table-column
            label="Runde/Gruppe"
            property="matchable"
            width="150">
        </el-table-column>

        <el-table-column
            label="Spieler 1"
            property="p1.fullname"
            min-width="180">
        </el-table-column>

        <el-table-column
            label="Spieler 2"
            property="p2.fullname"
            min-width="180">
        </el-table-column>

        <el-table-column
            label="Status"
            property="statusName"
            width="150">

            <status slot-scope="scope" :status="scope.row.statusName"></status>
        </el-table-column>

        <el-table-column
            label="Ergebnis"
            property="result"
            label-class-name="text-center"
            class-name="text-center font-bold">
        </el-table-column>

        <el-table-column>
            <el-button
                v-if="!isDisabled(scope.row)"
                slot-scope="scope"
                type="primary"
                size="small"
                @click="show(scope.row)">
                Bearbeiten
            </el-button>
        </el-table-column>
    </el-table>

    <div class="flex justify-center mt-6" v-if="showedMatches.length < selectedMatches.length && selectedMatches.length > 0">
        <el-button type="primary" @click="showMore">
            <icon class="mr-1" icon="plus" fixed-width></icon>
            Mehr Spiele zeigen
        </el-button>
    </div>

    <match
        :visible.sync="dialogVisible"
        :match="match"
        :tables="tables"
        :results="results"
        @started="reload()"
        @stopped="reload()">
    </match>
</div>
</template>

<script>
import sort from 'fast-sort'
import admin from '@/views/layouts/admin'
import schedule from '@/views/layouts/scheduled-championships'
import Match from '@/components/match'

export default {
    components: {
        Match
    },

    props: {
        championship: Object,
        championships: Array,
        matches: Array,
        tables: Array,
        results: Array,
    },

    layout: (h, page) => {
        return h(admin, {} , [
            h(schedule, {props: {championship: page.data.props.championship}}, [page]),
        ])
    },

    remember: ['form'],

	data()
	{
		return {
            form: {
                relevant: true
            },
            dialogVisible: false,
            match: {},
            showing: 30
		}
	},

    computed: {
        showedMatches()
        {
            return this.selectedMatches.slice(0, this.showing)
        },

        relevants()
        {
            return sort(this.matches.filter(m => m.relevant)).by([
                {desc: m => m.isStarted},
                {asc: m => m.phase},
                {asc: m => m.order}
            ])
        },

        selectedMatches()
        {
            return this.form.relevant ? this.relevants : this.matches
        }
    },

    methods: {
        sort: sort,

        reload()
        {
            this.dialogVisible = false
            this.$inertia.reload()
        },

        showMore()
        {
            this.showing += 20
        },

        rowClassName({row, index})
        {
            if (row.isFinished)
            {
                return 'bg-blue-100'
            }
        },

        resetMatch()
        {
            this.match = {
                isStarted: false,
                statusName: false,
                matchable: '',
                phase: '',
                p1: '',
                p2: '',
                result_id: '',
                table_id: '',
                sets: []
            }

            this.clear()
        },

        show(match)
        {
            this.match = JSON.parse(JSON.stringify(match))
            this.dialogVisible = true
        },

        statusType(statusName)
        {
            // Default: Gestartet
            let type = ''

            if (statusName == 'Beendet') type = 'success'
            if (statusName == 'Vorbereitet') type = 'info'

            return type
        },

        isDisabled(match)
        {
            // return ! match.isRegular
            return !this.relevants.some(m => m.id == match.id)
        }
    }
}
</script>
