<template>
    <el-tabs v-model="activePhase" type="border-card" class="mt-8">
        <el-tab-pane
            :key="phase.id"
            v-for="(phase, index) in orderedPhases"
            class="mt-2"
            :label="'Phase ' + (index+1) + ': ' + phase.name"
            :name="'phase' + (index+1)"
            :disabled="!phase.canBeDrawn">

            <div v-if="phase.type == 'group'" class="pb-6 border-b border-gray-200 mb-6">
                <div class="flex">
                    <div class="flex flex-col mr-8">
                        <label class="label block text-sm mb-2">
                            Gruppenstärke <span class="text-red-600">*</span>
                        </label>

                        <el-input-number
                            v-model="phase.groupPhase.group_size"
                            controls-position="right"
                            @change="phase.groupPhase.qualified_id = ''"
                            @input="clear('group_size')"
                            :min="0"
                            :max="participants_count">
                        </el-input-number>

                        <span key="1" v-if="groupCount(phase) > 0" class="text-gray-600 text-sm pt-2">
                            <i class="fa fa-fw fa-long-arrow-right"></i>
                            {{ groupCount(phase) }} Gruppe(n)
                        </span>

                        <span key="2" v-if="has('group_size')" class="ml-1 text-red-600 text-xs pt-2">{{ get('group_size') }}</span>
                    </div>

                    <div class="flex flex-col mr-8">
                        <label class="label block text-sm mb-2">Qualifiziert für KO-Runde <span class="text-red-600">*</span></label>
                        <el-select
                            :disabled="qualified(phase).length == 0"
                            v-model="phase.groupPhase.qualified_id"
                            @change="phase.groupPhase.extra_qualified_id = ''"
                            @input="clear('qualified_id')"
                            placeholder="auswählen"
                            clearable>

                            <el-option
                                v-for="q in qualified(phase)"
                                :label="q.name"
                                :value="q.id"
                                :key="q.id">
                            </el-option>
                        </el-select>

                        <span key="1" v-if="qualifiedCount(phase) > 0" class="text-silver-light text-sm pt-2">
                            <i class="fa fa-fw fa-long-arrow-right"></i>
                            {{ qualifiedCount(phase) }} Teilnehmer qualifiziert
                        </span>

                        <span key="2" v-if="has('qualified_id')" class="ml-1 pt-2 text-red-600 text-xs">{{ get('qualified_id') }}</span>
                    </div>

                    <div class="">
                        <label class="label block text-sm mb-2">Zusätzlich qualifiziert</label>
                        <el-select
                            :disabled="qualifiedExtra(phase).length == 0"
                            v-model="phase.groupPhase.extra_qualified_id"
                            placeholder="auswählen"
                            clearable>

                            <el-option
                                v-for="q in qualifiedExtra(phase)"
                                :label="q.name"
                                :value="q.id"
                                :key="q.id">
                            </el-option>
                            </el-select>
                    </div>
                </div>
            </div>

            <div class="flex items-center mb-8">
                <label class="label mr-4">Losmethode <span class="text-red-600">*</span></label>
                <el-radio-group class="pr-2" v-model="phase.draw_type">
                    <el-radio-button v-for="type in phase.drawTypeList" :label="type.id" :key="type.id">{{ type.label }}</el-radio-button>
                </el-radio-group>

                <el-button
                    :disabled="participants_count == 0"
                    :loading="drawLoading"
                    class="ml-auto"
                    type="primary"
                    @click="generateDraw(phase, index)">

                    <icon class="mr-1" icon="random" fixed-width></icon>
                    Auslosen
                </el-button>

                <el-button
                    :disabled="!phase.isDrawn"
                    type="danger"
                    @click="resetDraw(phase, index)">

                    <icon class="mr-1" icon="undo" fixed-width></icon>
                    Reset
                </el-button>

                <el-button
                    :disabled="! phase.canBeStarted || phase.isStarted"
                    type="success"
                    @click="start(phase, index)">

                    <icon class="mr-1" icon="play" fixed-width></icon>
                    Starten
                </el-button>
            </div>

            <transition name="fade">
                <group-standings
                    :groups="phase.groupPhase.groups"
                    v-if="showGroups(phase)">
                </group-standings>

                <brackets
                    class="bg-gray-100"
                    :rounds="phase.koPhase.rounds"
                    v-if="showBrackets(phase)">
                </brackets>
            </transition>
        </el-tab-pane>
    </el-tabs>
