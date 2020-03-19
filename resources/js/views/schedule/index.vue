<template>
<div>
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

        <el-dialog
            @close="resetDialog"
            :title="dialogTitle"
            :visible.sync="dialogVisible"
            width="50%"
            append-to-body
            :modal="true">

            <div class="flex mb-6">
                <span class="mr-3 font-bold">{{ match.p1 }}</span>
                <span class="mr-3">-</span>
                <span class="font-bold">{{ match.p2 }}</span>
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

            <div v-else class="flex items-center mb-4">
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

            <div slot="footer" class="dialog-footer">
                <el-button type="text" @click="dialogVisible = false">Abbrechen</el-button>

                <el-button v-if="match.isStarted" type="primary" @click="stopMatch()">Spiel beenden</el-button>

                <el-button v-else type="success" @click="startMatch()">
                    <icon class="mr-1" icon="play" fixed-width></icon>
                    Spiel starten
                </el-button>
            </div>
        </el-dialog>
    </box>
</div>
</template>

<script>
import sort from 'fast-sort'
import admin from '@/views/layouts/admin'

export default {
    layout: (h, page) => h(admin, {props: {title: 'Übersicht'}} ,  [page]),

    props: {
        matches: Array,
        tables: Array,
    },

    remember: 'form',

    data()
    {
        return {
            form: {
                running: false
            },
            dialogVisible: false,
            dialogTitle: '',
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

        orderedTables()
        {
            return sort(this.tables).asc(t => t.name)
        },

        freeTables()
        {
            return this.orderedTables.filter(t => t.busy == false)
        },
    },

    methods: {
        sort: sort,

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
    }
}
</script>
