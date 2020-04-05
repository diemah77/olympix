<template>
<div>
    <div class="flex items-center justify-between mb-6">
        <div class="">
            Spiele: <span class="font-bold">{{ selectedMatches.length }}</span>
        </div>

        <el-checkbox v-model="form.relevant" label="Nur relevante Spiele anzeigen"></el-checkbox>
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

    <el-dialog
        @close="resetDialog"
        :title="dialogTitle"
        :visible.sync="dialogVisible"
        width="50%"
        append-to-body
        :modal="true">

        <div class="flex items-center mb-6">
            <span class="mr-3 font-bold">{{ match.p1 }}</span>
            <span class="mr-3">-</span>
            <span class="font-bold">{{ match.p2 }}</span>
            <div v-if="match.handicap" class="font-bold ml-6 py-1 px-2 bg-orange-200 rounded">{{ match.handicap }}</div>
        </div>

        <div v-if="match.isStarted">
            <div class="flex items-center mb-4">
                <span class="w-1/5 mr-2">Ergebnis: </span>

                <div class="flex-1">
                    <el-select
                        v-model="match.result_id"
                        placeholder="Ergebnis"
                        clearable
                        @clear="handleResultClear(match)"
                        @change="handleResultUpdate(match)">

                        <el-option
                            v-for="item in results"
                            :key="item.id"
                            :label="item.label"
                            :value="item.id">
                        </el-option>
                    </el-select>

                    <span v-if="has('result_id')" class="ml-3 text-red-600 text-xs">{{ get('result_id') }}</span>
                </div>
            </div>

            <div class="flex items-center">
                <span class="w-1/5 mr-2">Sätze: </span>

                <div class="flex-1">
                    <el-input
                        class="w-16 mr-2"
                        v-for="set in match.sets"
                        v-model="set.points"
                        @change="clear('sets')"
                        :key="set.id">
                    </el-input>

                    <span v-if="has('sets')" class="ml-3 text-red-600 text-xs">{{ get('sets') }}</span>
                </div>
            </div>
        </div>

        <div v-else class="mb-4">
            <div class="flex items-center">
                <span class="w-1/5 mr-2">Tisch: </span>

                <div class="flex-1">
                    <el-select v-model="match.table_id" placeholder="Tisch wählen">
                        <el-option
                            v-for="item in freeTables"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </div>
            </div>
        </div>

        <div slot="footer" class="dialog-footer">
            <el-button type="text" @click="dialogVisible = false">Abbrechen</el-button>

            <el-button v-if="match.isStarted" type="primary" @click="stopMatch()">Spiel beenden</el-button>

            <el-button v-else type="success" @click="startMatch()">
                <icon class="mr-1" icon="play" fixed-width></icon>
                Spiel starten
            </el-button>
        </div>
    </el-dialog>
</div>
</template>

<script>
import sort from 'fast-sort'
import admin from '@/views/layouts/admin'
import validation from '@/mixins/validation'
import schedule from '@/views/layouts/scheduled-championships'

export default {
    mixins: [validation],

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

    remember: 'form',

	data()
	{
		return {
            form: {
                relevant: true
            },
            dialogVisible: false,
            dialogTitle: '',
            match: {
                isStarted: false,
                statusName: false,
                matchable: '',
                phase: '',
                p1: '',
                p2: '',
                result_id: '',
                table_id: '',
                sets: []
            },
		}
	},

    computed: {
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
            return this.form.relevant ? this.relevants : this.matches.slice(0,30)
        },

        orderedTables()
        {
            return sort(this.tables).asc(t => t.name)
        },

        freeTables()
        {
            return this.orderedTables.filter(t => t.busy == false)
        },

        setsCount()
        {
            return this.match.result_id ? this.results.find(r => r.id == this.match.result_id).setCount : this.championship.winningSets
        },
    },

    methods: {
        sort: sort,

        rowClassName({row, index})
        {
            if (row.isFinished)
            {
                return 'bg-blue-100'
            }
        },

        resetDialog()
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

        handleResultUpdate(match)
        {
            this.clear('result_id')
            this.updateSets(match)
        },

        handleResultClear(match)
        {
            this.clear('sets')
            match.sets.forEach(s => s.points = "")
        },

        updateSets(match)
        {
            let diff = this.setsCount - match.sets.length

            if (diff > 0)
            {
                for (let i = 1; i <= diff; i++)
                {
                    match.sets.push({ points: ''})
                }
            }
            else if (diff < 0)
            {
                for (let i = 1; i <= Math.abs(diff); i++)
                {
                    match.sets.pop()
                }
            }
        },

        startMatch()
        {
            axios.post(route('matches.start', [this.$page.t.id, this.championship.id, this.match.id]).url(), this.match).then(response =>
            {
                this.dialogVisible = false
                this.$inertia.reload()
            })
            .catch(error => console.log(error.response))
        },

        stopMatch()
        {
            axios.post(route('matches.stop', [this.$page.t.id, this.championship.id, this.match.id]).url(), this.match).then(response =>
            {
                this.dialogVisible = false
                this.$inertia.reload()
            })
            .catch(error => this.errors = error.response.data.errors)
        },

        show(match)
        {
            this.match = JSON.parse(JSON.stringify(match))
            this.dialogTitle = 'Spiel ' + match.id + ' - ' + this.championship.name
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
            return !this.relevants.some(m => m.id == match.id)
        }
    }
}
</script>
