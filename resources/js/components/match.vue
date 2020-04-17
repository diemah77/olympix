<template>
<el-dialog
    @close="$emit('update:visible', false)"
    :visible="visible"
    width="50%"
    append-to-body
    :modal="true">

    <div slot="title" class="flex items-center">
        <h1 class="text-lg mr-4">{{ title }}</h1>
        <span class="px-3 py-1 text-xs rounded-full" :class="badgeClass">{{ localMatch.statusName }}</span>
    </div>

    <div>
        <div class="flex items-center mb-6">
            <span class="mr-3 font-bold">{{ localMatch.p1 }}</span>
            <span class="mr-3">-</span>
            <span class="font-bold">{{ localMatch.p2 }}</span>
            <div v-if="localMatch.handicap" class="font-bold ml-6 py-1 px-2 bg-orange-200 rounded">{{ localMatch.handicap }}</div>
        </div>

        <el-tabs v-model="activeTab" type="card">
            <el-tab-pane class="px-3" name="start">
                <span slot="label">
                    <icon class="mr-1 text-green-500" icon="play" fixed-width />
                    Spiel starten
                </span>

                <div class="flex items-center">
                    <span class="w-20 mr-2">Tisch: </span>

                    <div class="flex items-center">
                        <el-select v-model="localMatch.table_id" placeholder="Tisch wählen" clearable>
                            <el-option
                                v-for="item in freeTables"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                            </el-option>
                        </el-select>

                        <icon
                            class="ml-2 text-green-600 hover:text-green-400 cursor-pointer"
                            icon="check-circle"
                            fixed-width
                            title="Tisch automatisch zuweisen"
                            @click="assignTable()"
                        />
                    </div>

                    <span v-if="has('table_id')" class="ml-3 text-red-600 text-xs">{{ get('table_id') }}</span>
                </div>
            </el-tab-pane>

            <el-tab-pane class="px-3" name="stop">
                <span slot="label">
                    <icon class="mr-1 text-green-500" icon="check" fixed-width />
                    Spiel beenden
                </span>

                <div>
                    <div class="flex items-center mb-4">
                        <span class="w-20 mr-2">Ergebnis: </span>

                        <div class="flex-1">
                            <el-select
                                v-model="localMatch.result_id"
                                placeholder="Ergebnis"
                                clearable
                                @clear="handleResultClear()"
                                @change="handleResultUpdate()">

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
                        <span class="w-20 mr-2">Sätze: </span>

                        <div class="flex-1">
                            <el-input
                                class="w-16 mr-2"
                                v-for="set in localMatch.sets"
                                v-model="set.points"
                                @change="clear('sets')"
                                :key="set.id">
                            </el-input>

                            <span v-if="has('sets')" class="ml-3 text-red-600 text-xs">{{ get('sets') }}</span>
                        </div>
                    </div>
                </div>
            </el-tab-pane>
        </el-tabs>
    </div>

    <div slot="footer" class="flex">
        <el-button v-if="false" type="info" plain @click="reset()">
            <icon icon="undo" fixed-width class="mr-1" />
            <span>Spiel zurücksetzen</span>
        </el-button>

        <div class="ml-auto">
            <el-button plain @click="$emit('update:visible', false)">Abbrechen</el-button>

            <el-button v-if="activeTab === 'stop'" type="primary" @click="stopMatch()">Spiel beenden</el-button>
            <el-button
                v-else
                :disabled="localMatch.statusName != 'Vorbereitet'"
                type="primary"
                @click="startMatch()">

                <!-- <icon class="mr-1" icon="play" fixed-width></icon> -->
                Spiel starten
            </el-button>
        </div>
    </div>
</el-dialog>
</template>

<script>
import sort from 'fast-sort'
import validation from '@/mixins/validation'

export default {
    mixins: [validation],

    props: {
        visible: {
            type: Boolean,
            required: true
        },

        match: {
            type: Object,
            required: true
        },

        tables: {
            type: Array,
            required: true
        },

        results: {
            type: Array,
            required: true
        }
    },

    watch: {
        match(newVal)
        {
            this.localMatch = newVal
            this.activeTab = newVal.isStarted ? 'stop' : 'start'
        }
    },

    data()
    {
        return {
            localMatch: this.match,
            activeTab: 'start'
        }
    },

    computed: {
        badgeClass()
        {
            if (this.localMatch.statusName == 'Vorbereitet')
            {
                return 'bg-gray-200 text-gray-800'
            }

            return this.localMatch.statusName == 'Gestartet' ? 'bg-green-500 text-white' : 'bg-orange-500 text-white'
        },

        title()
        {
            return this.match.championship + ' - ' + this.match.matchable
        },

        setsCount()
        {
            return this.localMatch.result_id ? this.results.find(r => r.id == this.localMatch.result_id).setCount : this.localMatch.winningSets
        },

        orderedTables()
        {
            return sort(this.tables).asc(t => t.name)
        },

        freeTables()
        {
            return this.orderedTables.filter(t => t.busy == false || t.id == this.localMatch.table_id)
        },
    },

    methods: {
        sort: sort,

        handleResultUpdate()
        {
            this.clear('result_id')
            this.updateSets()
        },

        handleResultClear()
        {
            this.clear('sets')
            this.localMatch.sets.forEach(s => s.points = "")
        },

        updateSets()
        {
            let diff = this.setsCount - this.localMatch.sets.length

            if (diff > 0)
            {
                for (let i = 1; i <= diff; i++)
                {
                    this.localMatch.sets.push({ points: ''})
                }
            }
            else if (diff < 0)
            {
                for (let i = 1; i <= Math.abs(diff); i++)
                {
                    this.localMatch.sets.pop()
                }
            }
        },

        assignTable()
        {
            this.localMatch.table_id = this.freeTables[0].id
            this.$forceUpdate()
        },

        startMatch()
        {
            axios.post(route('matches.start', [this.$page.t.id, this.localMatch.championship_id, this.localMatch.id]).url(), this.localMatch).then(response =>
            {
               this.$emit('started')
            })
            .catch(error => this.errors = error.response.data.errors)
        },

        stopMatch()
        {
            axios.post(route('matches.stop', [this.$page.t.id, this.localMatch.championship_id, this.localMatch.id]).url(), this.localMatch).then(response =>
            {
                this.$emit('stopped')
            })
            .catch(error => this.errors = error.response.data.errors)
        },

        reset()
        {
            axios.post(route('matches.reset', [this.$page.t.id, this.localMatch.championship_id, this.localMatch.id]).url()).then(response =>
            {
                this.$emit('reset')
            })
        }
    }
}
</script>