</template>

<script>
import sort from 'fast-sort'
import validation from '@/mixins/validation'
import admin from '@/views/layouts/admin'
import GroupStandings from '@/components/groupStandings'
import Brackets from '@/components/brackets'

export default {
    components: {
        Layout,
        GroupStandings,
        Brackets
    },

    mixins: [validation],

    props : {
        championship: Object,
        initialPhases: Array,
        participants_count: Number,
    },

	data()
	{
		return {
            phases: this.initialPhases,
            activePhase: 'phase1',
            drawLoading: false,
            resetLoading: false,
		}
    },

    computed: {
        orderedPhases()
		{
			return sort(this.phases).asc(p => p.order)
		},
    },

    methods: {
        sort: sort,

        save()
        {
            if (this.mode == 'edit')
            {
                axios.put(route('championships.update', [this.$page.t.id, this.form.id]), this.form).then(() =>
                {
                    this.$message.success('Spielklasse aktualisiert.')
                })
                .catch(error => this.errors = error.response.data.errors)
            }
            else
            {
                axios.post(route('championships.store', [this.$page.t.id]), this.form).then(response =>
                {
                    this.$inertia.visit(route('championships.edit', [this.$page.t.id, response.data.id]))
                })
                .catch(error => this.errors = error.response.data.errors)
            }
        },

        showGroups(phase)
		{
			return (phase.type == 'group' && phase.groupPhase.groups.length)
        },

        showBrackets(phase)
		{
			return (phase.type == 'ko' && phase.koPhase.rounds_count > 0)
        },

        groupCount(phase)
		{
			return Math.max(parseInt(this.participants_count / phase.groupPhase.group_size), 1)
        },

        qualified(phase)
		{
			return phase.groupPhase.qualifieds.filter(q => q.extra == false && q.value <= phase.groupPhase.group_size)
        },

        qualifiedCount(phase)
		{
			return Math.min(this.groupCount(phase) * this.getQualifiedsValue(phase, phase.groupPhase.qualified_id) + this.getQualifiedsValue(phase, phase.groupPhase.extra_qualified_id),
				this.participants_count)
        },

        getQualifiedsValue(phase, id)
		{
			return id ? phase.groupPhase.qualifieds.find(q => q.id == id).value : 0
        },

        qualifiedExtra(phase)
		{
			return phase.groupPhase.qualifieds.filter(q => q.extra == true && q.value <= (phase.groupPhase.group_size - this.getQualifiedsValue(phase, phase.groupPhase.qualified_id)))
        },

        generateDraw(phase, index)
		{
            this.drawLoading = true

            axios.post(route('draws.store', [this.$page.t.id, this.championship.id, phase.id]), phase).then(response =>
            {
                this.$set(this.phases, index, response.data)
                this.drawLoading = false

                this.$nextTick(() => this.$message.success('Phase erfolgreich ausgelost!'))
            })
		},

		resetDraw(phase, index)
		{
            this.resetLoading = true

            axios.delete(route('draws.destroy', [this.$page.t.id, this.championship.id, phase.id])).then(response =>
            {
                this.$set(this.phases, index, response.data)
                this.resetLoading = false

                this.$nextTick(() => this.$message.success('Phase zurückgesetzt!'))
            })
        },

        start(phase, index)
		{
            axios.post(route('phases.start', [this.$page.t.id, this.championship.id, phase.id])).then(response =>
            {
                phase.isStarted = true
                // this.$set(this.phases, index, response.data)
                this.$message.success('Phase gestartet!')
            })
		},
    }
}
</script>
