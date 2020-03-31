<template>
<div>
    <div class="flex items-center mb-8 pt-8">
        <label class="label w-1/5 text-right mr-8">Spielklasse <span class="text-red-600">*</span></label>
        <el-input clearable @input="clear('name')" class="w-1/3" v-model="form.name"></el-input>
        <span v-if="has('name')" class="ml-3 text-red-600 text-xs">{{ get('name') }}</span>
    </div>

    <div class="flex items-center mb-6">
        <label class="label w-1/5 text-right mr-8">Spielsystem <span class="text-red-600">*</span></label>
        <el-radio-group class="pr-2" v-model="form.system_id" @change="clear('system_id')">
            <el-radio-button
                v-for="system in systems"
                :label="system.id"
                :key="system.id">

                {{ system.name }}
            </el-radio-button>
        </el-radio-group>
        <span v-if="has('system_id')" class="ml-3 text-red-600 text-xs">{{ get('system_id') }}</span>
    </div>

    <div class="flex items-center mb-6">
        <div class="w-1/5 mr-8"></div>

        <el-checkbox v-model="form.handicap" label="Mit Vorgabe" class="mr-8"></el-checkbox>

        <transition-group name="fade" >
            <template v-if="form.handicap">
                <el-checkbox key="0" v-model="form.reverted_handicap" label="Vorgabe umkehren">
                </el-checkbox>

                <el-tooltip placement="bottom" key="1">
                    <div slot="content">Der Stärkere erhält Vorgabe.</div>
                    <icon icon="question-circle" class="ml-1 mr-8 text-sm text-gray-600"></icon>
                </el-tooltip>
            </template>
        </transition-group>

        <transition name="fade">
            <el-button v-if="form.handicap" class="hover:underline m-0 p-0 border-0" type="text" @click="dialogVisible = true">
                <icon icon="calculator" fixed-width></icon>
                <span>Vorgabepunkte</span>
            </el-button>
        </transition>
    </div>

    <div class="flex items-center mb-8" v-if="enableThirdPlace">
        <div class="w-1/5 mr-8"></div>

        <el-checkbox v-model="form.third_place" label="Spiel um Platz 3"></el-checkbox>
    </div>

    <div class="flex items-center mb-8">
        <label class="label w-1/5 text-right mr-8">Typ <span class="text-red-600">*</span></label>
        <el-radio-group class="pr-2" v-model="form.type_id" @change="clear('type_id')">
            <el-radio-button
                v-for="item in types"
                :label="item.id"
                :key="item.id">

                {{ item.name }}
            </el-radio-button>
        </el-radio-group>
        <span v-if="has('type_id')" class="ml-3 text-red-600 text-xs">{{ get('type_id') }}</span>
    </div>

    <div class="flex items-center mb-8">
        <label class="label w-1/5 text-right mr-8">Gewinnsätze <span class="text-red-600">*</span></label>
        <el-radio-group class="pr-2" v-model="form.sets" @change="clear('sets')">
            <el-radio-button v-for="set in form.setsList" :label="set.id" :key="set.id">{{ set.label }}</el-radio-button>
        </el-radio-group>
        <span v-if="has('sets')" class="ml-3 text-red-600 text-xs">{{ get('sets') }}</span>
    </div>

    <div class="flex">
        <div class="w-4/5 pl-8 ml-auto">
            <el-button @click="$inertia.visit(route('championships.index', $page.t.id))">Abbrechen</el-button>
            <el-button type="primary" @click="save">Speichern</el-button>
        </div>
    </div>

    <el-dialog
        title="Vorgabepunkte"
        :visible.sync="dialogVisible"
        width="30%"
        append-to-body
        :modal="true">

        <el-table :data="form.handicaps">
            <el-table-column
                prop="difference"
                label="Max. Differenz">

                <el-input slot-scope="scope" v-model="scope.row.difference" placeholder="Differenz"></el-input>
            </el-table-column>

            <el-table-column
                prop="handicap"
                label="Vorgabe">

                <el-input slot-scope="scope" v-model="scope.row.handicap" placeholder="Vorgabe"></el-input>
            </el-table-column>

            <el-table-column
                width="60">
                <el-button slot-scope="scope" type="primary" size="mini" @click="removeHandicap(scope.$index)">
                    -
                </el-button>
            </el-table-column>
        </el-table>

        <el-button class="mt-4" type="text" @click="addHandicap()">
            <icon icon="plus" fixed-width></icon>
            <span>Vorgabe hinzufügen</span>
        </el-button>

        <div slot="footer">
            <el-button type="primary" @click="dialogVisible = false">Schließen</el-button>
        </div>
    </el-dialog>
</div>
</template>

<script>
import admin from '@/views/layouts/admin'
import validation from '@/mixins/validation'
import championship from '@/views/layouts/championship'

export default {
    mixins: [
        validation
    ],

    layout: (h, page) => {
        return h(admin, {
            props: {
                title: page.data.props.mode == 'create' ? 'Spielklasse erstellen' : page.data.props.championship.name
            }
        } , [
            h(championship, {props: {championship: page.data.props.championship, width: 'narrow'}}, [page]),
        ])
    },

    props : {
        championship: Object,
        systems: Array,
        types: Array,
        mode: {
            type: String,
            default: 'create'
        }
    },

	data()
	{
		return {
            form: {...this.championship},
            dialogVisible: false
		}
    },

    watch: {
        enableThirdPlace(newValue)
        {
            if (! newValue)
            {
                this.form.third_place = false
            }
        }
    },

    computed: {
        isSaved()
        {
            return this.mode == 'edit'
        },

        enableThirdPlace()
        {
            const system = this.systems.find(s => s.id == this.form.system_id)

            return system ? system.name.indexOf('Doppel') < 0 : false
        }
    },

    methods: {
        removeHandicap(index)
        {
            this.form.handicaps.splice(index, 1)
        },

        addHandicap()
        {
            this.form.handicaps.push({difference: '', handicap: ''})
        },

        save()
        {
            if (this.isSaved)
            {
                if (this.championship.system_id != this.form.system_id ||
                    this.championship.third_place != this.form.third_place ||
                    this.championship.type_id != this.form.type_id ||
                    this.championship.sets != this.form.sets)
                {
                    this.$confirm('Diese Änderung setzt alle Phasen zurück. Fortfahren?', 'Achtung', {
                        confirmButtonText: 'OK',
                        cancelButtonText: 'Abbrechen',
                        type: 'warning'
                    })
                    .then(() => {
                        axios.put(route('championships.update', [this.$page.t.id, this.form.id]).url(), this.form).then(() =>
                        {
                            this.$inertia.reload()
                            this.$message.success('Änderungen gespeichert.')
                        })
                        .catch(error => this.errors = error.response.data.errors)
                    })
                    .catch(() => {})
                }
                else
                {
                    axios.put(route('championships.update', [this.$page.t.id, this.form.id]).url(), this.form).then(() =>
                        {
                            this.$inertia.reload()
                            this.$message.success('Änderungen gespeichert.')
                        })
                        .catch(error => this.errors = error.response.data.errors)
                }
            }
            else
            {
                axios.post(route('championships.store', [this.$page.t.id]).url(), this.form).then(response =>
                {
                    this.$inertia.visit(route('championships.edit', [this.$page.t.id, response.data.id]).url())
                })
                .catch(error => this.errors = error.response.data.errors)
            }
        }
    }
}
</script>
