<template>
<box>
    <div class="flex items-center justify-between mb-6">
        <div class="">
            Spiele: <span class="font-bold">{{ selectedMatches.length }} von {{ matches.length }}</span>
        </div>

        <el-checkbox v-model="form.running" label="Nur laufende Spiele anzeigen"></el-checkbox>
    </div>

    <el-table
        :data="selectedMatches"
        empty-text="Keine Spiele"
        :row-class-name="rowClassName">

        <el-table-column
            type="index"
            width="50">
        </el-table-column>

        <el-table-column
            label="Klasse"
            property="championship"
            width="120">
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
            property="p1"
            min-width="180">
        </el-table-column>

        <el-table-column
            label="Spieler 2"
            property="p2"
            min-width="180">
        </el-table-column>

        <el-table-column
            label="Status"
            property="statusName"
            width="150">

            <status slot-scope="scope" :status="scope.row.statusName"></status>
        </el-table-column>

        <el-table-column>
            <el-button
                slot-scope="scope"
                type="primary"
                size="small"
                @click="show(scope.row)">

                Bearbeiten
            </el-button>
        </el-table-column>
    </el-table>

    <div class="flex justify-center mt-6" v-if="selectedMatches.length < matches.length && selectedMatches.length > 0">
        <el-button type="primary" @click="showMore">
            <icon class="mr-1" icon="plus" fixed-width></icon>
            Mehr Spiele zeigen
        </el-button>
    </div>

    <match
        :visible.sync="dialogVisible"
        :match="match"
        :tables="tables"
        :results="championshipResults"
        @started="reload()"
        @stopped="reload()">
    </match>
</box>
</template>

<script>
import sort from 'fast-sort'
import admin from '@/views/layouts/admin'
import schedule from '@/views/layouts/schedule'
import Match from '@/components/match'

export default {
    layout: (h, page) => {
        return h(admin, {} , [
            h(schedule, {}, [page]),
        ])
    },

    props: {
        matches: Array,
        tables: Array,
        results: Array
    },

    components: {
        Match
    },

    remember: ['form'],

    data()
    {
        return {
            form: {
                running: false
            },
            dialogVisible: false,
            match: {},
            showing: 20
        }
    },

    computed: {
        showedMatches()
        {
            return this.matches.slice(0, this.showing)
        },

        running()
        {
            const matches = this.showedMatches.filter(m => m.isStarted)

            return sort(matches).by([
                {asc: m => m.phase},
                {asc: m => m.order}
            ])
        },

        selectedMatches()
        {
            return this.form.running ? this.running : this.showedMatches
        },

        championshipResults()
        {
            return this.results.filter(r => r.size == this.match.setsCount)
        },
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
            if (row.isStarted)
            {
                return 'bg-blue-100'
            }
        },

        show(match)
        {
            this.match = JSON.parse(JSON.stringify(match))
            this.dialogTitle = 'Spiel ' + match.id + ' - ' + match.championship
            this.dialogVisible = true
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
    }
}
</script>
