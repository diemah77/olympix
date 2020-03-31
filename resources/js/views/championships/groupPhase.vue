<template>
<div>
    <div class="pb-6 border-b border-gray-200 mb-6">
        <div class="flex">
            <div class="flex flex-col mr-8">
                <label class="label block text-sm mb-2">
                    Gruppenstärke <span class="text-red-600">*</span>
                </label>

                <el-input-number
                    v-model="form.group_size"
                    controls-position="right"
                    @change="form.qualified_id = ''"
                    @input="clear('group_size')"
                    :min="2"
                    :max="participants_count">
                </el-input-number>

                <span key="1" v-if="groupCount > 0" class="text-gray-600 text-sm pt-2">
                    <icon icon="long-arrow-alt-right" fixed-width></icon>
                    {{ groupCount }} Gruppe(n)
                </span>

                <span key="2" v-if="has('group_size')" class="ml-1 text-red-600 text-xs pt-2">{{ get('group_size') }}</span>
            </div>

            <div class="flex flex-col mr-8">
                <label class="label block text-sm mb-2">Qualifiziert für KO-Runde <span class="text-red-600">*</span></label>
                <el-select
                    :disabled="qualified.length == 0"
                    v-model="form.qualified_id"
                    @change="form.extra_qualified_id = ''"
                    @input="clear('qualified_id')"
                    placeholder="auswählen"
                    clearable>

                    <el-option
                        v-for="q in qualified"
                        :label="q.name"
                        :value="q.id"
                        :key="q.id">
                    </el-option>
                </el-select>

                <span key="1" v-if="qualifiedCount > 0" class="text-gray-600 text-sm pt-2">
                    <icon icon="long-arrow-alt-right" fixed-width></icon>
                    {{ qualifiedCount }} Teilnehmer qualifiziert
                </span>

                <span key="2" v-if="has('qualified_id')" class="ml-1 pt-2 text-red-600 text-xs">{{ get('qualified_id') }}</span>
            </div>

            <div class="">
                <label class="label block text-sm mb-2">Zusätzlich qualifiziert</label>
                <el-select
                    :disabled="qualifiedExtra.length == 0"
                    v-model="form.extra_qualified_id"
                    placeholder="auswählen"
                    clearable>

                    <el-option
                        v-for="q in qualifiedExtra"
                        :label="q.name"
                        :value="q.id"
                        :key="q.id">
                    </el-option>
                </el-select>
            </div>
        </div>
    </div>

    <div class="flex items-center">
        <label class="label mr-4">Losmethode <span class="text-red-600">*</span></label>
        <el-radio-group class="pr-2" v-model="phase.draw_type">
            <el-radio-button v-for="type in phase.drawTypeList" :label="type.id" :key="type.id">{{ type.label }}</el-radio-button>
        </el-radio-group>

        <el-button
            :disabled="participants_count == 0"
            :loading="drawLoading"
            class="ml-auto"
            type="primary"
            @click="generateDraw()">

            <icon class="mr-1" icon="random" fixed-width></icon>
            Auslosen
        </el-button>

        <el-button
            :disabled="!phase.isDrawn"
            type="danger"
            @click="resetDraw()">

            <icon class="mr-1" icon="undo" fixed-width></icon>
            Reset
        </el-button>

        <el-button
            :disabled="! phase.canBeStarted || phase.isStarted"
            type="success"
            @click="start()">

            <icon class="mr-1" icon="play" fixed-width></icon>
            Starten
        </el-button>
    </div>

    <transition name="fade">
        <groups
            class="mt-8"
            :groups="form.groups"
            v-if="showGroups">
        </groups>
    </transition>
</div>
</template>

<script>
import admin from '@/views/layouts/admin'
import validation from '@/mixins/validation'
import championship from '@/views/layouts/championship'
// import GroupStandings from '@/components/groupStandings'
import Groups from '@/components/groups'

export default {
     props : {
        championship: Object,
        phase: Object,
        participants_count: Number,
    },

    components: {
        Groups,
    },

    mixins: [validation],

    layout: (h, page) => {
        return h(admin, {} , [
            h(championship, {
                props: {
                    championship: page.data.props.championship
                }
            }, [page]),
        ])
    },

    watch: {
        phase()
        {
            this.form = this.phase
        }
    },

	data()
	{
		return {
            form: this.phase,
            drawLoading: false,
            resetLoading: false,
		}
    },

    computed: {
        showGroups()
		{
			return this.form.groups.length > 0
        },

        groupCount()
		{
			return Math.max(parseInt(this.participants_count / this.form.group_size), 1)
        },

        qualified()
		{
			return this.phase.qualifieds.filter(q => q.extra == false && q.value <= this.form.group_size)
        },

        qualifiedCount()
		{
			return Math.min(this.groupCount * this.getQualifiedsValue(this.form.qualified_id) + this.getQualifiedsValue(this.form.extra_qualified_id),
				this.participants_count)
        },

        qualifiedExtra()
		{
			return this.phase.qualifieds.filter(q => q.extra == true && q.value <= (this.form.group_size - this.getQualifiedsValue(this.form.qualified_id)))
        },
    },

    methods: {
        getQualifiedsValue(id)
		{
			return id ? this.form.qualifieds.find(q => q.id == id).value : 0
        },

        generateDraw()
		{
            if (this.form.isDrawn)
            {
                this.$confirm('Soll die Phase neu gelost werden?', 'Achtung', {
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Abbrechen',
                    type: 'warning'
                })
                .then(() => {
                    this.drawLoading = true

                    axios.post(route('draws.store', [this.$page.t.id, this.championship.id, this.form.id]).url(), this.form).then(response =>
                    {
                        this.$inertia.reload()
                        this.drawLoading = false
                        this.$message.success('Phase erfolgreich ausgelost!')
                    })
                    .catch(error =>
                    {
                        this.drawLoading = false
                        this.errors = error.response.data.errors
                    })
                })
                .catch(() => {})
            }
            else
            {
                this.drawLoading = true

                axios.post(route('draws.store', [this.$page.t.id, this.championship.id, this.form.id]).url(), this.form).then(response =>
                {
                    this.$inertia.reload()
                    this.drawLoading = false
                    this.$message.success('Phase erfolgreich ausgelost!')
                })
                .catch(error =>
                {
                    this.drawLoading = false
                    this.errors = error.response.data.errors
                })
            }
		},

		resetDraw()
		{
            this.$confirm('Soll die Auslosung zurückgesetzt werden?', 'Achtung', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Abbrechen',
                type: 'warning'
            })
            .then(() => {
                this.resetLoading = true

                axios.delete(route('draws.destroy', [this.$page.t.id, this.championship.id, this.form.id]).url()).then(response =>
                {
                    this.resetLoading = false
                    this.$inertia.reload()
                    this.$message.success('Phase zurückgesetzt!')
                })
            })
            .catch(() => {})
        },

        start()
		{
            axios.post(route('phases.start', [this.$page.t.id, this.championship.id, this.form.id]).url()).then(response =>
            {
                this.$inertia.reload()
                this.$message.success('Phase gestartet!')
            })
		}
    }
}
</script>
