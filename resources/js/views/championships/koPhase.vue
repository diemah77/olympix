<template>
<div>
    <el-alert
        v-if="!phase.canBeDrawn"
        class="mb-6"
        type="warning"
        effect="dark"
        show-icon>

        Die KO-Phase kann erst gelost werden, wenn die Gruppenphase beendet ist!
    </el-alert>

    <div class="flex items-center">
        <label class="label mr-4">Losmethode <span class="text-red-600">*</span></label>
        <el-radio-group class="pr-2" v-model="form.draw_type" :disabled="!phase.canBeDrawn">
            <el-radio-button v-for="type in form.drawTypeList" :label="type.id" :key="type.id">{{ type.label }}</el-radio-button>
        </el-radio-group>

        <el-button
            :disabled="!form.canBeDrawn || participants_count == 0"
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
        <brackets
            class="mt-8"
            :rounds="phase.rounds"
            :is-draw="true"
            v-if="showBrackets">
        </brackets>
    </transition>
</div>
</template>

<script>
import admin from '@/views/layouts/admin'
import validation from '@/mixins/validation'
import championship from '@/views/layouts/championship'
import Brackets from '@/components/brackets'

export default {
     props : {
        championship: Object,
        phase: Object,
        participants_count: Number,
    },

    components: {
        Brackets
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
        showBrackets()
		{
			return this.form.rounds_count > 0
        }
    },

    methods: {
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
